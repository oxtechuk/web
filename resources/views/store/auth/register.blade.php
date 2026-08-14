@extends('store.layouts.app')

@section('title', __('إنشاء حساب - GR Motors'))

@section('css')
<style>
.gr-auth-wrapper {
    background-color: #f4f5f7;
    min-height: 85vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 15px;
}

.gr-auth-card {
    display: flex;
    flex-direction: row-reverse; /* RTL layout: Banner on Right, Form on Left */
    width: 100%;
    max-width: 920px;
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

/* RTL Banner Side (Right) */
.gr-auth-banner {
    flex: 1;
    background: repeating-linear-gradient(
        -45deg,
        #1c1c1c,
        #1c1c1c 12px,
        #111111 12px,
        #111111 24px
    );
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px;
    text-align: center;
    min-height: 480px;
}

.gr-auth-banner-content {
    color: #ffffff;
}

.gr-auth-logo {
    max-height: 75px;
    margin-bottom: 20px;
    background: #fff;
    padding: 8px 12px;
    border-radius: 8px;
}

.gr-auth-brand-name {
    font-size: 1.6rem;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 8px;
    font-family: 'Cairo', 'Bahij-SemiBold', sans-serif;
}

.gr-auth-brand-sub {
    font-size: 1.1rem;
    font-weight: 600;
    color: #eab308;
    margin: 0;
}

/* Form Side (Left) */
.gr-auth-form-side {
    flex: 1;
    padding: 45px 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background: #ffffff;
}

.gr-auth-title {
    font-size: 2rem;
    font-weight: 800;
    color: #111827;
    text-align: center;
    margin-bottom: 30px;
    font-family: 'Cairo', 'Bahij-SemiBold', sans-serif;
}

.gr-form-group {
    margin-bottom: 20px;
    text-align: right;
}

.gr-form-group label {
    display: block;
    font-size: 0.9rem;
    font-weight: 700;
    color: #374151;
    margin-bottom: 8px;
    text-align: right;
}

.gr-form-control {
    width: 100%;
    height: 48px;
    padding: 10px 16px;
    font-size: 0.95rem;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    background-color: #ffffff;
    color: #1f2937;
    outline: none;
    transition: all 0.2s ease-in-out;
    box-sizing: border-box;
}

.gr-form-control:focus {
    border-color: #df7c7e;
    box-shadow: 0 0 0 3px rgba(223, 124, 126, 0.15);
}

.gr-btn-primary {
    width: 100%;
    height: 48px;
    background-color: #df7c7e;
    color: #ffffff;
    font-size: 1.05rem;
    font-weight: 700;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    margin-top: 10px;
    margin-bottom: 25px;
    transition: background-color 0.2s ease;
}

.gr-btn-primary:hover {
    background-color: #d16b6d;
}

/* Divider Line */
.gr-divider {
    position: relative;
    text-align: center;
    margin: 15px 0 25px 0;
}

.gr-divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background-color: #e5e7eb;
    z-index: 1;
}

.gr-divider span {
    position: relative;
    z-index: 2;
    background-color: #ffffff;
    padding: 4px 16px;
    font-size: 0.85rem;
    color: #9ca3af;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
}

/* Secondary Button */
.gr-secondary-action {
    text-align: center;
}

.gr-btn-outline-gold {
    display: inline-block;
    padding: 8px 32px;
    font-size: 0.95rem;
    font-weight: 700;
    color: #ca8a04;
    border: 2px solid #facc15;
    border-radius: 50px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.gr-btn-outline-gold:hover {
    background-color: #fefce8;
    color: #a16207;
}

/* Error messages */
.gr-error-text {
    color: #dc2626;
    font-size: 0.85rem;
    margin-top: 4px;
    text-align: right;
}

@media (max-width: 768px) {
    .gr-auth-card {
        flex-direction: column-reverse;
    }
    .gr-auth-banner {
        min-height: 200px;
        padding: 30px 20px;
    }
    .gr-auth-form-side {
        padding: 30px 20px;
    }
}
</style>
@endsection

@section('content')
<div class="gr-auth-wrapper">
    <div class="gr-auth-card">
        {{-- RTL Right: Striped Dark Banner --}}
        <div class="gr-auth-banner">
            <div class="gr-auth-banner-content">
                <a href="{{ route('store.home') }}">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="GR Motors" class="gr-auth-logo">
                </a>
              
            </div>
        </div>

        {{-- RTL Left: White Form --}}
        <div class="gr-auth-form-side">
            <h2 class="gr-auth-title">{{ __('إنشاء حساب') }}</h2>

            @if(session('error'))
                <div style="background-color: #fef2f2; color: #991b1b; padding: 10px 14px; border-radius: 8px; font-size: 0.9rem; margin-bottom: 15px; text-align: center;">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('store.auth.register.post') }}" method="POST">
                @csrf

                <div class="gr-form-group">
                    <label>{{ __('الاسم بالكامل') }}</label>
                    <input type="text" 
                           name="name" 
                           class="gr-form-control" 
                           placeholder="{{ __('اسمك الكريم') }}" 
                           value="{{ old('name') }}" 
                           required autofocus>
                    @error('name')
                        <div class="gr-error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="gr-form-group">
                    <label>{{ __('البريد الإلكتروني أو رقم الموبايل') }}</label>
                    <input type="text" 
                           name="phone" 
                           class="gr-form-control" 
                           placeholder="name@example.com" 
                           value="{{ old('phone') }}" 
                           dir="auto" 
                           required>
                    @error('phone')
                        <div class="gr-error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="gr-form-group">
                    <label>{{ __('كلمة المرور') }}</label>
                    <input type="password" 
                           name="password" 
                           class="gr-form-control" 
                           placeholder="" 
                           required>
                    @error('password')
                        <div class="gr-error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="gr-form-group">
                    <label>{{ __('تأكيد كلمة المرور') }}</label>
                    <input type="password" 
                           name="password_confirmation" 
                           class="gr-form-control" 
                           placeholder="" 
                           required>
                </div>

                @include('store.partials.recaptcha')

                <button type="submit" class="gr-btn-primary">
                    {{ __('إنشاء حساب بالبريد الإلكتروني') }}
                </button>
            </form>

            <div class="gr-divider">
                <span>{{ __('هل لديك حساب مسبقاً؟') }}</span>
            </div>

            <div class="gr-secondary-action">
                <a href="{{ route('store.auth.login') }}" class="gr-btn-outline-gold">
                    {{ __('تسجيل الدخول') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
