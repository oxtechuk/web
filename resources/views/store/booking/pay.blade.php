@extends('store.layouts.app')
@section('title', __('الدفع الآمن') . ' | ' . __('حجز سيارة'))
@section('meta_description', __('أدخل بيانات بطاقتك لإتمام عملية حجز السيارة بأمان'))

@section('content')
<div class="py-5" style="min-height:80vh; background: linear-gradient(135deg, #0f0f23 0%, #1a1a3e 50%, #0f0f23 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">

                {{-- Header --}}
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                        style="width:70px;height:70px;background:rgba(244,164,23,0.15);border:2px solid #f4a417;">
                        <i class="bi bi-shield-lock-fill fs-2" style="color:#f4a417;"></i>
                    </div>
                    <h2 class="fw-bold text-white mb-1">{{ __('الدفع الآمن') }}</h2>
                    <p class="text-white-50 small">{{ __('رسوم حجز السيارة — مدفوعاتك محمية بتشفير SSL') }}</p>
                </div>

                {{-- Booking Summary --}}
                <div class="card border-0 rounded-4 mb-4 overflow-hidden">
                    <div class="card-body p-4" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-white-50 small mb-1">{{ __('الحجز') }}</p>
                                <h6 class="text-white fw-bold mb-0">{{ $booking->car?->name ?? __('حجز سيارة') }}</h6>
                                <p class="text-white-50 small mb-0">{{ $booking->client_name }}</p>
                            </div>
                            <div class="text-end">
                                <p class="text-white-50 small mb-1">{{ __('رسوم الحجز') }}</p>
                                <h4 class="fw-bold mb-0" style="color:#f4a417;">
                                    {{ number_format($bookingFee) }}
                                    <small class="fs-6 text-white-50">{{ $currency }}</small>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment Form --}}
                <div class="card border-0 rounded-4 overflow-hidden shadow-lg">
                    <div class="card-body p-4" style="background: #ffffff;">

                        {{-- Errors --}}
                        @if(session('payment_error'))
                        <div class="alert alert-danger rounded-3 border-0 small mb-4">
                            <i class="bi bi-x-circle-fill me-2"></i>
                            {{ session('payment_error') }}
                        </div>
                        @endif

                        <form id="paymentForm" action="{{ route('store.booking.pay', $booking->id) }}" method="POST"
                            onsubmit="handlePaymentSubmit(event)">
                            @csrf

                            {{-- Card Brand --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted mb-2">{{ __('نوع البطاقة') }}</label>
                                <div class="d-flex gap-2">
                                    <label class="flex-fill" style="cursor:pointer;">
                                        <input type="radio" name="card_brand" value="VISA" class="d-none card-brand-radio" checked>
                                        <div class="card-brand-option border rounded-3 p-2 text-center" style="border-color:#1a1f71 !important; background:#f0f4ff;">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Visa_Inc._logo.svg/120px-Visa_Inc._logo.svg.png"
                                                alt="Visa" style="height:22px; object-fit:contain;">
                                        </div>
                                    </label>
                                    <label class="flex-fill" style="cursor:pointer;">
                                        <input type="radio" name="card_brand" value="MASTER" class="d-none card-brand-radio">
                                        <div class="card-brand-option border rounded-3 p-2 text-center border-light">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a4/Mastercard_2019_logo.svg/120px-Mastercard_2019_logo.svg.png"
                                                alt="Mastercard" style="height:22px; object-fit:contain;">
                                        </div>
                                    </label>
                                    <label class="flex-fill" style="cursor:pointer;">
                                        <input type="radio" name="card_brand" value="MADA" class="d-none card-brand-radio">
                                        <div class="card-brand-option border rounded-3 p-2 text-center border-light" style="font-weight:700; color:#00813a; font-size:13px; line-height:22px;">
                                            mada
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Card Number --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">{{ __('رقم البطاقة') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="bi bi-credit-card text-muted" id="cardIcon"></i>
                                    </span>
                                    <input type="text" id="cardNumber" name="card_number"
                                        class="form-control bg-light border-0 py-2 font-monospace"
                                        placeholder="0000 0000 0000 0000"
                                        maxlength="19"
                                        autocomplete="cc-number"
                                        oninput="formatCardNumber(this)"
                                        required>
                                </div>
                                @error('card_number')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Card Holder --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">{{ __('اسم حامل البطاقة') }}</label>
                                <input type="text" name="card_holder"
                                    class="form-control bg-light border-0 py-2"
                                    placeholder="{{ __('الاسم كما هو مكتوب على البطاقة') }}"
                                    dir="ltr"
                                    autocomplete="cc-name"
                                    required>
                                @error('card_holder')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Expiry & CVV --}}
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <label class="form-label fw-bold small text-muted">{{ __('تاريخ الانتهاء') }}</label>
                                    <input type="text" id="cardExpiry"
                                        class="form-control bg-light border-0 py-2 font-monospace text-center"
                                        placeholder="MM / YY"
                                        maxlength="7"
                                        autocomplete="cc-exp"
                                        oninput="formatExpiry(this)"
                                        required>
                                    <input type="hidden" name="card_expiry_month" id="cardExpiryMonth">
                                    <input type="hidden" name="card_expiry_year" id="cardExpiryYear">
                                    @error('card_expiry_month')
                                    <div class="text-danger small mt-1">{{ __('تاريخ انتهاء غير صحيح') }}</div>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold small text-muted">
                                        CVV
                                        <button type="button" class="btn btn-link p-0 ms-1 text-muted small"
                                            data-bs-toggle="tooltip" title="{{ __('الرقم المكون من 3-4 أرقام على ظهر البطاقة') }}">
                                            <i class="bi bi-question-circle"></i>
                                        </button>
                                    </label>
                                    <div class="input-group">
                                        <input type="password" name="card_cvv"
                                            class="form-control bg-light border-0 py-2 font-monospace text-center"
                                            placeholder="•••"
                                            maxlength="4"
                                            autocomplete="cc-csc"
                                            required>
                                        <button type="button" class="btn btn-light border-0"
                                            onmousedown="document.querySelector('[name=card_cvv]').type='text'"
                                            onmouseup="document.querySelector('[name=card_cvv]').type='password'"
                                            onmouseleave="document.querySelector('[name=card_cvv]').type='password'">
                                            <i class="bi bi-eye-fill text-muted small"></i>
                                        </button>
                                    </div>
                                    @error('card_cvv')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <button type="submit" id="payBtn"
                                class="btn w-100 py-3 fw-bold rounded-3 text-white border-0 fs-6"
                                style="background: linear-gradient(135deg, #f4a417, #e8920a);">
                                <i class="bi bi-lock-fill me-2"></i>
                                {{ __('ادفع :amount :currency وأكمل الحجز', ['amount' => number_format($bookingFee), 'currency' => $currency]) }}
                            </button>

                            {{-- Processing Overlay (يظهر عند الإرسال) --}}
                            <div id="processingOverlay" class="d-none text-center py-4">
                                <div class="spinner-border text-warning mb-3" style="width:3rem;height:3rem;"></div>
                                <p class="fw-bold text-dark mb-1">{{ __('جاري معالجة الدفع...') }}</p>
                                <p class="text-muted small mb-0">{{ __('لا تغلق هذه الصفحة أو تضغط زر الرجوع') }}</p>
                            </div>

                            {{-- Security Badges --}}
                            <div class="d-flex align-items-center justify-content-center gap-3 mt-3">
                                <span class="text-muted small"><i class="bi bi-lock-fill text-success me-1"></i>SSL Secured</span>
                                <span class="text-muted small"><i class="bi bi-shield-fill-check text-primary me-1"></i>PCI DSS</span>
                                <span class="text-muted small"><i class="bi bi-credit-card-2-back me-1"></i>HyperPay</span>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Skip Payment Link --}}
                <div class="text-center mt-4">
                    <a href="{{ route('store.booking.success', $booking->id) }}"
                        class="text-white-50 small text-decoration-none"
                        onclick="return confirm('{{ __('هل تريد إكمال الحجز بدون دفع رسوم الحجز؟') }}')">
                        <i class="bi bi-arrow-left me-1"></i>
                        {{ __('إكمال الحجز بدون دفع (الدفع اختياري)') }}
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    .font-monospace { font-family: 'Courier New', monospace; letter-spacing: 1px; }
    .card-brand-option {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .card-brand-radio:checked + .card-brand-option {
        border-color: #f4a417 !important;
        box-shadow: 0 0 0 2px rgba(244,164,23,0.3);
        background: #fffbf0 !important;
    }
    #payBtn:hover { transform: translateY(-1px); box-shadow: 0 8px 25px rgba(244,164,23,0.4); }
    #payBtn:active { transform: none; }
    #payBtn:disabled { opacity: 0.7; cursor: not-allowed; transform: none; box-shadow: none; }
    #payBtn { transition: all 0.2s ease; }
</style>
@endsection

@section('js')
<script>
// ======================================
// Card Number Formatter
// ======================================
function formatCardNumber(input) {
    var value = input.value.replace(/\D/g, '').substring(0, 16);
    input.value = value.replace(/(.{4})/g, '$1 ').trim();
}

// ======================================
// Expiry Date Formatter
// ======================================
function formatExpiry(input) {
    var value = input.value.replace(/\D/g, '').substring(0, 6);
    if (value.length >= 2) {
        value = value.substring(0, 2) + ' / ' + value.substring(2);
    }
    input.value = value;

    // استخراج الشهر والسنة
    var parts = input.value.replace(/\s/g, '').split('/');
    document.getElementById('cardExpiryMonth').value = parts[0] || '';
    var year = parts[1] || '';
    // تحويل YY إلى YYYY
    document.getElementById('cardExpiryYear').value = year.length === 2 ? '20' + year : year;
}

// ======================================
// Card Brand Toggle UI
// ======================================
document.querySelectorAll('.card-brand-radio').forEach(function(radio) {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.card-brand-option').forEach(function(opt) {
            opt.style.borderColor = '';
            opt.style.boxShadow = '';
        });
    });
});

// ======================================
// Anti-Double-Submit Protection
// ======================================
var isSubmitting = false;

function handlePaymentSubmit(e) {
    // منع الإرسال المزدوج
    if (isSubmitting) {
        e.preventDefault();
        return false;
    }

    // التحقق من تاريخ الانتهاء
    var month = document.getElementById('cardExpiryMonth').value;
    var year  = document.getElementById('cardExpiryYear').value;
    if (!month || !year || parseInt(month) < 1 || parseInt(month) > 12) {
        e.preventDefault();
        alert('{{ __('تاريخ انتهاء البطاقة غير صحيح') }}');
        return false;
    }

    // تفعيل حالة الإرسال
    isSubmitting = true;

    var btn = document.getElementById('payBtn');
    var overlay = document.getElementById('processingOverlay');

    // إخفاء الزر وإظهار الـ overlay
    btn.style.display = 'none';
    overlay.classList.remove('d-none');

    // إعادة تفعيل بعد 30 ثانية (في حالة timeout)
    setTimeout(function() {
        if (isSubmitting) {
            btn.style.display = '';
            overlay.classList.add('d-none');
            isSubmitting = false;
            alert('{{ __('انتهت مهلة الدفع. حاول مرة أخرى.') }}');
        }
    }, 30000);

    return true; // اسمح بالإرسال
}

// Tooltips
if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
        new bootstrap.Tooltip(el);
    });
}
</script>
@endsection
