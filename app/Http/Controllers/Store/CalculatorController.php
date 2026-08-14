<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\CalculatorFactor;
use App\Models\CalculatorLead;
use App\Services\Cache\CalculatorCacheService;
use App\Services\TwilioOtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalculatorController extends Controller
{
    public function __construct(
        private readonly CalculatorCacheService $cache,
    ) {}

    public function __invoke(): View
    {
        $locale = app()->getLocale();

        $data = $this->cache->rememberCalculatorData();

        $banks = $data['banks'];
        $factors = $data['factors'];
        $cars = $data['cars'];

        $config = [
            'banks' => $banks->map(fn ($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'rate' => (float) $b->annual_rate,
            ])->values(),
            'genders' => $factors->get(CalculatorFactor::TYPE_GENDER, collect())->map(fn ($f) => [
                'id' => $f->id,
                'code' => $f->code,
                'label' => $f->labelForLocale($locale),
                'adj' => (float) $f->rate_adjustment,
            ])->values(),
            'ageBands' => $factors->get(CalculatorFactor::TYPE_AGE_BAND, collect())->map(fn ($f) => [
                'id' => $f->id,
                'code' => $f->code,
                'label' => $f->labelForLocale($locale),
                'min' => $f->min_age,
                'max' => $f->max_age,
                'adj' => (float) $f->rate_adjustment,
            ])->values(),
            'salaries' => $factors->get(CalculatorFactor::TYPE_SALARY_BAND, collect())->map(fn ($f) => [
                'id' => $f->id,
                'code' => $f->code,
                'label' => $f->labelForLocale($locale),
                'adj' => (float) $f->rate_adjustment,
            ])->values(),
            'employments' => $factors->get(CalculatorFactor::TYPE_EMPLOYMENT, collect())->map(fn ($f) => [
                'id' => $f->id,
                'code' => $f->code,
                'label' => $f->labelForLocale($locale),
                'adj' => (float) $f->rate_adjustment,
            ])->values(),
        ];

        return view('store.calculator', [
            'calculatorBanks' => $banks,
            'calculatorConfig' => $config,
            'cars' => $cars,
        ]);
    }

    public function sendOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        // Check if OTP is enabled in settings
        $otpEnabled = $this->isOtpEnabled();

        if (! $otpEnabled) {
            // OTP off → register lead immediately and unlock calculator
            $lead = CalculatorLead::create([
                'name'    => $validated['name'],
                'phone'   => $validated['phone'],
                'details' => [
                    'page'          => 'calculator_page',
                    'otp_bypassed'  => true,
                    'registered_at' => now()->toISOString(),
                ],
            ]);

            return response()->json([
                'success'     => true,
                'otp_skipped' => true,
                'message'     => __('تم التسجيل بنجاح. يمكنك الآن استخدام الحاسبة.'),
                'lead_id'     => $lead->id,
            ]);
        }

        // OTP is enabled — store name in session and send SMS
        session(['otp_pending_name' => $validated['name']]);

        try {
            $service = app(TwilioOtpService::class);
            $result  = $service->sendOtp($validated['phone']);
            return response()->json($result, $result['success'] ? 200 : 422);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('OTP send failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('فشل إرسال رمز التحقق، يرجى المحاولة لاحقاً.'),
            ], 422);
        }
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:20',
            'code'  => 'required|string|size:6',
        ]);

        try {
            $service = app(TwilioOtpService::class);
            $result  = $service->verifyOtp($validated['phone'], $validated['code']);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('OTP verify failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('حدث خطأ في التحقق، يرجى المحاولة لاحقاً.'),
            ], 422);
        }

        if (! $result['success']) {
            return response()->json($result, 422);
        }

        $name = session('otp_pending_name', '');
        session()->forget('otp_pending_name');

        $lead = CalculatorLead::create([
            'name'    => $name,
            'phone'   => $validated['phone'],
            'details' => [
                'page'            => 'calculator_page',
                'otp_verified_at' => now()->toISOString(),
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => __('تم التحقق بنجاح. يمكنك الآن استخدام الحاسبة.'),
            'lead_id' => $lead->id,
        ]);
    }

    /**
     * Check if OTP is enabled in settings.
     */
    private function isOtpEnabled(): bool
    {
        $setting = \App\Models\Setting::where('key', 'otp_enabled')->first();
        if ($setting === null) {
            return true; // default ON if not set
        }
        $v = $setting->value;
        return $v === true || $v === 1 || $v === '1' || $v === 'true';
    }


    public function saveLead(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'car_id' => 'nullable|exists:cars,id',
            'details' => 'nullable|array',
        ]);

        $lead = CalculatorLead::create($validated);

        return response()->json([
            'success' => true,
            'message' => __('تم تسجيل بياناتك بنجاح. يمكنك الآن استخدام الحاسبة.'),
            'lead_id' => $lead->id,
        ]);
    }
}
