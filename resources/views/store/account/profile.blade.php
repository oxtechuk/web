@extends('store.account.layout')

@section('account_content')
<div class="premium-content-card">
    <h2 class="premium-page-title">
        {{ __('البيانات الشخصية') }}
    </h2>

    <form action="{{ route('store.account.profile.update') }}" method="POST" class="premium-form m-0 p-0">
        @csrf
        @method('PUT')

        <div class="premium-form-grid">
            <div class="form-field-group">
                <label class="premium-label">{{ __('الاسم بالكامل') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" class="premium-input @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <div class="premium-invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-field-group">
                <label class="premium-label">{{ __('رقم الموبايل') }} <span class="text-danger">*</span></label>
                <input type="text" name="phone" class="premium-input @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" dir="ltr" required>
                @error('phone')
                    <div class="premium-invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-field-group col-span-2">
                <label class="premium-label">{{ __('البريد الإلكتروني') }}</label>
                <input type="email" name="email" class="premium-input @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" dir="ltr">
                @error('email')
                    <div class="premium-invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="premium-section-divider"></div>

        <h4 class="premium-section-subtitle">
            <i class="bi bi-shield-lock-fill"></i> {{ __('تغيير كلمة السر (اختياري)') }}
        </h4>

        <div class="premium-form-grid">
            <div class="form-field-group col-span-2">
                <label class="premium-label">{{ __('كلمة السر الحالية') }}</label>
                <input type="password" name="current_password" class="premium-input @error('current_password') is-invalid @enderror">
                @error('current_password')
                    <div class="premium-invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-field-group">
                <label class="premium-label">{{ __('كلمة السر الجديدة') }}</label>
                <input type="password" name="password" class="premium-input @error('password') is-invalid @enderror">
                @error('password')
                    <div class="premium-invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-field-group">
                <label class="premium-label">{{ __('تأكيد كلمة السر الجديدة') }}</label>
                <input type="password" name="password_confirmation" class="premium-input">
            </div>
        </div>

        <div class="premium-form-actions">
            <button type="submit" class="premium-submit-btn">
                <i class="bi bi-save-fill"></i> {{ __('حفظ التعديلات') }}
            </button>
        </div>
    </form>
</div>

<style>
/* Grid for form inputs */
.premium-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 25px;
}

.col-span-2 {
    grid-column: span 2;
}

.form-field-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.premium-label {
    font-size: 14px;
    font-weight: 700;
    color: #444;
}

.premium-input {
    width: 100%;
    height: 48px;
    padding: 10px 18px;
    border-radius: 12px;
    border: 1px solid #dcdde1;
    background-color: #fcfdfe;
    font-size: 15px;
    font-weight: 600;
    color: #2b2b2b;
    outline: none;
    transition: all 0.25s ease-in-out;
}

.premium-input:focus {
    border-color: var(--primary, linear-gradient(180deg, #ED1C24 0%, #B1161C 32.21%, #8A1217 55.77%, #5A0D10 100%));
    background-color: #ffffff;
    box-shadow: 0 0 0 4px rgba(238, 30, 38, 0.08);
}

.premium-input.is-invalid {
    border-color: #dc2626;
    background-color: #fef2f2;
}

.premium-input.is-invalid:focus {
    box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1);
}

.premium-invalid-feedback {
    color: #dc2626;
    font-size: 13px;
    font-weight: 700;
    margin-top: 2px;
}

.premium-section-divider {
    height: 1px;
    background-color: #f1f2f4;
    margin: 35px 0 30px;
}

.premium-section-subtitle {
    font-size: 18px;
    font-weight: 800;
    color: #1a1a1a;
    margin: 0 0 20px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.premium-section-subtitle i {
    color: var(--primary, linear-gradient(180deg, #ED1C24 0%, #B1161C 32.21%, #8A1217 55.77%, #5A0D10 100%));
}

.premium-form-actions {
    margin-top: 35px;
    display: flex;
    justify-content: flex-end;
}

.premium-submit-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 36px;
    background: var(--gradient-primary, linear-gradient(270deg, #FD7277 0%, #EE1E26 100%));
    color: #ffffff;
    font-weight: 800;
    font-size: 15px;
    border: none;
    border-radius: 50px;
    cursor: pointer;
    box-shadow: 0 8px 20px rgba(238, 30, 38, 0.2);
    transition: all 0.3s ease;
}

.premium-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(238, 30, 38, 0.35);
}

/* Responsive form grid */
@media (max-width: 768px) {
    .premium-form-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .col-span-2 {
        grid-column: span 1;
    }
    
    .premium-form-actions {
        justify-content: center;
    }
    
    .premium-submit-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection
