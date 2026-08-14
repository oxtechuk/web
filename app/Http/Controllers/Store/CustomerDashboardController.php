<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerDashboardController extends Controller
{
    public function orders()
    {
        $user = Auth::user();
        $bookings = $user->bookings()
            ->with('car.brand')
            ->latest()
            ->paginate(10);

        return view('store.account.orders', compact('user', 'bookings'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('store.account.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($user->id)],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => ['nullable', 'required_with:password', 'string'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => __('يرجى إدخال الاسم بالكامل'),
            'phone.required' => __('يرجى إدخال رقم الموبايل'),
            'phone.unique' => __('رقم الموبايل مستخدم من قبل حساب آخر'),
            'password.min' => __('كلمة السر يجب أن تكون 6 أحرف على الأقل'),
            'password.confirmed' => __('تأكيد كلمة السر غير مطابق'),
        ]);

        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => __('كلمة السر الحالية غير صحيحة')]);
            }
            $user->password = Hash::make($request->password);
        }

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->save();

        return back()->with('success', __('تم تحديث البيانات الشخصية بنجاح'));
    }

    public function favorites()
    {
        $user = Auth::user();
        $favoriteCars = $user->favoriteCars()->paginate(9);

        return view('store.account.favorites', compact('user', 'favoriteCars'));
    }

    public function toggleFavorite(Car $car)
    {
        $user = Auth::user();
        $user->favoriteCars()->toggle($car->id);

        return back()->with('success', __('تم تحديث قائمة المفضلة'));
    }

    /**
     * تحميل فاتورة الحجز PDF
     */
    public function downloadInvoice(Booking $booking)
    {
        // تأكد أن الحجز ينتمي للمستخدم الحالي
        if ($booking->user_id !== Auth::id()) {
            abort(403, __('غير مصرح لك بالوصول لهذه الفاتورة'));
        }

        $booking->load('car.brand');

        $pdf = null;
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('store.booking.invoice', compact('booking'));
        } elseif (class_exists(\Barryvdh\DomPDF\Facade\PDF::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\PDF::loadView('store.booking.invoice', compact('booking'));
        } elseif (app()->bound('dompdf.wrapper')) {
            $pdf = app('dompdf.wrapper')->loadView('store.booking.invoice', compact('booking'));
        }

        if (!$pdf) {
            abort(500, __('حزمة إنشاء PDF غير مثبتة على السيرفر'));
        }

        $pdf->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'defaultFont'          => 'DejaVu Sans',
                'chroot'               => public_path(),
            ]);

        $filename = 'invoice-' . str_pad($booking->id, 5, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($filename);
    }
}
