<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Mail\BookingConfirmation;
use App\Mail\NewBookingAdminNotification;
use App\Models\Booking;
use App\Models\Car;
use App\Models\Employee;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\NewBookingNotification;
use App\Services\HyperPayService;
use App\Services\MailConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $car = null;
        if ($request->filled('car_id')) {
            $car = Car::with('brand')->findOrFail($request->car_id);
        }
        $cars = Car::with('brand')
            ->where('is_active', true)
            ->where('is_highlighted', '!=', 'coming_soon')
            ->orderBy('name')
            ->get();

        if ($car && $car->is_highlighted === 'coming_soon') {
            $cars->prepend($car);
        }

        // جلب إعدادات HyperPay
        $hyperPaySettings = Setting::whereIn('key', [
            'hyperpay_enabled', 'hyperpay_booking_fee', 'hyperpay_currency', 'hyperpay_mode',
        ])->pluck('value', 'key');

        $hyperPayEnabled = ($hyperPaySettings['hyperpay_enabled'] ?? '0') == '1';
        $bookingFee = (float) ($hyperPaySettings['hyperpay_booking_fee'] ?? 500);
        $currency = $hyperPaySettings['hyperpay_currency'] ?? 'SAR';

        return view('store.booking.create', compact(
            'car', 'cars', 'hyperPayEnabled', 'bookingFee', 'currency'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'down_payment' => 'required|integer|min:0',
            'duration_years' => 'required|integer|min:1|max:10',
            'interest_rate' => 'nullable|numeric|min:0|max:50',
            'notes' => 'nullable|string|max:1000',
            'booking_type' => 'nullable|string|in:test_drive,purchase,inquiry',
            'location' => 'nullable|string|max:500',
            // خيار الدفع (اختياري)
            'with_payment' => 'nullable|in:1,0',
            'g-recaptcha-response' => [new \App\Rules\Recaptcha],
        ]);

        // احسب القسط والإجمالي
        $car = Car::findOrFail($data['car_id']);
        $interestRate = isset($data['interest_rate']) && $data['interest_rate'] > 0
                            ? (float) $data['interest_rate']
                            : 4.0;
        $principal = max(0, $car->cash_price - $data['down_payment']);
        $totalMonths = $data['duration_years'] * 12;
        $monthlyRate = ($interestRate / 100) / 12;

        if ($monthlyRate > 0) {
            $monthly = $principal * ($monthlyRate * pow(1 + $monthlyRate, $totalMonths))
                       / (pow(1 + $monthlyRate, $totalMonths) - 1);
        } else {
            $monthly = $totalMonths > 0 ? $principal / $totalMonths : 0;
        }

        $data['monthly_installment'] = (int) round($monthly);
        $data['total_price'] = (int) round($monthly * $totalMonths + $data['down_payment']);
        $data['interest_rate'] = $interestRate;
        $data['status'] = 'new';
        $data['source'] = 'website';
        $data['locale'] = app()->getLocale();
        $data['payment_status'] = 'none'; // الافتراضي: لم يدفع

        // توليد idempotency key إذا أراد الدفع
        $withPayment = ($request->input('with_payment') == '1');
        if ($withPayment) {
            $data['payment_idempotency_key'] = app(HyperPayService::class)->generateIdempotencyKey();
            $data['payment_status'] = 'pending';
        }

        // =====================================================
        // ربط الحساب: للمسجلين تلقائياً وللجدد يتم إنشاء حساب
        // =====================================================
        if (Auth::guard('web')->check()) {
            $data['user_id'] = Auth::guard('web')->id();
        } else {
            $cleanPhone = preg_replace('/[^\d+]/', '', $data['client_phone']);
            $email = ! empty($data['client_email']) ? trim($data['client_email']) : null;

            $user = null;
            if (! empty($cleanPhone)) {
                $user = User::where('phone', $cleanPhone)->first();
            }
            if (! $user && ! empty($email)) {
                $user = User::where('email', $email)->first();
            }

            if (! $user) {
                // مستخدم لأول مرة: إنشاء حساب جديد له تلقائياً
                $user = User::create([
                    'name' => $data['client_name'],
                    'phone' => $cleanPhone ?: $data['client_phone'],
                    'email' => $email,
                    'password' => Hash::make($cleanPhone ?: '12345678'),
                    'status' => 'active',
                ]);
            } else {
                // تحديث الهاتف أو البريد إن لم يكونا مسجلين
                if (empty($user->phone) && ! empty($cleanPhone)) {
                    $user->update(['phone' => $cleanPhone]);
                }
            }

            if ($user) {
                $data['user_id'] = $user->id;
                // تسجيل دخول العميل تلقائياً
                Auth::guard('web')->login($user);
            }
        }

        $booking = Booking::create($data);
        $booking->load('car.brand');

        // Auto assign
        app(\App\Services\BookingAssignmentService::class)->autoAssign($booking);

        // إشعار الإدارة (Notification في النظام)
        $admins = Employee::where('role', 'admin')->orWhere('id', 1)->get();
        Notification::send($admins, new NewBookingNotification($booking));

        // =====================================================
        // إرسال الإيميلات (مع SMTP ديناميكي من الإعدادات)
        // =====================================================
        try {
            app(MailConfigService::class)->applyDynamicMailConfig();

            // إيميل تأكيد للعميل (إذا أدخل بريده)
            if (! empty($booking->client_email)) {
                Mail::to($booking->client_email)
                    ->locale($booking->locale ?? 'ar')
                    ->send(new BookingConfirmation($booking));
            }

            // إيميل إشعار للإدارة
            $adminEmail = Setting::where('key', 'contact_email')->value('value');
            if ($adminEmail) {
                $adminEmailStr = is_array($adminEmail) ? ($adminEmail['value'] ?? '') : $adminEmail;
                if (! empty($adminEmailStr)) {
                    Mail::to($adminEmailStr)->send(new NewBookingAdminNotification($booking));
                }
            }
        } catch (\Exception $e) {
            Log::error('Email sending failed for booking #'.$booking->id.': '.$e->getMessage());
        }

        // واتساب ترحيبي
        $settings = Setting::whereIn('key', ['whatsapp_template_new_lead'])->pluck('value', 'key');
        $template = $settings['whatsapp_template_new_lead'] ?? '';
        if (! empty($template) && ! empty($booking->client_phone)) {
            $message = str_replace(
                ['{customer_name}', '{car_name}', '{status}'],
                [$booking->client_name, $car->name, Booking::STATUSES[$data['status']]['label'] ?? 'جديد'],
                $template
            );
            app(\App\Services\TwilioWhatsAppService::class)->sendWhatsApp($booking->client_phone, $message);
        }

        // ===================================================
        // إذا اختار الدفع → توجيه لصفحة الدفع
        // إذا لم يختر → توجيه لصفحة النجاح مباشرة
        // ===================================================
        if ($withPayment) {
            return redirect()->route('store.booking.pay.form', $booking->id);
        }

        $whatsappText = urlencode(
            "طلب حجز جديد من موقع \n".
            "الاسم: {$data['client_name']}\n".
            "الهاتف: {$data['client_phone']}\n".
            "السيارة: {$car->name}\n".
            'المقدم: '.number_format($data['down_payment'])." ﷼\n".
            "المدة: {$data['duration_years']} سنة\n".
            'القسط: '.number_format($data['monthly_installment']).' ﷼ / شهر'
        );

        return redirect()->route('store.booking.success', $booking->id)
            ->with('whatsapp_text', $whatsappText);
    }

    /**
     * عرض صفحة إدخال بيانات البطاقة
     */
    public function payForm(Booking $booking)
    {
        // منع الوصول إذا كان الدفع مكتملاً أو الحجز لا ينتظر دفعاً
        if ($booking->payment_status === 'paid') {
            return redirect()->route('store.booking.success', $booking->id);
        }

        $bookingFee = Setting::where('key', 'hyperpay_booking_fee')->value('value') ?? 500;
        $currency = Setting::where('key', 'hyperpay_currency')->value('value') ?? 'SAR';

        return view('store.booking.pay', compact('booking', 'bookingFee', 'currency'));
    }

    /**
     * معالجة الدفع عبر HyperPay (Server-to-Server)
     */
    public function pay(Request $request, Booking $booking)
    {
        // ===================================================
        // الحماية من الدفع المزدوج (Double Submit)
        // ===================================================
        if ($booking->payment_status === 'paid') {
            return redirect()->route('store.booking.success', $booking->id)
                ->with('info', 'تم الدفع مسبقاً لهذا الحجز.');
        }

        // التحقق من الـ idempotency key (لمنع إعادة الإرسال)
        if (empty($booking->payment_idempotency_key)) {
            $booking->update(['payment_idempotency_key' => app(HyperPayService::class)->generateIdempotencyKey()]);
        }

        // Validate بيانات البطاقة
        $request->validate([
            'card_number' => 'required|string|min:13|max:19',
            'card_holder' => 'required|string|max:100',
            'card_expiry_month' => 'required|string|size:2',
            'card_expiry_year' => 'required|string|size:4',
            'card_cvv' => 'required|string|min:3|max:4',
            'card_brand' => 'required|in:VISA,MASTER,MADA',
        ]);

        $hyperPayService = app(HyperPayService::class);
        $bookingFee = Setting::where('key', 'hyperpay_booking_fee')->value('value') ?? 500;

        $paymentData = [
            'amount' => $bookingFee,
            'card_brand' => $request->card_brand,
            'card_number' => preg_replace('/\s+/', '', $request->card_number),
            'card_holder' => $request->card_holder,
            'card_expiry_month' => $request->card_expiry_month,
            'card_expiry_year' => $request->card_expiry_year,
            'card_cvv' => $request->card_cvv,
            'customer_email' => $booking->client_email ?? '',
            'customer_name' => $booking->client_name,
            'customer_phone' => $booking->client_phone,
        ];

        // ===================================================
        // إرسال طلب الدفع
        // ===================================================
        $result = $hyperPayService->initiatePayment($paymentData, $booking->payment_idempotency_key);

        // ===================================================
        // معالجة النتائج
        // ===================================================
        Log::info('HyperPay Payment Result for Booking #'.$booking->id, $result);

        switch ($result['status']) {

            case 'success':
                // ✅ دفع ناجح
                $booking->update([
                    'payment_status' => 'paid',
                    'payment_amount' => $bookingFee,
                    'payment_transaction_id' => $result['transaction_id'],
                    'payment_result_code' => $result['code'],
                    'payment_result_description' => $result['description'],
                    'payment_at' => now(),
                ]);

                $whatsappText = urlencode(
                    "✅ تم تأكيد حجزك بنجاح!\n".
                    "الاسم: {$booking->client_name}\n".
                    "رقم الحجز: #{$booking->id}\n".
                    'المبلغ المدفوع: '.number_format($bookingFee)." ﷼\n".
                    "رقم المعاملة: {$result['transaction_id']}"
                );

                return redirect()->route('store.booking.success', $booking->id)
                    ->with('payment_success', true)
                    ->with('whatsapp_text', $whatsappText);

            case 'pending':
                // ⏳ معلق - ينتظر Webhook
                $booking->update([
                    'payment_status' => 'pending',
                    'payment_amount' => $bookingFee,
                    'payment_transaction_id' => $result['transaction_id'],
                    'payment_result_code' => $result['code'],
                    'payment_result_description' => $result['description'],
                ]);

                return redirect()->route('store.booking.payment.pending', $booking->id);

            case 'failed':
            default:
                // ❌ فشل الدفع
                $booking->update([
                    'payment_status' => 'failed',
                    'payment_result_code' => $result['code'],
                    'payment_result_description' => $result['description'],
                ]);

                return redirect()->route('store.booking.payment.failed', $booking->id)
                    ->with('payment_error', $result['description'])
                    ->with('payment_code', $result['code']);
        }
    }

    /**
     * صفحة نجاح الحجز
     */
    public function success(Booking $booking)
    {
        return view('store.booking.success', compact('booking'));
    }

    /**
     * صفحة فشل الدفع
     */
    public function paymentFailed(Booking $booking)
    {
        return view('store.booking.payment-failed', compact('booking'));
    }

    /**
     * صفحة انتظار الدفع (Pending)
     */
    public function paymentPending(Booking $booking)
    {
        return view('store.booking.payment-pending', compact('booking'));
    }
}
