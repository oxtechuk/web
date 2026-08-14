<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerAuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('store.account.orders');
        }

        return view('store.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
            'g-recaptcha-response' => [new \App\Rules\Recaptcha],
        ], [
            'phone.required' => __('يرجى إدخال البريد الإلكتروني أو رقم الموبايل'),
            'password.required' => __('يرجى إدخال كلمة السر'),
        ]);

        $inputValue = $request->phone;
        $isEmail = filter_var($inputValue, FILTER_VALIDATE_EMAIL);

        if ($isEmail) {
            $credentials = [
                'email' => $inputValue,
                'password' => $request->password,
            ];

            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();

                return redirect()->intended(route('store.account.orders'))
                    ->with('success', __('تم تسجيل الدخول بنجاح'));
            }
        } else {
            $phone = preg_replace('/[^\d+]/', '', $inputValue);

            if (Auth::attempt(['phone' => $inputValue, 'password' => $request->password], $request->boolean('remember')) ||
                Auth::attempt(['phone' => $phone, 'password' => $request->password], $request->boolean('remember'))) {
                $request->session()->regenerate();

                return redirect()->intended(route('store.account.orders'))
                    ->with('success', __('تم تسجيل الدخول بنجاح'));
            }
        }

        return back()->withErrors([
            'phone' => __('البريد الإلكتروني/رقم الموبايل أو كلمة السر غير صحيحة'),
        ])->onlyInput('phone');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('store.account.orders');
        }

        return view('store.auth.register');
    }

    public function register(Request $request)
    {
        $inputValue = $request->input('phone');
        $isEmail = filter_var($inputValue, FILTER_VALIDATE_EMAIL);

        if ($isEmail) {
            // Validate as email
            $request->merge(['email' => $inputValue]);

            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
                'g-recaptcha-response' => [new \App\Rules\Recaptcha],
            ], [
                'name.required' => __('يرجى إدخال الاسم بالكامل'),
                'email.required' => __('يرجى إدخال البريد الإلكتروني'),
                'email.unique' => __('البريد الإلكتروني مسجل بالفعل لدينا'),
                'password.required' => __('يرجى إدخال كلمة السر'),
                'password.min' => __('كلمة السر يجب أن لا تقل عن 6 أحرف'),
                'password.confirmed' => __('تأكيد كلمة السر غير مطابق'),
            ]);

            $phone = null;
            $email = $inputValue;
        } else {
            // Validate as phone
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
                'g-recaptcha-response' => [new \App\Rules\Recaptcha],
            ], [
                'name.required' => __('يرجى إدخال الاسم بالكامل'),
                'phone.required' => __('يرجى إدخال رقم الموبايل'),
                'phone.unique' => __('رقم الموبايل مسجل بالفعل لدينا'),
                'password.required' => __('يرجى إدخال كلمة السر'),
                'password.min' => __('كلمة السر يجب أن لا تقل عن 6 أحرف'),
                'password.confirmed' => __('تأكيد كلمة السر غير مطابق'),
            ]);

            $phone = preg_replace('/[^\d+]/', '', $inputValue);
            $email = null;
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $phone,
            'email' => $email,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        Auth::login($user);

        return redirect()->route('store.account.orders')
            ->with('success', __('تم إنشاء حسابك وتسجيل الدخول بنجاح'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('store.home')->with('success', __('تم تسجيل الخروج بنجاح'));
    }
}
