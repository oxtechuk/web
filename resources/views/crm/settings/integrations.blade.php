@extends('partials.Layouts.crm-master')
@section('title', __('الربط والإشعارات') . ' | AutoCRM')

@section('content')
    <div class="container-fluid" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
        <div class="mb-2 d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-1 fw-bold"> <i class="bi bi-plugin me-2 text-primary"></i>{{ __('الربط والإشعارات (Integrations)') }}</h4>
                <p class="text-muted mb-0 small">{{ __('إدارة ربط الواجهات البرمجية (API) وقوالب رسائل الواتساب والـ SMS') }}</p>
            </div>
        </div>

        @include('partials.settings-subnav')

        <form action="{{ route('crm.settings.update') }}" method="POST">
            @csrf
            <div class="row g-4">
                <div class="col-lg-8">

                    {{-- ============================== --}}
                    {{-- HyperPay Payment Gateway --}}
                    {{-- ============================== --}}
                    @php
                        $hyperPayMode    = $settings['hyperpay_mode'] ?? 'test';
                        $hyperPayEnabled = ($settings['hyperpay_enabled'] ?? '0') == '1';
                        $isTestMode      = $hyperPayMode === 'test';
                    @endphp
                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                        <div class="card-header border-bottom pt-4 px-4 pb-3" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="fw-bold mb-0 text-white">
                                        <i class="bi bi-credit-card-2-front me-2" style="color:#f4a417;"></i>
                                        بوابة الدفع HyperPay
                                    </h6>
                                    <p class="text-white-50 small mb-0 mt-1">ربط بوابة الدفع لتحصيل رسوم الحجز من العملاء (اختياري للعميل)</p>
                                </div>
                                <span class="badge {{ $isTestMode ? 'bg-warning text-dark' : 'bg-success' }} fs-6 px-3 py-2">
                                    <i class="bi bi-{{ $isTestMode ? 'bug' : 'shield-check' }} me-1"></i>
                                    {{ $isTestMode ? 'Test Mode' : 'Live Mode' }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-4">

                            {{-- Enable/Disable HyperPay --}}
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-4 border mb-4"
                                style="background: {{ $hyperPayEnabled ? '#f0fff4' : '#f8f9fa' }}; border-color: {{ $hyperPayEnabled ? '#28a745' : '#dee2e6' }} !important;">
                                <div>
                                    <h6 class="fw-bold mb-1">
                                        <i class="bi bi-toggle-{{ $hyperPayEnabled ? 'on text-success' : 'off text-muted' }} me-2"></i>
                                        تفعيل بوابة الدفع
                                    </h6>
                                    <p class="text-muted small mb-0">عند التفعيل، يظهر خيار الدفع في صفحة الحجز. الدفع <strong>اختياري</strong> — العميل يستطيع الحجز بدون دفع.</p>
                                </div>
                                <div class="form-check form-switch fs-4">
                                    <input type="hidden" name="hyperpay_enabled" value="0">
                                    <input class="form-check-input" type="checkbox" name="hyperpay_enabled"
                                        id="hyperPayEnabled" value="1"
                                        {{ $hyperPayEnabled ? 'checked' : '' }} style="cursor:pointer;">
                                </div>
                            </div>

                            {{-- Test / Live Mode Toggle --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted">وضع التشغيل</label>
                                <div class="d-flex gap-3">
                                    <label class="flex-fill" style="cursor:pointer;">
                                        <input type="radio" name="hyperpay_mode" value="test"
                                            class="d-none hyperpay-mode-radio"
                                            {{ $isTestMode ? 'checked' : '' }}>
                                        <div class="mode-card p-3 rounded-3 border text-center"
                                            style="{{ $isTestMode ? 'border-color:#ffc107 !important; background:#fffbf0;' : '' }}">
                                            <i class="bi bi-bug fs-4 d-block mb-1 {{ $isTestMode ? 'text-warning' : 'text-muted' }}"></i>
                                            <strong class="d-block small">Test Mode</strong>
                                            <span class="text-muted" style="font-size:11px;">للاختبار فقط - لا يخصم</span>
                                        </div>
                                    </label>
                                    <label class="flex-fill" style="cursor:pointer;">
                                        <input type="radio" name="hyperpay_mode" value="live"
                                            class="d-none hyperpay-mode-radio"
                                            {{ !$isTestMode ? 'checked' : '' }}>
                                        <div class="mode-card p-3 rounded-3 border text-center"
                                            style="{{ !$isTestMode ? 'border-color:#28a745 !important; background:#f0fff4;' : '' }}">
                                            <i class="bi bi-shield-check fs-4 d-block mb-1 {{ !$isTestMode ? 'text-success' : 'text-muted' }}"></i>
                                            <strong class="d-block small">Live Mode</strong>
                                            <span class="text-muted" style="font-size:11px;">إنتاج حقيقي - يخصم فعلياً</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Live Mode Warning --}}
                            <div id="liveModeWarning" class="{{ $isTestMode ? 'd-none' : '' }} alert alert-danger rounded-3 border-0 small mb-4">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <strong>تنبيه:</strong> أنت في Live Mode. أي دفع سيخصم فعلياً من بطاقة العميل. تأكد من صحة بيانات API قبل الحفظ.
                            </div>

                            {{-- Test Credentials --}}
                            <div id="testCredentials" class="{{ !$isTestMode ? 'd-none' : '' }}">
                                <div class="p-3 rounded-3 mb-4" style="background:#fffbf0; border:1px solid #ffc107;">
                                    <h6 class="fw-bold text-warning-emphasis mb-3">
                                        <i class="bi bi-bug me-2"></i>بيانات Test Mode
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label fw-bold small text-muted">Entity ID (Test)</label>
                                            <input type="text" name="hyperpay_test_entity_id"
                                                class="form-control bg-light border-0 shadow-none py-2 font-monospace"
                                                dir="ltr"
                                                value="{{ $settings['hyperpay_test_entity_id'] ?? '' }}"
                                                placeholder="8a8294174d0595bb014d05d829cb01cd">
                                            <div class="form-text">احصل عليه من لوحة تحكم HyperPay → Test Credentials</div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold small text-muted">Access Token (Test)</label>
                                            <div class="input-group">
                                                <input type="password" name="hyperpay_test_access_token"
                                                    id="testAccessToken"
                                                    class="form-control bg-light border-0 shadow-none py-2 font-monospace"
                                                    dir="ltr"
                                                    value="{{ $settings['hyperpay_test_access_token'] ?? '' }}"
                                                    placeholder="OGE4Mjk0...">
                                                <button type="button" class="btn btn-outline-secondary border-0"
                                                    onclick="togglePassword('testAccessToken', this)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Live Credentials --}}
                            <div id="liveCredentials" class="{{ $isTestMode ? 'd-none' : '' }}">
                                <div class="p-3 rounded-3 mb-4" style="background:#f0fff4; border:1px solid #28a745;">
                                    <h6 class="fw-bold text-success mb-3">
                                        <i class="bi bi-shield-check me-2"></i>بيانات Live Mode (إنتاج)
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label fw-bold small text-muted">Entity ID (Live)</label>
                                            <input type="text" name="hyperpay_live_entity_id"
                                                class="form-control bg-light border-0 shadow-none py-2 font-monospace"
                                                dir="ltr"
                                                value="{{ $settings['hyperpay_live_entity_id'] ?? '' }}"
                                                placeholder="أدخل Entity ID الخاص بحساب Live">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold small text-muted">Access Token (Live)</label>
                                            <div class="input-group">
                                                <input type="password" name="hyperpay_live_access_token"
                                                    id="liveAccessToken"
                                                    class="form-control bg-light border-0 shadow-none py-2 font-monospace"
                                                    dir="ltr"
                                                    value="{{ $settings['hyperpay_live_access_token'] ?? '' }}"
                                                    placeholder="أدخل Access Token الخاص بحساب Live">
                                                <button type="button" class="btn btn-outline-secondary border-0"
                                                    onclick="togglePassword('liveAccessToken', this)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Booking Fee & Currency --}}
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">
                                        <i class="bi bi-cash-coin me-1 text-primary"></i>رسوم الحجز (مبلغ ثابت)
                                    </label>
                                    <div class="input-group">
                                        <input type="number" name="hyperpay_booking_fee"
                                            class="form-control bg-light border-0 shadow-none py-2"
                                            value="{{ $settings['hyperpay_booking_fee'] ?? '500' }}"
                                            min="1" step="1" placeholder="500">
                                        <span class="input-group-text bg-light border-0">ريال</span>
                                    </div>
                                    <div class="form-text">المبلغ الثابت الذي يدفعه العميل عند اختيار الدفع</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">
                                        <i class="bi bi-currency-exchange me-1 text-primary"></i>العملة
                                    </label>
                                    <select name="hyperpay_currency" class="form-select bg-light border-0 shadow-none py-2">
                                        <option value="SAR" {{ ($settings['hyperpay_currency'] ?? 'SAR') == 'SAR' ? 'selected' : '' }}>SAR - ريال سعودي</option>
                                        <option value="AED" {{ ($settings['hyperpay_currency'] ?? '') == 'AED' ? 'selected' : '' }}>AED - درهم إماراتي</option>
                                        <option value="USD" {{ ($settings['hyperpay_currency'] ?? '') == 'USD' ? 'selected' : '' }}>USD - دولار أمريكي</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Test Connection --}}
                            <div class="border-top pt-3">
                                <button type="button" id="testConnectionBtn"
                                    class="btn btn-outline-primary rounded-3 px-4"
                                    onclick="testHyperPayConnection()">
                                    <i class="bi bi-wifi me-2"></i>اختبار الاتصال
                                </button>
                                <div id="testConnectionResult" class="mt-3 d-none"></div>
                                <p class="text-muted small mt-2 mb-0">
                                    <i class="bi bi-info-circle me-1"></i>
                                    يرسل طلب تجريبي لـ HyperPay للتأكد من صحة بيانات API (لن يخصم أي مبلغ فعلي).
                                    <strong>احفظ الإعدادات أولاً قبل الاختبار.</strong>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- OTP Enabled Toggle --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                        <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-lock text-danger me-2"></i>{{ __('التحقق بالجوال (OTP) في الحاسبة') }}</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-4 border border-light-subtle">
                                <div>
                                    <h6 class="fw-bold mb-1"><i class="bi bi-toggle-on me-2 text-danger"></i>{{ __('تفعيل التحقق بالجوال (OTP)') }}</h6>
                                    <p class="text-muted small mb-0">{{ __('عند التفعيل، يجب على المستخدم التحقق برقم جواله قبل استخدام الحاسبة. عند الإيقاف، يكفي إدخال الاسم والرقم مباشرةً ويتم التسجيل فوراً.') }}</p>
                                </div>
                                <div class="form-check form-switch fs-4">
                                    <input type="hidden" name="otp_enabled" value="0">
                                    <input class="form-check-input" type="checkbox" name="otp_enabled" id="otpEnabled" value="1"
                                        {{ ($settings['otp_enabled'] ?? '1') == '1' ? 'checked' : '' }}
                                        style="cursor:pointer;">
                                </div>
                            </div>
                            <div class="mt-3 p-3 bg-info-subtle text-info rounded-3 small" id="otpOffNote" style="{{ ($settings['otp_enabled'] ?? '1') != '1' ? '' : 'display:none;' }}">
                                <i class="bi bi-info-circle-fill me-1"></i>
                                {{ __('تنبيه: تم إيقاف OTP. سيتم تسجيل المستخدم في عملاء الحاسبة مباشرةً بمجرد إدخال الاسم والرقم.') }}
                            </div>
                            <script>
                            document.getElementById('otpEnabled').addEventListener('change', function() {
                                document.getElementById('otpOffNote').style.display = this.checked ? 'none' : '';
                            });
                            </script>
                        </div>
                    </div>

                    {{-- Twilio API Settings --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                        <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-whatsapp text-success me-2"></i>{{ __('إعدادات Twilio API (واتساب & رسائل نصية)') }}</h6>
                        </div>
                        <div class="card-body p-4">
                            <p class="text-muted small mb-4">{{ __('أدخل بيانات حسابك في Twilio لتتمكن من إرسال إشعارات للعملاء. يمكنك الحصول عليها من لوحة تحكم Twilio.') }}</p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Twilio Account SID</label>
                                    <input type="text" name="twilio_sid" class="form-control bg-light border-0 shadow-none py-2" value="{{ $settings['twilio_sid'] ?? '' }}" placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxx">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Twilio Auth Token</label>
                                    <input type="password" name="twilio_auth_token" class="form-control bg-light border-0 shadow-none py-2" value="{{ $settings['twilio_auth_token'] ?? '' }}" placeholder="••••••••••••••••••••••••">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">{{ __('رقم مرسل الواتساب (Twilio WhatsApp Number)') }}</label>
                                    <input type="text" name="twilio_whatsapp_number" class="form-control bg-light border-0 shadow-none py-2 text-start" dir="ltr" value="{{ $settings['twilio_whatsapp_number'] ?? '' }}" placeholder="whatsapp:+14155238886">
                                    <div class="mt-1 small text-muted">{{ __('يجب أن يبدأ بـ whatsapp: ثم كود الدولة') }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">{{ __('رقم مرسل الـ SMS (Twilio Phone Number)') }}</label>
                                    <input type="text" name="twilio_sms_number" class="form-control bg-light border-0 shadow-none py-2 text-start" dir="ltr" value="{{ $settings['twilio_sms_number'] ?? '' }}" placeholder="+1234567890">
                                    <div class="mt-1 small text-muted">{{ __('اختياري، يستخدم للرسائل النصية فقط') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- WhatsApp Templates --}}
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-file-text text-primary me-2"></i>{{ __('قوالب الرسائل (Templates)') }}</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="alert alert-info rounded-3 border-0 small mb-4">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <strong>{{ __('المتغيرات المتاحة:') }}</strong> يمكنك استخدام المتغيرات التالية داخل نص الرسالة وسيتم استبدالها تلقائياً:
                                <ul class="mb-0 mt-2">
                                    <li><code>{customer_name}</code> - {{ __('اسم العميل') }}</li>
                                    <li><code>{car_name}</code> - {{ __('اسم السيارة (إن وجد)') }}</li>
                                    <li><code>{status}</code> - {{ __('حالة الطلب (لرسائل المتابعة)') }}</li>
                                </ul>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-dark">{{ __('قالب رسالة: تقديم طلب جديد (New Lead)') }}</label>
                                <textarea name="whatsapp_template_new_lead" class="form-control bg-light border-0 shadow-none" rows="4" placeholder="{{ __('مثال: مرحباً {customer_name}، شكراً لتواصلك معنا بخصوص {car_name}. سيقوم فريقنا بالتواصل معك قريباً.') }}">{{ $settings['whatsapp_template_new_lead'] ?? '' }}</textarea>
                                <div class="mt-2 text-muted small">{{ __('يتم إرسالها للعميل عند تقديمه لطلب جديد من الموقع.') }}</div>
                            </div>

                            <hr class="text-muted opacity-25 my-4">

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-dark">{{ __('قالب رسالة: تحديث حالة الطلب (Order Status Update)') }}</label>
                                <textarea name="whatsapp_template_status_update" class="form-control bg-light border-0 shadow-none" rows="4" placeholder="{{ __('مثال: مرحباً {customer_name}، نود إعلامك بأنه تم تغيير حالة طلبك الخاص بـ {car_name} لتصبح: {status}.') }}">{{ $settings['whatsapp_template_status_update'] ?? '' }}</textarea>
                                <div class="mt-2 text-muted small">{{ __('يتم إرسالها للعميل تلقائياً عند تغيير حالة طلبه من الإدارة (مثل: مكتمل، قيد التنفيذ).') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ============================== --}}
                {{-- Email (SMTP) Settings --}}
                {{-- ============================== --}}
                @php
                    $mailDriver     = $settings['mail_driver']       ?? 'smtp';
                    $mailHost       = $settings['mail_host']         ?? '';
                    $mailPort       = $settings['mail_port']         ?? '587';
                    $mailUser       = $settings['mail_username']     ?? '';
                    $mailPass       = $settings['mail_password']     ?? '';
                    $mailEncryption = $settings['mail_encryption']   ?? 'tls';
                    $mailFromAddr   = $settings['mail_from_address'] ?? '';
                    $mailFromName   = $settings['mail_from_name']    ?? 'GR Motors';
                @endphp
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header border-bottom pt-4 px-4 pb-3" style="background: linear-gradient(135deg, #0f5132 0%, #198754 100%);">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="fw-bold mb-0 text-white">
                                    <i class="bi bi-envelope-at me-2" style="color:#90ee90;"></i>
                                    إعدادات البريد الإلكتروني (SMTP)
                                </h6>
                                <p class="text-white-50 small mb-0 mt-1">إعداد خادم البريد لإرسال إيميلات التأكيد وتحديثات الحالة للعملاء</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">

                        {{-- Driver --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">بروتوكول الإرسال (Driver)</label>
                            <select name="mail_driver" class="form-select bg-light border-0 shadow-none">
                                <option value="smtp"    {{ $mailDriver == 'smtp'    ? 'selected' : '' }}>SMTP (موصى به)</option>
                                <option value="sendmail" {{ $mailDriver == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                <option value="log"     {{ $mailDriver == 'log'     ? 'selected' : '' }}>Log (للاختبار فقط)</option>
                            </select>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label class="form-label fw-bold small text-muted">خادم البريد (SMTP Host)</label>
                                <input type="text" name="mail_host" value="{{ is_array($mailHost) ? ($mailHost['value'] ?? '') : $mailHost }}"
                                    class="form-control bg-light border-0 shadow-none"
                                    placeholder="smtp.gmail.com أو mail.example.com" dir="ltr">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">المنفذ (Port)</label>
                                <select name="mail_port" class="form-select bg-light border-0 shadow-none">
                                    @php $portVal = is_array($mailPort) ? ($mailPort['value'] ?? '587') : $mailPort; @endphp
                                    <option value="587"  {{ $portVal == '587'  ? 'selected' : '' }}>587 (TLS)</option>
                                    <option value="465"  {{ $portVal == '465'  ? 'selected' : '' }}>465 (SSL)</option>
                                    <option value="25"   {{ $portVal == '25'   ? 'selected' : '' }}>25 (بدون تشفير)</option>
                                    <option value="2525" {{ $portVal == '2525' ? 'selected' : '' }}>2525</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">اسم المستخدم / الإيميل</label>
                                <input type="email" name="mail_username" value="{{ is_array($mailUser) ? ($mailUser['value'] ?? '') : $mailUser }}"
                                    class="form-control bg-light border-0 shadow-none"
                                    placeholder="your@email.com" dir="ltr">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">كلمة السر / App Password</label>
                                <div class="input-group">
                                    <input type="password" id="mail_password" name="mail_password" value="{{ is_array($mailPass) ? ($mailPass['value'] ?? '') : $mailPass }}"
                                        class="form-control bg-light border-0 shadow-none" dir="ltr">
                                    <button class="btn btn-light border" type="button" id="toggleMailPass">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="mt-1 text-muted small">
                                    💡 إذا كنت تستخدم Gmail، استخدم <a href="https://myaccount.google.com/apppasswords" target="_blank">App Password</a> بدلاً من كلمة مرور حسابك
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">التشفير (Encryption)</label>
                            <div class="d-flex gap-3">
                                @foreach(['tls' => 'TLS (موصى به)', 'ssl' => 'SSL', 'none' => 'بدون تشفير'] as $enc => $label)
                                @php $encVal = is_array($mailEncryption) ? ($mailEncryption['value'] ?? 'tls') : $mailEncryption; @endphp
                                <label class="flex-fill" style="cursor:pointer;">
                                    <input type="radio" name="mail_encryption" value="{{ $enc }}"
                                        class="d-none enc-radio"
                                        {{ $encVal == $enc ? 'checked' : '' }}>
                                    <div class="p-2 rounded-3 border text-center small fw-bold
                                        {{ $encVal == $enc ? 'border-success bg-light' : '' }}" style="font-size:12px;">
                                        {{ $label }}
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">اسم المرسل</label>
                                <input type="text" name="mail_from_name"
                                    value="{{ is_array($mailFromName) ? ($mailFromName['value'] ?? 'GR Motors') : $mailFromName }}"
                                    class="form-control bg-light border-0 shadow-none"
                                    placeholder="GR Motors">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">بريد المرسل (From Address)</label>
                                <input type="email" name="mail_from_address"
                                    value="{{ is_array($mailFromAddr) ? ($mailFromAddr['value'] ?? '') : $mailFromAddr }}"
                                    class="form-control bg-light border-0 shadow-none"
                                    placeholder="noreply@yourdomain.com" dir="ltr">
                            </div>
                        </div>

                        {{-- Test Email --}}
                        <div class="p-3 rounded-3 border" style="background:#f0fff4; border-color:#6fcf97 !important;">
                            <h6 class="fw-bold mb-2 text-success"><i class="bi bi-send me-2"></i>اختبار إعدادات البريد</h6>
                            <p class="text-muted small mb-3">أدخل بريدك الإلكتروني لإرسال رسالة اختبار للتأكد من صحة الإعدادات</p>
                            <div class="d-flex gap-2">
                                <input type="email" id="mail_test_to" class="form-control bg-white border" placeholder="test@example.com" dir="ltr">
                                <button type="button" class="btn btn-success fw-bold px-4 text-nowrap" id="testEmailBtn" onclick="testEmailConnection()">
                                    <i class="bi bi-send me-2"></i>إرسال اختباري
                                </button>
                            </div>
                            <div id="emailTestResult" class="mt-3 d-none"></div>
                        </div>

                        <div class="alert alert-info border-0 rounded-3 mt-3 small">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>ملاحظة:</strong> تأكد من حفظ الإعدادات أولاً قبل إجراء الاختبار. إيميلات التأكيد ترسل تلقائياً للعملاء الذين يدخلون بريدهم عند الحجز.
                        </div>

                    </div>
                </div>

                {{-- Sidebar Action --}}

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden shadow-lg sticky-top" style="top: 20px; background: #0d0d0d;">
                        <div class="card-body p-4 position-relative">
                            <i class="bi bi-save position-absolute opacity-10 text-white" style="font-size: 80px; right: -10px; bottom: -20px;"></i>
                            <h5 class="fw-bold mb-3 text-white">{{ __('حفظ إعدادات الربط') }}</h5>
                            <p class="small text-white-50 mb-4">{{ __('تأكد من صحة بيانات HyperPay وTwilio قبل الحفظ لتجنب تعطل الخدمات.') }}</p>
                            @can('manage-settings-integrations')
                            <button type="submit" class="btn btn-light w-100 py-3 fw-bold rounded-3 shadow-sm">
                                <i class="bi bi-check2-circle me-2 text-primary"></i> {{ __('تحديث الإعدادات') }}
                            </button>
                            @endcan

                            {{-- HyperPay Status --}}
                            <div class="mt-4 p-3 rounded-3" style="background: rgba(255,255,255,0.08);">
                                <h6 class="text-white small fw-bold mb-2">
                                    <i class="bi bi-credit-card me-1" style="color:#f4a417;"></i> حالة HyperPay
                                </h6>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="badge {{ $hyperPayEnabled ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $hyperPayEnabled ? 'مفعّل' : 'معطّل' }}
                                    </span>
                                    <span class="badge {{ $isTestMode ? 'bg-warning text-dark' : 'bg-success' }}">
                                        {{ $isTestMode ? '🧪 Test' : '✅ Live' }}
                                    </span>
                                    @if($settings['hyperpay_booking_fee'] ?? false)
                                    <span class="badge bg-light text-dark">
                                        {{ number_format($settings['hyperpay_booking_fee']) }} {{ $settings['hyperpay_currency'] ?? 'SAR' }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('css')
<style>
    .mode-card {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .mode-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .font-monospace { font-family: 'Courier New', monospace; font-size: 13px; }
    #testConnectionResult .alert { animation: fadeIn 0.3s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection

@section('js')
<script>
// Toggle Test/Live Mode UI
document.querySelectorAll('.hyperpay-mode-radio').forEach(function(radio) {
    radio.addEventListener('change', function() {
        var isTest = this.value === 'test';
        document.getElementById('testCredentials').classList.toggle('d-none', !isTest);
        document.getElementById('liveCredentials').classList.toggle('d-none', isTest);
        document.getElementById('liveModeWarning').classList.toggle('d-none', isTest);
    });
});

// Toggle Password Visibility
function togglePassword(inputId, btn) {
    var input = document.getElementById(inputId);
    var isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    btn.querySelector('i').className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
}

// Test HyperPay Connection
function testHyperPayConnection() {
    var btn = document.getElementById('testConnectionBtn');
    var result = document.getElementById('testConnectionResult');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>جاري الاختبار...';
    result.classList.add('d-none');

    fetch('{{ route("crm.settings.hyperpay.test") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        result.classList.remove('d-none');
        result.innerHTML =
            '<div class="alert alert-' + (data.success ? 'success' : 'danger') + ' rounded-3 border-0 small mb-0">' +
            '<i class="bi bi-' + (data.success ? 'check-circle-fill' : 'x-circle-fill') + ' me-2"></i>' +
            data.message + '</div>';
    })
    .catch(function(err) {
        result.classList.remove('d-none');
        result.innerHTML = '<div class="alert alert-danger rounded-3 border-0 small mb-0"><i class="bi bi-x-circle-fill me-2"></i>خطأ في الاتصال: ' + err.message + '</div>';
    })
    .finally(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-wifi me-2"></i>اختبار الاتصال';
    });
}

function testEmailConnection() {
    const btn    = document.getElementById('testEmailBtn');
    const result = document.getElementById('emailTestResult');
    const testTo = document.getElementById('mail_test_to').value;

    if (!testTo) {
        result.classList.remove('d-none');
        result.innerHTML = '<div class="alert alert-warning rounded-3 border-0 small mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>يرجى إدخال البريد الإلكتروني للاختبار</div>';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>جاري الإرسال...';
    result.classList.add('d-none');

    fetch('{{ route("crm.settings.email.test") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ to: testTo })
    })
    .then(res => res.json())
    .then(data => {
        result.classList.remove('d-none');
        result.innerHTML =
            '<div class="alert alert-' + (data.success ? 'success' : 'danger') + ' rounded-3 border-0 small mb-0">' +
            '<i class="bi bi-' + (data.success ? 'check-circle-fill' : 'x-circle-fill') + ' me-2"></i>' +
            data.message + '</div>';
    })
    .catch(err => {
        result.classList.remove('d-none');
        result.innerHTML = '<div class="alert alert-danger rounded-3 border-0 small mb-0"><i class="bi bi-x-circle-fill me-2"></i>خطأ في الاتصال: ' + err.message + '</div>';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send me-2"></i>إرسال إيميل اختباري';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const toggleMailPass = document.getElementById('toggleMailPass');
    if (toggleMailPass) {
        toggleMailPass.addEventListener('click', function() {
            const input = document.getElementById('mail_password');
            const isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';
            this.querySelector('i').className = 'bi bi-eye' + (isText ? '' : '-slash');
        });
    }
});
</script>
@endsection
