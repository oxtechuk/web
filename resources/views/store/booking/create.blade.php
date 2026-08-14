@extends('store.layouts.app')
@section('title', __('حجز سيارة') . ($car ? ' — ' . $car->name : '') . ' | ' . (is_array($globalSettings['site_name'] ?? null) ? ($globalSettings['site_name'][App::getLocale()] ?? ($globalSettings['site_name']['ar'] ?? 'GR Motors')) : ($globalSettings['site_name'] ?? 'GR Motors')))
@section('meta_description', __('احجز سيارتك المفضلة الآن مع GR Motors. نموذج حجز سريع وآمن مع حاسبة تقسيط فورية.'))

@section('content')

<div class="booking-page">

  {{-- ══════════════════════════════════════════════ --}}
  {{--              HERO HEADER                       --}}
  {{-- ══════════════════════════════════════════════ --}}
  <section class="bk-hero">
    <div class="bk-hero__overlay"></div>
    <div class="container bk-hero__inner">
      <div class="bk-hero__badge">
        <i class="bi bi-shield-check"></i> {{ __('حجز آمن وموثوق') }}
      </div>
      <h1 class="bk-hero__title">
        {{ __('احجز') }} <span class="bk-hero__title--accent">{{ __('سيارة أحلامك') }}</span>
      </h1>
      <p class="bk-hero__subtitle">
        {{ __('أكمل بياناتك وسيتواصل معك فريق المبيعات المتخصص لتأكيد طلبك وتجهيز أفضل عرض سعر.') }}
      </p>
      {{-- Steps --}}
      <div class="bk-steps">
        <div class="bk-step bk-step--active">
          <div class="bk-step__icon"><i class="bi bi-1-circle-fill"></i></div>
          <span>{{ __('بياناتك') }}</span>
        </div>
        <div class="bk-step__line"></div>
        <div class="bk-step bk-step--active">
          <div class="bk-step__icon"><i class="bi bi-2-circle-fill"></i></div>
          <span>{{ __('خطة الدفع') }}</span>
        </div>
        <div class="bk-step__line"></div>
        <div class="bk-step">
          <div class="bk-step__icon"><i class="bi bi-3-circle-fill"></i></div>
          <span>{{ __('التأكيد') }}</span>
        </div>
      </div>
    </div>
  </section>

  {{-- ══════════════════════════════════════════════ --}}
  {{--         MAIN CONTENT: Two-Column Layout        --}}
  {{-- ══════════════════════════════════════════════ --}}
  <section class="bk-main">
    <div class="container bk-main__grid">

      {{-- ────────────────────────────────── --}}
      {{-- LEFT COLUMN: Car Card (Sticky)     --}}
      {{-- ────────────────────────────────── --}}
      <aside class="bk-sidebar">

        {{-- Car Summary Card --}}
        <div class="bk-car-card">
          @if($car)
            <div class="bk-car-card__img-wrap">
<img loading="lazy" 
                src="{{ $car->thumbnail ? asset('storage/' . $car->thumbnail) : asset('assets/images/placeholder-car.jpg') }}"
                alt="{{ $car->name }}"
                class="bk-car-card__img"
              >
              @if($car->brand)
                <div class="bk-car-card__brand-badge">
                  {{ $car->brand->name }}
                </div>
              @endif
            </div>
            <div class="bk-car-card__body">
              <h2 class="bk-car-card__name">{{ $car->name }} {{ $car->year }}</h2>
              <div class="bk-car-card__price-row">
                <div>
                  <div class="bk-car-card__price-label">{{ __('السعر الكلي') }}</div>
                  <div class="bk-car-card__price">{{ number_format($car->cash_price) }} <span>{!! __('ريال') !!}</span></div>
                </div>
                @if($car->min_installment)
                <div class="bk-car-card__installment">
                  <div class="bk-car-card__price-label">{{ __('أقل قسط شهري') }}</div>
                  <div class="bk-car-card__monthly">{{ number_format($car->min_installment) }} <span>{!! __('ريال') !!}/{{ __('شهر') }}</span></div>
                </div>
                @endif
              </div>
            </div>
          @else
            <div class="bk-car-card__body bk-car-card__body--empty">
              <div class="bk-car-card__empty-icon"><i class="bi bi-car-front"></i></div>
              <p>{{ __('اختر السيارة المطلوبة من النموذج') }}</p>
            </div>
          @endif
        </div>

        {{-- Live Calculator Preview --}}
        <div class="bk-calc-preview" id="calcPreview">
          <div class="bk-calc-preview__header">
            <i class="bi bi-calculator"></i>
            <span>{{ __('حاسبة القسط الشهري') }}</span>
          </div>
          <div class="bk-calc-preview__result">
            <div class="bk-calc-preview__label">{{ __('القسط الشهري المقدر') }}</div>
            <div class="bk-calc-preview__amount" id="liveMonthly">
              <span class="bk-calc-preview__amount--num" id="liveMonthlyNum">—</span>
              <span class="bk-calc-preview__amount--cur">{!! __('ريال') !!}</span>
            </div>
          </div>
          <div class="bk-calc-preview__breakdown">
            <div class="bk-calc-preview__row">
              <span>{{ __('سعر السيارة') }}</span>
              <span id="liveCarPrice">{{ $car ? number_format($car->cash_price) . ' ' . __('ريال') : '—' }}</span>
            </div>
            <div class="bk-calc-preview__row">
              <span>{{ __('المقدم') }}</span>
              <span id="liveDown">—</span>
            </div>
            <div class="bk-calc-preview__row">
              <span>{{ __('المبلغ الممول') }}</span>
              <span id="livePrincipal">—</span>
            </div>
            <div class="bk-calc-preview__row bk-calc-preview__row--total">
              <span>{{ __('الإجمالي') }}</span>
              <span id="liveTotal">—</span>
            </div>
          </div>
          <div class="bk-calc-preview__note">
            <i class="bi bi-info-circle"></i>
            {{ __('الحساب تقديري بنسبة فائدة 4% سنوياً. العرض النهائي يُحدده فريق المبيعات.') }}
          </div>
        </div>

        {{-- Trust Badges --}}
        <div class="bk-trust">
          <div class="bk-trust__item">
            <div class="bk-trust__icon"><i class="bi bi-shield-lock-fill"></i></div>
            <div class="bk-trust__text">
              <strong>{{ __('بيانات آمنة') }}</strong>
              <span>{{ __('معلوماتك محمية 100%') }}</span>
            </div>
          </div>
          <div class="bk-trust__item">
            <div class="bk-trust__icon"><i class="bi bi-headset"></i></div>
            <div class="bk-trust__text">
              <strong>{{ __('تواصل فوري') }}</strong>
              <span>{{ __('رد خلال 24 ساعة') }}</span>
            </div>
          </div>
          <div class="bk-trust__item">
            <div class="bk-trust__icon"><i class="bi bi-award-fill"></i></div>
            <div class="bk-trust__text">
              <strong>{{ __('أفضل سعر مضمون') }}</strong>
              <span>{{ __('نقدم أفضل عروض التمويل') }}</span>
            </div>
          </div>
        </div>

      </aside>

      {{-- ────────────────────────────────── --}}
      {{-- RIGHT COLUMN: Booking Form         --}}
      {{-- ────────────────────────────────── --}}
      <main class="bk-form-wrap">

        {{-- Success Alert --}}
        @if(session('success'))
          <div class="bk-alert bk-alert--success">
            <i class="bi bi-check-circle-fill bk-alert__icon"></i>
            <div>
              <strong>{{ __('تم إرسال طلبك بنجاح!') }}</strong>
              <p>{{ session('success') }}</p>
            </div>
          </div>
        @endif

        {{-- Validation Errors --}}
        @if($errors->any())
          <div class="bk-alert bk-alert--error">
            <i class="bi bi-exclamation-triangle-fill bk-alert__icon"></i>
            <div>
              <strong>{{ __('يرجى تصحيح الأخطاء التالية:') }}</strong>
              <ul class="bk-alert__list">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        @endif

        <form action="{{ route('store.booking.store') }}" method="POST" id="bookingForm" class="bk-form" novalidate>
          @csrf
          <input type="hidden" name="monthly_installment" id="hiddenMonthly" value="0">
          <input type="hidden" name="total_price"         id="hiddenTotal"   value="0">
          <input type="hidden" name="interest_rate"       value="4">

          {{-- ═══════════ SECTION 1: السيارة ═══════════ --}}
          <div class="bk-form__section">
            <div class="bk-form__section-header">
              <div class="bk-form__section-icon"><i class="bi bi-car-front-fill"></i></div>
              <div>
                <h3 class="bk-form__section-title">{{ __('السيارة المطلوبة') }}</h3>
                <p class="bk-form__section-subtitle">{{ __('اختر السيارة التي تريد الاستفسار عنها') }}</p>
              </div>
            </div>
            <div class="bk-form__fields">
              <div class="bk-form__group bk-form__group--full">
                <label class="bk-form__label" for="car_id">
                  {{ __('السيارة') }} <span class="bk-form__required">*</span>
                </label>
                <div class="bk-select-wrap">
                  <select
                    name="car_id"
                    id="car_id"
                    class="bk-form__select {{ $errors->has('car_id') ? 'bk-form__select--error' : '' }}"
                    required
                    onchange="onCarChange(this)"
                  >
                    <option value="">{{ __('— اختر السيارة —') }}</option>
                    @foreach($cars as $c)
                      <option
                        value="{{ $c->id }}"
                        data-price="{{ $c->cash_price }}"
                        {{ (old('car_id', $car?->id) == $c->id) ? 'selected' : '' }}
                      >
                        {{ $c->brand?->name }} {{ $c->name }} {{ $c->year }} — {{ number_format($c->cash_price) }} {!! __('ريال') !!}
                      </option>
                    @endforeach
                  </select>
                  <i class="bi bi-chevron-down bk-select-wrap__arrow"></i>
                </div>
                @error('car_id')
                  <span class="bk-form__error">{{ $message }}</span>
                @enderror
              </div>
            </div>
          </div>

          {{-- ═══════════ SECTION 1.5: تفاصيل الطلب ═══════════ --}}
          <div class="bk-form__section">
            <div class="bk-form__section-header">
              <div class="bk-form__section-icon"><i class="bi bi-clipboard-check-fill"></i></div>
              <div>
                <h3 class="bk-form__section-title">{{ __('تفاصيل الطلب') }}</h3>
                <p class="bk-form__section-subtitle">{{ __('نوع الطلب والموقع') }}</p>
              </div>
            </div>
            <div class="bk-form__fields">
              <div class="bk-form__group">
                <label class="bk-form__label" for="booking_type">
                  {{ __('نوع الطلب') }} <span class="bk-form__required">*</span>
                </label>
                <div class="bk-select-wrap">
                  <select
                    name="booking_type"
                    id="booking_type"
                    class="bk-form__select {{ $errors->has('booking_type') ? 'bk-form__select--error' : '' }}"
                    required
                  >
                    <option value="">{{ __('— اختر النوع —') }}</option>
                    <option value="test_drive" {{ old('booking_type') == 'test_drive' ? 'selected' : '' }}>{{ __('تجربة قيادة') }}</option>
                    <option value="purchase" {{ old('booking_type') == 'purchase' ? 'selected' : '' }}>{{ __('شراء') }}</option>
                    <option value="inquiry" {{ old('booking_type') == 'inquiry' ? 'selected' : '' }}>{{ __('استفسار') }}</option>
                  </select>
                  <i class="bi bi-chevron-down bk-select-wrap__arrow"></i>
                </div>
                @error('booking_type')
                  <span class="bk-form__error">{{ $message }}</span>
                @enderror
              </div>

              <div class="bk-form__group">
                <label class="bk-form__label" for="location">
                  {{ __('الموقع الجغرافي') }}
                  <span class="bk-form__optional">({{ __('اختياري') }})</span>
                </label>
                <div class="bk-input-wrap">
                  <i class="bi bi-geo-alt bk-input-wrap__icon"></i>
                  <input
                    type="text"
                    name="location"
                    id="location"
                    class="bk-form__input {{ $errors->has('location') ? 'bk-form__input--error' : '' }}"
                    placeholder="{{ __('مثال: الرياض، المملكة العربية السعودية') }}"
                    value="{{ old('location') }}"
                  >
                </div>
                @error('location')
                  <span class="bk-form__error">{{ $message }}</span>
                @enderror
              </div>
            </div>
          </div>

          {{-- ═══════════ SECTION 2: بياناتك ═══════════ --}}
          <div class="bk-form__section">
            <div class="bk-form__section-header">
              <div class="bk-form__section-icon"><i class="bi bi-person-fill"></i></div>
              <div>
                <h3 class="bk-form__section-title">{{ __('بياناتك الشخصية') }}</h3>
                <p class="bk-form__section-subtitle">{{ __('لنتمكن من التواصل معك') }}</p>
              </div>
            </div>

            @auth('web')
              <div class="mb-4 p-3 rounded-3 border" style="background: #eef2ff; border-color: #c7d2fe !important;">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-person-check-fill text-primary fs-5"></i>
                  <div>
                    <strong class="text-dark">{{ __('مرحباً :name', ['name' => auth('web')->user()->name]) }}</strong>
                    <p class="mb-0 text-muted small">{{ __('تم ملء بيانات التواصل تلقائياً من حسابك المسجل لدينا.') }}</p>
                  </div>
                </div>
              </div>
            @else
              <div class="mb-4 p-3 rounded-3 border" style="background: #f8fafc; border-color: #e2e8f0 !important;">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-person-plus-fill text-danger fs-5"></i>
                  <div>
                    <strong class="text-dark">{{ __('عميل جديد؟') }}</strong>
                    <p class="mb-0 text-muted small">{{ __('عند تقديم الطلب، سيتم إنشاء حساب لك تلقائياً بموبايلك لتمكينك من متابعة حالته وتحميل الفاتورة.') }}</p>
                  </div>
                </div>
              </div>
            @endauth

            <div class="bk-form__fields">
              <div class="bk-form__group">
                <label class="bk-form__label" for="client_name">
                  {{ __('الاسم بالكامل') }} <span class="bk-form__required">*</span>
                </label>
                <div class="bk-input-wrap">
                  <i class="bi bi-person bk-input-wrap__icon"></i>
                  <input
                    type="text"
                    name="client_name"
                    id="client_name"
                    class="bk-form__input {{ $errors->has('client_name') ? 'bk-form__input--error' : '' }}"
                    placeholder="{{ __('أدخل اسمك الثلاثي') }}"
                    value="{{ old('client_name', auth('web')->user()?->name) }}"
                    required
                    autocomplete="name"
                  >
                </div>
                @error('client_name')
                  <span class="bk-form__error">{{ $message }}</span>
                @enderror
              </div>

              <div class="bk-form__group">
                <label class="bk-form__label" for="client_phone">
                  {{ __('رقم الهاتف') }} <span class="bk-form__required">*</span>
                </label>
                <div class="bk-input-wrap">
                  <i class="bi bi-telephone bk-input-wrap__icon"></i>
                  <input
                    type="tel"
                    name="client_phone"
                    id="client_phone"
                    class="bk-form__input {{ $errors->has('client_phone') ? 'bk-form__input--error' : '' }}"
                    placeholder="05XXXXXXXX"
                    value="{{ old('client_phone', auth('web')->user()?->phone) }}"
                    required
                    dir="ltr"
                    autocomplete="tel"
                  >
                </div>
                @error('client_phone')
                  <span class="bk-form__error">{{ $message }}</span>
                @enderror
              </div>

              <div class="bk-form__group bk-form__group--full">
                <label class="bk-form__label" for="client_email">
                  {{ __('البريد الإلكتروني') }}
                  <span class="bk-form__optional">({{ __('اختياري') }})</span>
                </label>
                <div class="bk-input-wrap">
                  <i class="bi bi-envelope bk-input-wrap__icon"></i>
                  <input
                    type="email"
                    name="client_email"
                    id="client_email"
                    class="bk-form__input {{ $errors->has('client_email') ? 'bk-form__input--error' : '' }}"
                    placeholder="example@email.com"
                    value="{{ old('client_email', auth('web')->user()?->email) }}"
                    dir="ltr"
                    autocomplete="email"
                  >
                </div>
                @error('client_email')
                  <span class="bk-form__error">{{ $message }}</span>
                @enderror
              </div>
            </div>
          </div>

          {{-- ═══════════ SECTION 3: خطة التمويل ═══════════ --}}
          <div class="bk-form__section">
            <div class="bk-form__section-header">
              <div class="bk-form__section-icon"><i class="bi bi-wallet2"></i></div>
              <div>
                <h3 class="bk-form__section-title">{{ __('خطة التمويل') }}</h3>
                <p class="bk-form__section-subtitle">{{ __('حدد المقدم والمدة المناسبة لك') }}</p>
              </div>
            </div>
            <div class="bk-form__fields">
              <div class="bk-form__group">
                <label class="bk-form__label" for="down_payment">
                  {{ __('المقدم المقترح') }} <span class="bk-form__required">*</span>
                </label>
                <div class="bk-input-wrap">
                  <i class="bi bi-cash-coin bk-input-wrap__icon"></i>
                  <input
                    type="number"
                    name="down_payment"
                    id="down_payment"
                    class="bk-form__input {{ $errors->has('down_payment') ? 'bk-form__input--error' : '' }}"
                    placeholder="{{ __('مثال: 50000') }}"
                    value="{{ old('down_payment', 0) }}"
                    min="0"
                    required
                    oninput="recalculate()"
                  >
                  <span class="bk-input-wrap__suffix">{!! __('ريال') !!}</span>
                </div>
                @error('down_payment')
                  <span class="bk-form__error">{{ $message }}</span>
                @enderror
              </div>

              <div class="bk-form__group">
                <label class="bk-form__label" for="duration_years">
                  {{ __('مدة التقسيط') }} <span class="bk-form__required">*</span>
                </label>
                <div class="bk-select-wrap">
                  <select
                    name="duration_years"
                    id="duration_years"
                    class="bk-form__select"
                    required
                    onchange="onDurationChange()"
                  >
                    <option value="3" {{ old('duration_years', 3) == 3 ? 'selected' : '' }}>{{ __('3 سنوات') }}</option>
                    <option value="5" {{ old('duration_years', 3) == 5 ? 'selected' : '' }}>{{ __('5 سنوات') }}</option>
                    <option value="2" {{ old('duration_years', 3) == 2 ? 'selected' : '' }}>{{ __('نظام 50/50 (سنتان)') }}</option>
                  </select>
                  <i class="bi bi-chevron-down bk-select-wrap__arrow"></i>
                </div>
                @error('duration_years')
                  <span class="bk-form__error">{{ $message }}</span>
                @enderror
              </div>

              {{-- Quick Down Payment Chips --}}
              <div class="bk-form__group bk-form__group--full" id="quickChipsWrap" style="display:none;">
                <label class="bk-form__label">{{ __('مقترحات المقدم') }}</label>
                <div class="bk-chips" id="quickChips"></div>
              </div>
            </div>
          </div>

          {{-- ═══════════ SECTION 4: ملاحظات ═══════════ --}}
          <div class="bk-form__section {{ !$hyperPayEnabled ? 'bk-form__section--last' : '' }}">
            <div class="bk-form__section-header">
              <div class="bk-form__section-icon"><i class="bi bi-chat-dots-fill"></i></div>
              <div>
                <h3 class="bk-form__section-title">{{ __('ملاحظات إضافية') }}</h3>
                <p class="bk-form__section-subtitle">{{ __('أي تفاصيل أخرى تريد إضافتها') }}</p>
              </div>
            </div>
            <div class="bk-form__fields">
              <div class="bk-form__group bk-form__group--full">
                <textarea
                  name="notes"
                  id="notes"
                  class="bk-form__textarea"
                  rows="4"
                  placeholder="{{ __('مثال: أريد لون أسود، أو لدي سيارة للمبادلة، أو سؤال عن الضمان...') }}"
                >{{ old('notes') }}</textarea>
              </div>
            </div>
          </div>

          @if($hyperPayEnabled)
          {{-- ═══════════ SECTION 5: الدفع وتأكيد الحجز ═══════════ --}}
          <div class="bk-form__section bk-form__section--last">
            <div class="bk-form__section-header">
              <div class="bk-form__section-icon"><i class="bi bi-credit-card-fill text-warning"></i></div>
              <div>
                <h3 class="bk-form__section-title">{{ __('رسوم حجز السيارة') }}</h3>
                <p class="bk-form__section-subtitle">{{ __('اختر ما إذا كنت ترغب في دفع رسوم الحجز الآن لتأكيد طلبك فوريًا') }}</p>
              </div>
            </div>
            <div class="bk-form__fields">
              <div class="bk-form__group bk-form__group--full">
                <div class="bk-payment-options" style="display: flex; gap: 15px; margin-bottom: 15px; flex-wrap: wrap;">
                  
                  <label class="bk-payment-option-label" style="flex: 1; min-width: 240px; cursor: pointer;">
                    <input type="radio" name="with_payment" value="1" class="d-none bk-payment-radio" checked>
                    <div class="bk-payment-card p-3 rounded-3 border" style="border-color: #f4a417 !important; background: rgba(244, 164, 23, 0.08); transition: all 0.2s; min-height: 90px; display: flex; flex-direction: column; justify-content: center; padding: 15px;">
                      <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-credit-card-2-front-fill text-warning fs-5"></i>
                        <strong class="text-white small" style="font-weight: 700;">{{ __('ادفع رسوم الحجز الآن (أونلاين)') }}</strong>
                      </div>
                      <span class="text-white-50" style="font-size: 12px; display: block; line-height: 1.5;">
                        {{ __('مبلغ الدفع المطلوب:') }} 
                        <strong style="color: #f4a417; font-size: 14px;">{{ number_format($bookingFee) }} {{ $currency }}</strong>
                      </span>
                    </div>
                  </label>

                  <label class="bk-payment-option-label" style="flex: 1; min-width: 240px; cursor: pointer;">
                    <input type="radio" name="with_payment" value="0" class="d-none bk-payment-radio">
                    <div class="bk-payment-card p-3 rounded-3 border" style="border-color: rgba(255, 255, 255, 0.1) !important; background: rgba(255, 255, 255, 0.02); transition: all 0.2s; min-height: 90px; display: flex; flex-direction: column; justify-content: center; padding: 15px;">
                      <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-clock-history text-muted fs-5"></i>
                        <strong class="text-white-50 small" style="font-weight: 700;">{{ __('إرسال الطلب والمتابعة لاحقاً') }}</strong>
                      </div>
                      <span class="text-white-50" style="font-size: 12px; display: block; line-height: 1.5;">
                        {{ __('يمكنك إرسال طلب الحجز مجانًا وسيقوم موظف المبيعات بالتواصل معك.') }}
                      </span>
                    </div>
                  </label>

                </div>
              </div>
            </div>
          </div>

          <style>
            .bk-payment-option-label input[type="radio"]:checked + .bk-payment-card {
              border-color: #f4a417 !important;
              background: rgba(244, 164, 23, 0.08) !important;
              box-shadow: 0 0 10px rgba(244,164,23,0.15);
            }
            .bk-payment-option-label input[type="radio"]:not(:checked) + .bk-payment-card {
              border-color: rgba(255, 255, 255, 0.1) !important;
              background: rgba(255, 255, 255, 0.02) !important;
            }
            .bk-payment-card:hover {
              border-color: rgba(244, 164, 23, 0.6) !important;
            }
          </style>
          @endif

          {{-- Agreement + Submit --}}
          <div class="bk-form__footer">
            <div class="bk-form__agreement">
              <i class="bi bi-info-circle-fill"></i>
              {{ __('بإرسال الطلب، توافق على معالجة بياناتك للتواصل معك وتقديم أفضل عرض سعر متاح.') }}
            </div>
            @include('store.partials.recaptcha')
            <button type="submit" class="bk-form__submit" id="submitBtn">
              <span class="bk-form__submit-text">
                <i class="bi bi-send-check-fill"></i>
                {{ __('إرسال طلب الحجز') }}
              </span>
              <span class="bk-form__submit-sub" id="submitSub">
                {{ __('سيتواصل معك فريقنا قريباً') }}
              </span>
            </button>
          </div>

        </form>
      </main>

    </div>
  </section>

</div>

@endsection

@section('js')
<script>
// ═══════════════════════════════════════════════════════════
//  GR Motors — Booking Page Calculator
// ═══════════════════════════════════════════════════════════
const INTEREST_RATE  = 4.0;  // % سنوياً
let currentCarPrice  = {{ $car ? $car->cash_price : 0 }};

// تحديث عند تغيير السيارة
function onCarChange(select) {
  const opt = select.options[select.selectedIndex];
  currentCarPrice = parseFloat(opt.dataset.price) || 0;
  document.getElementById('liveCarPrice').innerHTML =
    currentCarPrice > 0 ? formatNum(currentCarPrice) + ' ' + '{!! __('ريال') !!}' : '—';
  updateQuickChips();
  recalculate();
}

// حساب القسط الشهري
function calcMonthly(price, down, years) {
  const principal = Math.max(0, price - down);
  const months    = years * 12;
  const r         = (INTEREST_RATE / 100) / 12;
  if (months === 0) return 0;
  if (r === 0) return principal / months;
  return principal * (r * Math.pow(1 + r, months)) / (Math.pow(1 + r, months) - 1);
}

function formatNum(n) {
  return Math.round(n).toLocaleString('{{ app()->getLocale() == 'ar' ? 'ar-SA' : 'en-US' }}');
}

function recalculate() {
  const down     = parseFloat(document.getElementById('down_payment').value) || 0;
  const years    = parseInt(document.getElementById('duration_years').value) || 3;
  const price    = currentCarPrice;

  if (price <= 0) {
    resetCalc();
    return;
  }

  const monthly   = calcMonthly(price, down, years);
  const principal = Math.max(0, price - down);
  const total     = monthly * years * 12 + down;

  // تحديث الـ sidebar
  document.getElementById('liveMonthlyNum').textContent = formatNum(monthly);
  document.getElementById('liveDown').innerHTML       = formatNum(down) + ' ' + '{!! __('ريال') !!}';
  document.getElementById('livePrincipal').innerHTML  = formatNum(principal) + ' ' + '{!! __('ريال') !!}';
  document.getElementById('liveTotal').innerHTML      = formatNum(total) + ' ' + '{!! __('ريال') !!}';

  // تحديث الـ hidden fields
  document.getElementById('hiddenMonthly').value = Math.round(monthly);
  document.getElementById('hiddenTotal').value   = Math.round(total);

  // تحديث نص زر الإرسال
  document.getElementById('submitSub').innerHTML =
    '{{ __('قسط شهري مقدر:') }} ' + formatNum(monthly) + ' ' + '{!! __('ريال') !!}';

  // Animation flash
  const el = document.getElementById('liveMonthlyNum');
  el.classList.remove('flash-anim');
  void el.offsetWidth; // reflow
  el.classList.add('flash-anim');
}

function resetCalc() {
  ['liveMonthlyNum','liveDown','livePrincipal','liveTotal'].forEach(id => {
    document.getElementById(id).textContent = '—';
  });
  document.getElementById('submitSub').textContent = '{{ __('سيتواصل معك فريقنا قريباً') }}';
}

// مدة التقسيط: عند اختيار نظام 50/50 يتم تعبئة المقدم تلقائياً بـ 50%
function onDurationChange() {
  const sel = document.getElementById('duration_years');
  if (sel && sel.value === '2') {
    const dp = Math.round(currentCarPrice * 50 / 100);
    document.getElementById('down_payment').value = dp;
    document.querySelectorAll('.bk-chip').forEach(c => {
      c.classList.toggle('bk-chip--active', parseInt(c.textContent, 10) === 50);
    });
  }
  recalculate();
}

// اقتراحات المقدم (10%, 20%, 30%)
function updateQuickChips() {
  if (currentCarPrice <= 0) {
    document.getElementById('quickChipsWrap').style.display = 'none';
    return;
  }
  const wrap  = document.getElementById('quickChipsWrap');
  const chips = document.getElementById('quickChips');
  wrap.style.display = '';
  chips.innerHTML = '';
  [10, 20, 30, 40, 50].forEach(pct => {
    const val = Math.round(currentCarPrice * pct / 100);
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'bk-chip';
    btn.innerHTML = pct + '% — ' + formatNum(val) + ' ' + '{!! __('ريال') !!}';
    btn.onclick = () => {
      document.getElementById('down_payment').value = val;
      document.querySelectorAll('.bk-chip').forEach(c => c.classList.remove('bk-chip--active'));
      btn.classList.add('bk-chip--active');
      recalculate();
    };
    chips.appendChild(btn);
  });
}

// تشغيل فوري إذا كانت سيارة محددة
document.addEventListener('DOMContentLoaded', () => {
  recalculate();
  updateQuickChips();

  const bookingForm = document.getElementById('bookingForm');

  // Form View tracking
  trackEvent('FormView', { form_type: 'booking' });

  // Form Started tracking (one-time)
  let formStartedFired = false;
  if (bookingForm) {
    bookingForm.addEventListener('focusin', function() {
      if (!formStartedFired) {
        formStartedFired = true;
        trackEvent('FormStarted', { form_type: 'booking' });
      }
    });
  }

  // Form submit loading state + tracking
  bookingForm.addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    const carSelect = document.getElementById('car_id');
    const carModel = carSelect
      ? (carSelect.options[carSelect.selectedIndex]?.text?.replace(/—.*$/, '').trim() || '')
      : '';
    const phone = document.getElementById('client_phone')?.value || '';
    const location = document.getElementById('location')?.value || '';

    btn.classList.add('bk-form__submit--loading');
    btn.querySelector('.bk-form__submit-text').innerHTML =
      '<span class="bk-spinner"></span> {{ __('جاري الإرسال...') }}';

    const baseParams = {
      car_id: carSelect?.value,
      monthly_installment: document.getElementById('hiddenMonthly')?.value
    };

    trackEvent('BookingSubmitted', baseParams);

    trackEvent('Lead', {
      phone: phone,
      car_model: carModel,
      location: location,
      car_price: currentCarPrice || 0,
      installment: document.getElementById('hiddenMonthly')?.value || 0
    });
  });
});
</script>
@endsection
