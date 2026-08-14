@extends('partials.Layouts.crm-master')
@section('title', __('الإعدادات العامة') . ' | AutoCRM')

@section('content')
    <div class="container-fluid" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
        <div class="mb-2 d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-1 fw-bold"> {{ __('الإعدادات العامة للموقع') }}</h4>
                <p class="text-muted mb-0 small">{{ __('إدارة معلومات الموقع الأساسية، بيانات التواصل، والمظهر العام') }}</p>
            </div>
        </div>

        @include('partials.settings-subnav')

        <form action="{{ route('crm.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-4">
                <div class="col-lg-9">
                    {{-- Navigation Tabs --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                        <div class="card-body p-0">
                            <ul class="nav nav-pills nav-fill bg-light p-2" id="settingsTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active fw-bold py-3 rounded-3" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">
                                        <i class="bi bi-info-circle me-2"></i> {{ __('المعلومات الأساسية') }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold py-3 rounded-3" id="appearance-tab" data-bs-toggle="tab" data-bs-target="#appearance" type="button" role="tab">
                                        <i class="bi bi-palette me-2"></i> {{ __('المظهر والواجهة') }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold py-3 rounded-3" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">
                                        <i class="bi bi-telephone me-2"></i> {{ __('بيانات التواصل') }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold py-3 rounded-3" id="locations-tab" data-bs-toggle="tab" data-bs-target="#locations" type="button" role="tab">
                                        <i class="bi bi-geo-alt me-2"></i> {{ __('الفروع والمواقع') }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold py-3 rounded-3" id="bento-tab" data-bs-toggle="tab" data-bs-target="#bento" type="button" role="tab">
                                        <i class="bi bi-grid-3x3-gap me-2"></i> {{ __('المعرض الرئيسي') }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold py-3 rounded-3" id="hero-tab" data-bs-toggle="tab" data-bs-target="#hero" type="button" role="tab">
                                        <i class="bi bi-play-circle me-2"></i> {{ __('هيرو الرئيسية') }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold py-3 rounded-3" id="popup-tab" data-bs-toggle="tab" data-bs-target="#popup" type="button" role="tab">
                                        <i class="bi bi-megaphone me-2"></i> {{ __('Popup ترويجي') }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold py-3 rounded-3" id="loader-tab" data-bs-toggle="tab" data-bs-target="#loader" type="button" role="tab">
                                        <i class="bi bi-hourglass-split me-2"></i> {{ __('شاشة التحميل') }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold py-3 rounded-3" id="coming-soon-tab" data-bs-toggle="tab" data-bs-target="#coming-soon" type="button" role="tab">
                                        <i class="bi bi-clock-history me-2"></i> {{ __('قريباً') }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold py-3 rounded-3" id="cookies-tab" data-bs-toggle="tab" data-bs-target="#cookies" type="button" role="tab">
                                        <i class="bi bi-cookie me-2"></i> {{ __('الكوكيز') }}
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- Tab Content --}}
                    <div class="tab-content" id="settingsTabContent">
                        {{-- Tab 1: Basic Info --}}
                        <div class="tab-pane fade show active" id="basic" role="tabpanel">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-body p-4">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-muted">{{ __('اسم الموقع (بالعربية)') }}</label>
                                            <input type="text" name="site_name[ar]" class="form-control bg-light border-0 shadow-none py-2" value="{{ $settings['site_name']['ar'] ?? '' }}" placeholder="مثال: جي آر موتورز">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-muted">{{ __('اسم الموقع (بالإنجليزية)') }}</label>
                                            <input type="text" name="site_name[en]" class="form-control bg-light border-0 shadow-none py-2" value="{{ $settings['site_name']['en'] ?? '' }}" placeholder="e.g.: GR Motors">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-muted">
                                                <i class="bi bi-file-earmark-pdf text-danger me-1"></i>
                                                {{ __('البرتفوليو (بالعربية) — PDF') }}
                                            </label>
                                            @php
                                                $pdfArPath = $settings['portfolio_pdf_ar'] ?? null;
                                                $pdfArUrl  = $pdfArPath ? Storage::disk('public')->url($pdfArPath) : null;
                                            @endphp
                                            @if($pdfArUrl)
                                            <div class="d-flex align-items-center gap-2 mb-2 p-2 rounded-3 border" style="background:#f8f9fa;">
                                                <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                                                <a href="{{ $pdfArUrl }}" target="_blank" class="small fw-bold text-danger text-decoration-none flex-grow-1">
                                                    عرض الملف الحالي
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2"
                                                    onclick="if(confirm('هل تريد حذف البرتفوليو العربي؟')) { document.getElementById('delete_portfolio_pdf_ar').value='1'; this.closest('.d-flex').style.display='none'; }">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <input type="hidden" name="delete_portfolio_pdf_ar" id="delete_portfolio_pdf_ar" value="0">
                                            </div>
                                            @endif
                                            <input type="file" name="portfolio_pdf_ar" accept="application/pdf"
                                                class="form-control bg-light border-0 shadow-none py-2">
                                            <div class="mt-1 text-muted small">{{ __('يقبل PDF فقط — الحجم الأقصى 40MB') }}</div>
                                            {{-- Fallback URL --}}
                                            <div class="mt-2">
                                                <label class="form-label fw-bold small text-muted">{{ __('أو رابط خارجي (بديل)') }}</label>
                                                <input type="url" name="portfolio_link_ar" class="form-control bg-light border-0 shadow-none py-2"
                                                    value="{{ $settings['portfolio_link_ar'] ?? '' }}" placeholder="https://example.com/ar-portfolio">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-muted">
                                                <i class="bi bi-file-earmark-pdf text-danger me-1"></i>
                                                {{ __('البرتفوليو (بالإنجليزية) — PDF') }}
                                            </label>
                                            @php
                                                $pdfEnPath = $settings['portfolio_pdf_en'] ?? null;
                                                $pdfEnUrl  = $pdfEnPath ? Storage::disk('public')->url($pdfEnPath) : null;
                                            @endphp
                                            @if($pdfEnUrl)
                                            <div class="d-flex align-items-center gap-2 mb-2 p-2 rounded-3 border" style="background:#f8f9fa;">
                                                <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                                                <a href="{{ $pdfEnUrl }}" target="_blank" class="small fw-bold text-danger text-decoration-none flex-grow-1">
                                                    View Current File
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2"
                                                    onclick="if(confirm('Delete English portfolio?')) { document.getElementById('delete_portfolio_pdf_en').value='1'; this.closest('.d-flex').style.display='none'; }">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <input type="hidden" name="delete_portfolio_pdf_en" id="delete_portfolio_pdf_en" value="0">
                                            </div>
                                            @endif
                                            <input type="file" name="portfolio_pdf_en" accept="application/pdf"
                                                class="form-control bg-light border-0 shadow-none py-2">
                                            <div class="mt-1 text-muted small">{{ __('Accepts PDF only — Max 40MB') }}</div>
                                            {{-- Fallback URL --}}
                                            <div class="mt-2">
                                                <label class="form-label fw-bold small text-muted">{{ __('أو رابط خارجي (بديل)') }}</label>
                                                <input type="url" name="portfolio_link_en" class="form-control bg-light border-0 shadow-none py-2"
                                                    value="{{ $settings['portfolio_link_en'] ?? '' }}" placeholder="https://example.com/en-portfolio">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold small text-muted">{{ __('نص التذييل (Footer Text)') }}</label>
                                            <textarea name="footer_text" class="form-control bg-light border-0 shadow-none" rows="4" placeholder="{{ __('اكتب النص الذي يظهر في أسفل جميع الصفحات...') }}">{{ $settings['footer_text'] ?? '' }}</textarea>
                                        </div>
                                        <div class="col-md-12 mt-4">
                                            <hr class="text-muted opacity-25">
                                            <div class="d-flex align-items-center justify-content-between p-3 bg-white rounded-3 border" style="border-color: var(--crm-border) !important;">
                                                <div>
                                                    <h6 class="fw-bold mb-1"><i class="bi bi-shuffle me-2 text-danger"></i>{{ __('التوزيع التلقائي لطلبات السيارات') }}</h6>
                                                    <p class="text-muted small mb-0">{{ __('عند تفعيل هذا الخيار، سيتم توزيع طلبات شراء وحجز السيارات تلقائياً وبشكل متساوٍ (Round-Robin) على موظفي المبيعات النشطين.') }}</p>
                                                </div>
                                                <div class="form-check form-switch fs-4">
                                                    <input type="hidden" name="auto_assign_bookings" value="0">
                                                    <input class="form-check-input" type="checkbox" name="auto_assign_bookings" id="autoAssignBookings" value="1" {{ ($settings['auto_assign_bookings'] ?? '0') == '1' ? 'checked' : '' }} style="cursor: pointer;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 2: Appearance (including Logos and Breadcrumb BG) --}}
                        <div class="tab-pane fade" id="appearance" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm rounded-4 h-100">
                                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                                            <h6 class="fw-bold mb-0 text-dark">{{ __('الشعار والأيقونة') }}</h6>
                                        </div>
                                        <div class="card-body p-4 pt-2">
                                            <div class="mb-4">
                                                <label class="form-label fw-bold small text-muted d-block mb-3">{{ __('شعار الموقع (Logo)') }}</label>
                                                <div class="p-3 bg-light rounded-4 mb-3 border border-dashed text-center">
                                                    @if(isset($settings['site_logo']))
                                                        <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="Logo" class="img-fluid" style="max-height: 60px;">
                                                    @else
                                                        <i class="bi bi-image fs-2 opacity-25"></i>
                                                    @endif
                                                </div>
                                                <input type="file" name="site_logo" class="form-control bg-light border-0 shadow-none">
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label fw-bold small text-muted d-block mb-3">{{ __('أيقونة الموقع (Favicon)') }}</label>
                                                <div class="p-3 bg-light rounded-4 mb-3 border border-dashed text-center">
                                                    @if(isset($settings['site_favicon']))
                                                        <img src="{{ asset('storage/' . $settings['site_favicon']) }}" alt="Favicon" width="32">
                                                    @else
                                                        <i class="bi bi-app-indicator fs-2 opacity-25"></i>
                                                    @endif
                                                </div>
                                                <input type="file" name="site_favicon" class="form-control bg-light border-0 shadow-none">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm rounded-4 h-100">
                                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                                            <h6 class="fw-bold mb-0 text-dark">{{ __('خلفية عناوين الصفحات (Breadcrumb BG)') }}</h6>
                                        </div>
                                        <div class="card-body p-4 pt-2 text-center">
                                            <label class="form-label fw-bold small text-muted d-block mb-3 text-start">{{ __('صورة الخلفية الموحدة') }}</label>
                                            <div class="p-2 bg-dark rounded-4 mb-3 border border-dashed d-flex align-items-center justify-content-center overflow-hidden" style="min-height: 200px; background: #222;">
                                                @if(isset($settings['breadcrumb_bg']))
                                                    <img src="{{ asset('storage/' . $settings['breadcrumb_bg']) }}" alt="Breadcrumb" class="img-fluid w-100 object-fit-cover rounded-3" style="max-height: 180px;">
                                                @else
                                                    <div class="text-white opacity-50">
                                                        <i class="bi bi-layout-text-window-reverse fs-1 d-block mb-2"></i>
                                                        <span class="small">{{ __('لم يتم اختيار صورة بعد') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <input type="file" name="breadcrumb_bg" class="form-control bg-light border-0 shadow-none">
                                            <div class="mt-3 p-3 bg-info-subtle text-info rounded-3 text-start small">
                                                <i class="bi bi-info-circle-fill me-1"></i>
                                                {{ __('تظهر هذه الصورة كخلفية لاسم الصفحة في صفحات (من نحن، العروض، المدونة، إلخ).') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-car-front me-2 text-danger"></i>{{ __('الصورة الافتراضية للسيارات (Default Car Thumbnail)') }}</h6>
                                        </div>
                                        <div class="card-body p-4 pt-2">
                                            <div class="row align-items-center">
                                                <div class="col-md-7">
                                                    <label class="form-label fw-bold small text-muted d-block mb-1">{{ __('الصورة المصغرة الافتراضية للسيارات') }}</label>
                                                    <p class="text-muted small mb-3">{{ __('تظهر هذه الصورة تلقائياً لأي سيارة في المعرض، البحث، والمقارنة إذا لم يتم رفع صورة مخصصة لها.') }}</p>
                                                    <input type="hidden" name="delete_default_car_thumbnail" id="delete_default_car_thumbnail" value="0">
                                                    <input type="file" name="default_car_thumbnail" accept="image/*" class="form-control bg-light border-0 shadow-none">
                                                </div>
                                                <div class="col-md-5 text-center mt-3 mt-md-0">
                                                    <div class="p-3 bg-light rounded-4 border border-dashed d-inline-block position-relative w-100" style="min-height: 120px; max-width: 280px;">
                                                        @if(isset($settings['default_car_thumbnail']) && !empty($settings['default_car_thumbnail']))
                                                            <div class="position-relative d-inline-block w-100">
                                                                <img src="{{ asset('storage/' . $settings['default_car_thumbnail']) }}" alt="Default Car Thumbnail" class="img-fluid rounded-3" style="max-height: 100px; object-fit: cover;">
                                                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle p-0" style="width:26px; height:26px; line-height:26px;"
                                                                    onclick="if(confirm('{{ __('هل تريد حذف الصورة الافتراضية؟') }}')) { document.getElementById('delete_default_car_thumbnail').value='1'; this.closest('.position-relative').style.display='none'; }" title="{{ __('حذف') }}">
                                                                    <i class="bi bi-trash fs-6"></i>
                                                                </button>
                                                            </div>
                                                        @else
                                                            <div class="text-muted opacity-50 py-3">
                                                                <i class="bi bi-car-front-fill fs-1 d-block mb-1"></i>
                                                                <span class="small">{{ __('لم يتم رفع صورة افتراضية بعد') }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-fonts me-2 text-danger"></i>{{ __('إعدادات خط الموقع (Site Font Settings)') }}</h6>
                                        </div>
                                        <div class="card-body p-4 pt-2">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold small text-muted">{{ __('نوع الخط النشط') }}</label>
                                                    <select name="site_font" class="form-select bg-light border-0 shadow-none py-2" style="cursor: pointer;">
                                                        <option value="default" {{ ($settings['site_font'] ?? 'default') == 'default' ? 'selected' : '' }}>{{ __('الخط الحالي (Cairo / Inter)') }}</option>
                                                        <option value="bahij_semibold" {{ ($settings['site_font'] ?? 'default') == 'bahij_semibold' ? 'selected' : '' }}>Bahij_TheSansArabic-SemiBold.ttf</option>
                                                        <option value="bahij_plain" {{ ($settings['site_font'] ?? 'default') == 'bahij_plain' ? 'selected' : '' }}>Bahij_TheSansArabic-Plain.ttf</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 d-flex align-items-end">
                                                    <div class="p-3 bg-light rounded-3 small text-muted w-100 border">
                                                        <i class="bi bi-info-circle-fill me-1 text-primary"></i>
                                                        {{ __('ملاحظة: يجب التأكد من وجود ملفات الخطوط المطلوبة في مسار public/assets/fonts/ بالموقع ليعمل الخط بشكل صحيح.') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 3: Contact Info --}}
                        <div class="tab-pane fade" id="contact" role="tabpanel">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-body p-4">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-muted">{{ __('البريد الإلكتروني الرسمي') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-0"><i class="bi bi-envelope"></i></span>
                                                <input type="email" name="contact_email" class="form-control bg-light border-0 shadow-none py-2" value="{{ $settings['contact_email'] ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-muted">{{ __('رقم الهاتف الرئيسي') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-0"><i class="bi bi-telephone"></i></span>
                                                <input type="text" name="contact_phone" class="form-control bg-light border-0 shadow-none py-2" value="{{ $settings['contact_phone'] ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-muted">{{ __('رقم الواتساب') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-0"><i class="bi bi-whatsapp"></i></span>
                                                <input type="text" name="contact_whatsapp" class="form-control bg-light border-0 shadow-none py-2" value="{{ $settings['contact_whatsapp'] ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-muted">{{ __('العنوان بالتفصيل') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-0"><i class="bi bi-geo-alt"></i></span>
                                                <input type="text" name="contact_address" class="form-control bg-light border-0 shadow-none py-2" value="{{ $settings['contact_address'] ?? '' }}">
                                            </div>
                                        </div>

                                        {{-- Social Media Links --}}
                                        <div class="col-12 mt-5">
                                            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-share me-2"></i>{{ __('روابط التواصل الاجتماعي') }}</h6>
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addSocialRow()">
                                                    <i class="bi bi-plus-lg me-1"></i> {{ __('إضافة رابط جديد') }}
                                                </button>
                                            </div>

                                            <div id="social-container" class="d-flex flex-column gap-3">
                                                @foreach($socialMedia as $idx => $social)
                                                <div class="social-row d-flex align-items-center gap-2 p-3 bg-light rounded-4 border border-light-subtle" id="social-row-{{ $idx }}">
                                                    <div class="input-group w-auto">
                                                        <span class="input-group-text bg-white border-0 text-muted"><i class="bi bi-bootstrap"></i></span>
                                                        <input type="text" name="social_icon[]" class="form-control border-0 shadow-none" placeholder="{{ __('أيقونة (مثل: bi-facebook)') }}" value="{{ $social['icon'] ?? '' }}" required style="max-width: 180px;">
                                                    </div>
                                                    <div class="d-flex align-items-center bg-white rounded px-2">
                                                        <input type="color" name="social_color[]" class="form-control form-control-color border-0 p-0 shadow-none bg-transparent" value="{{ $social['color'] ?? '#333333' }}" title="{{ __('اختر لون الأيقونة') }}" style="width: 32px; height: 32px;">
                                                    </div>
                                                    <div class="input-group flex-grow-1">
                                                        <span class="input-group-text bg-white border-0 text-muted"><i class="bi bi-link-45deg"></i></span>
                                                        <input type="url" name="social_link[]" class="form-control border-0 shadow-none text-start" dir="ltr" placeholder="https://..." value="{{ $social['link'] ?? '' }}" required>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-light text-danger rounded-circle p-2" onclick="removeSocialRow({{ $idx }})" title="{{ __('حذف') }}">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </div>
                                                @endforeach
                                            </div>

                                            <div id="no-social-msg" class="text-center py-4 bg-light rounded-4 border border-dashed {{ count($socialMedia) > 0 ? 'd-none' : '' }}">
                                                <i class="bi bi-diagram-3 fs-2 text-muted opacity-50 mb-2 d-block"></i>
                                                <span class="text-muted small">{{ __('لم يتم إضافة حسابات تواصل اجتماعي بعد.') }}</span>
                                            </div>

                                            <div class="mt-3 p-3 bg-light rounded-3 text-muted small">
                                                <i class="bi bi-info-circle-fill me-1 text-primary"></i>
                                                {{ __('استخدم كلاسات Bootstrap Icons للأيقونات. مثال:') }}
                                                <code class="ms-1 px-2 py-1 bg-white rounded">bi-facebook</code>,
                                                <code class="ms-1 px-2 py-1 bg-white rounded">bi-twitter-x</code>,
                                                <code class="ms-1 px-2 py-1 bg-white rounded">bi-instagram</code>,
                                                <code class="ms-1 px-2 py-1 bg-white rounded">bi-snapchat</code>,
                                                <code class="ms-1 px-2 py-1 bg-white rounded">bi-tiktok</code>.
                                                <a href="https://icons.getbootstrap.com/" target="_blank" class="ms-2 text-primary text-decoration-none"><i class="bi bi-box-arrow-up-right me-1"></i>{{ __('تصفح الأيقونات') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 4: Branches & Locations --}}
                        <div class="tab-pane fade" id="locations" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm rounded-4 h-100">
                                        <div class="card-header bg-white border-0 pt-4 px-4">
                                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-geo-alt me-2 text-danger"></i>{{ __('الفرع الرئيسي (جدة)') }}</h6>
                                        </div>
                                        <div class="card-body p-4 pt-2">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small text-muted">{{ __('اسم الفرع (بالعربية)') }}</label>
                                                <input type="text" name="branch_1_name_ar" class="form-control bg-light border-0 shadow-none py-2" value="{{ $settings['branch_1_name_ar'] ?? '' }}" placeholder="{{ __('فرع جدة — حي الجوهرة') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small text-muted">{{ __('اسم الفرع (بالإنجليزية)') }}</label>
                                                <input type="text" name="branch_1_name_en" class="form-control bg-light border-0 shadow-none py-2 text-start" dir="ltr" value="{{ $settings['branch_1_name_en'] ?? '' }}" placeholder="Jeddah Branch — Al Jawhara">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small text-muted">{{ __('العنوان (بالعربية)') }}</label>
                                                <textarea name="branch_1_address_ar" class="form-control bg-light border-0 shadow-none" rows="2" placeholder="{{ __('جدة، حي الجوهرة، معارض السيارات، معرض جي آر') }}">{{ $settings['branch_1_address_ar'] ?? '' }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small text-muted">{{ __('العنوان (بالإنجليزية)') }}</label>
                                                <input type="text" name="branch_1_address_en" class="form-control bg-light border-0 shadow-none py-2 text-start" dir="ltr" value="{{ $settings['branch_1_address_en'] ?? '' }}" placeholder="Al Jawhara Dist., Car Showrooms, Jeddah">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small text-muted">{{ __('رقم الهاتف') }}</label>
                                                <input type="text" name="branch_1_phone" class="form-control bg-light border-0 shadow-none py-2 text-start" dir="ltr" value="{{ $settings['branch_1_phone'] ?? '' }}" placeholder="05xxxxxxxx">
                                            </div>
                                            <div>
                                                <label class="form-label fw-bold small text-muted">{{ __('رابط الخريطة (Embed URL)') }}</label>
                                                <input type="url" name="branch_1_map" class="form-control bg-light border-0 shadow-none py-2 text-start" dir="ltr" value="{{ $settings['branch_1_map'] ?? '' }}" placeholder="https://www.google.com/maps/embed...">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm rounded-4 h-100">
                                        <div class="card-header bg-white border-0 pt-4 px-4">
                                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-geo-alt me-2 text-primary"></i>{{ __('فرع مكة المكرمة') }}</h6>
                                        </div>
                                        <div class="card-body p-4 pt-2">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small text-muted">{{ __('اسم الفرع (بالعربية)') }}</label>
                                                <input type="text" name="branch_2_name_ar" class="form-control bg-light border-0 shadow-none py-2" value="{{ $settings['branch_2_name_ar'] ?? '' }}" placeholder="{{ __('فرع مكة المكرمة — حي ولي العهد') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small text-muted">{{ __('اسم الفرع (بالإنجليزية)') }}</label>
                                                <input type="text" name="branch_2_name_en" class="form-control bg-light border-0 shadow-none py-2 text-start" dir="ltr" value="{{ $settings['branch_2_name_en'] ?? '' }}" placeholder="Makkah Branch — Walyal Ahd">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small text-muted">{{ __('العنوان (بالعربية)') }}</label>
                                                <textarea name="branch_2_address_ar" class="form-control bg-light border-0 shadow-none" rows="2" placeholder="{{ __('مكة المكرمة، حي ولي العهد، طريق إبراهيم الخليل') }}">{{ $settings['branch_2_address_ar'] ?? '' }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small text-muted">{{ __('العنوان (بالإنجليزية)') }}</label>
                                                <input type="text" name="branch_2_address_en" class="form-control bg-light border-0 shadow-none py-2 text-start" dir="ltr" value="{{ $settings['branch_2_address_en'] ?? '' }}" placeholder="Ibrahim Al-Khalil Road, Walyal Ahd Dist., Makkah">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small text-muted">{{ __('رقم الهاتف') }}</label>
                                                <input type="text" name="branch_2_phone" class="form-control bg-light border-0 shadow-none py-2 text-start" dir="ltr" value="{{ $settings['branch_2_phone'] ?? '' }}" placeholder="05xxxxxxxx">
                                            </div>
                                            <div>
                                                <label class="form-label fw-bold small text-muted">{{ __('رابط الخريطة (Embed URL)') }}</label>
                                                <input type="url" name="branch_2_map" class="form-control bg-light border-0 shadow-none py-2 text-start" dir="ltr" value="{{ $settings['branch_2_map'] ?? '' }}" placeholder="https://www.google.com/maps/embed...">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mt-3 p-3 bg-info-subtle text-info rounded-3 small">
                                        <i class="bi bi-info-circle-fill me-1"></i>
                                        {{ __('تظهر الفروع مع خرائطها في صفحة (من نحن). لعرض الموقع على الخريطة: افتح خرائط جوجل > شارك > تضمين خريطة، ثم انسخ رابط iframe (العنوان الذي يبدأ بـ https://www.google.com/maps/embed...). إذا تركت الحقل فارغاً سيظهر الموقع تلقائياً حسب العنوان المكتوب.') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 4: Bento Gallery & Main Gallery --}}
                        <div class="tab-pane fade" id="bento" role="tabpanel">
                            <div class="row g-4">
                                {{-- Bento Cars --}}
                                <div class="col-md-12">
                                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                        <div class="card-header bg-white border-0 pt-4 px-4">
                                            <h6 class="fw-bold mb-0 text-dark">{{ __('تخصيص معرض Bento في الرئيسية') }}</h6>
                                        </div>
                                        <div class="card-body p-4 pt-2">
                                            <label class="form-label fw-bold small text-muted mb-3">{{ __('اختر من 3 إلى 5 سيارات مميزة لعرضها') }}</label>
                                            <select name="bento_cars[]" class="form-select bg-light border-0 shadow-none" multiple="multiple" style="min-height: 250px;">
                                                @foreach($cars as $car)
                                                    <option value="{{ $car->id }}" {{ in_array($car->id, $bentoCars) ? 'selected' : '' }} class="p-2 border-bottom border-light">
                                                        {{ $car->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- Main Gallery Upload --}}
                                <div class="col-md-12">
                                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                                            <h6 class="fw-bold mb-0 text-dark">{{ __('صور المعرض الرئيسي (صفحة من نحن)') }}</h6>
                                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3">{{ count($settings['main_gallery'] ?? []) }} {{ __('صورة') }}</span>
                                        </div>
                                        <div class="card-body p-4 pt-2">
                                            <div class="mb-4">
                                                <label class="form-label fw-bold small text-muted mb-3">{{ __('إضافة صور جديدة') }}</label>
                                                <input type="file" name="main_gallery[]" class="form-control bg-light border-0 shadow-none" multiple accept="image/*">
                                                <div class="mt-2 small text-muted">
                                                    <i class="bi bi-info-circle me-1"></i>
                                                    {{ __('يمكنك اختيار صور متعددة في وقت واحد.') }}
                                                </div>
                                            </div>

                                            @if(isset($settings['main_gallery']) && !empty($settings['main_gallery']))
                                                <label class="form-label fw-bold small text-muted mb-3">{{ __('الصور الحالية') }}</label>
                                                <div class="row g-3">
                                                    @php
                                                        $gallery = is_array($settings['main_gallery']) ? $settings['main_gallery'] : (json_decode($settings['main_gallery'], true) ?: []);
                                                    @endphp
                                                    @foreach($gallery as $img)
                                                        <div class="col-6 col-md-4 col-lg-3">
                                                            <div class="position-relative group">
                                                                <div class="rounded-4 overflow-hidden border shadow-sm" style="height: 120px;">
                                                                    <img src="{{ asset('storage/' . $img) }}" class="w-100 h-100 object-fit-cover">
                                                                </div>
                                                                <button type="submit" name="delete_gallery_image" value="{{ $img }}" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-2 shadow-sm" onclick="return confirm('{{ __('هل أنت متأكد من حذف هذه الصورة؟') }}')" style="width: 28px; height: 28px; padding: 0;">
                                                                    <i class="bi bi-x small"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-center py-5 bg-light rounded-4 border border-dashed">
                                                    <i class="bi bi-images fs-1 text-muted opacity-25 d-block mb-2"></i>
                                                    <span class="text-muted">{{ __('لا توجد صور في المعرض حالياً.') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 5: Hero Section (Slider) --}}
                        <div class="tab-pane fade" id="hero" role="tabpanel">
                            <input type="hidden" name="hero_slides_submitted" value="1">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-images text-danger me-2"></i>{{ __('شرائح الهيرو (Hero Slider)') }}</h6>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addHeroSlide()">
                                        <i class="bi bi-plus-lg me-1"></i> {{ __('إضافة شريحة جديدة') }}
                                    </button>
                                </div>
                                <div class="card-body p-4 pt-2">
                                    <p class="text-muted small mb-4">{{ __('قم بإدارة الشرائح الإعلانية التي تظهر في الواجهة الرئيسية للموقع. يفضل استخدام صور أو فيديوهات عالية الجودة بأبعاد عرضية (مثل 1920x1080).') }}</p>

                                    <div id="hero-slides-container" class="d-flex flex-column gap-4">
                                        @php
                                            $heroSlides = [];
                                            if (isset($settings['hero_slides'])) {
                                                $heroSlides = is_array($settings['hero_slides']) ? $settings['hero_slides'] : (json_decode($settings['hero_slides'], true) ?: []);
                                            }
                                        @endphp

                                        @foreach($heroSlides as $idx => $slide)
                                        @php $slide = is_array($slide) ? $slide : []; @endphp
                                        <div class="hero-slide-item card border border-light-subtle rounded-4 shadow-sm" id="hero-slide-{{ $idx }}">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="fw-bold mb-0 text-muted"><i class="bi bi-arrows-move me-2 cursor-move"></i>{{ __('شريحة') }} <span class="slide-index">{{ $idx + 1 }}</span></h6>
                                                    <button type="button" class="btn btn-sm btn-light text-danger rounded-circle p-2" onclick="removeHeroSlide({{ $idx }})" title="{{ __('حذف الشريحة') }}">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </div>
                                                <div class="row g-4">
                                                    {{-- Arabic Content --}}
                                                    <div class="col-md-6 border-end pe-md-4">
                                                        <div class="p-2 mb-3 bg-light rounded-3 fw-bold small text-dark d-flex align-items-center gap-2">
                                                            <span class="badge bg-danger">AR</span> {{ __('المحتوى بالعربية (Arabic Content)') }}
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-muted">{{ __('صورة أو فيديو الشريحة (بالعربية)') }}</label>
                                                            <input type="hidden" name="hero_slides[{{ $idx }}][image_path_ar]" value="{{ $slide['image_ar'] ?? $slide['image'] ?? '' }}">
                                                            @php
                                                                $imgAr = $slide['image_ar'] ?? $slide['image'] ?? null;
                                                            @endphp
                                                            @if($imgAr)
                                                                <div class="mb-2 rounded-3 overflow-hidden border bg-dark d-flex align-items-center justify-content-center" style="height:100px;">
                                                                    @if(in_array(strtolower(pathinfo($imgAr, PATHINFO_EXTENSION)), ['mp4', 'webm', 'ogg', 'mov', 'avi', 'wmv']))
                                                                        <video class="w-100 h-100" style="object-fit:cover;" muted playsinline controls>
                                                                            <source src="{{ asset('storage/' . $imgAr) }}">
                                                                        </video>
                                                                    @else
                                                                        <img src="{{ asset('storage/' . $imgAr) }}" class="w-100 h-100" style="object-fit:cover;">
                                                                    @endif
                                                                </div>
                                                            @endif
                                                            <input type="file" name="hero_slides[{{ $idx }}][image_ar]" class="form-control bg-light border-0 shadow-none text-sm" accept="image/*,video/*">
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-muted">{{ __('رابط يوتيوب (بالعربية - اختياري)') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-light border-0"><i class="bi bi-youtube text-danger"></i></span>
                                                                <input type="url" name="hero_slides[{{ $idx }}][youtube_link_ar]" class="form-control bg-light border-0 shadow-none py-2 text-start" dir="ltr" value="{{ $slide['youtube_link_ar'] ?? $slide['youtube_link'] ?? '' }}" placeholder="https://www.youtube.com/watch?v=...">
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-muted">{{ __('رابط الشريحة (بالعربية)') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-light border-0"><i class="bi bi-link-45deg"></i></span>
                                                                <input type="url" name="hero_slides[{{ $idx }}][link_ar]" class="form-control bg-light border-0 shadow-none py-2 text-start" dir="ltr" value="{{ $slide['link_ar'] ?? $slide['link'] ?? '' }}" placeholder="https://grmotors.sa/cars">
                                                            </div>
                                                        </div>

                                                        <div class="mb-0">
                                                            <label class="form-label fw-bold small text-muted">{{ __('نص الزر (بالعربية)') }}</label>
                                                            <input type="text" name="hero_slides[{{ $idx }}][button_text_ar]" class="form-control bg-light border-0 shadow-none py-2" value="{{ $slide['button_text_ar'] ?? $slide['button_text'] ?? __('اكتشف السيارات') }}" placeholder="{{ __('مثال: اكتشف العروض') }}">
                                                        </div>
                                                    </div>

                                                    {{-- English Content --}}
                                                    <div class="col-md-6 ps-md-4">
                                                        <div class="p-2 mb-3 bg-light rounded-3 fw-bold small text-dark d-flex align-items-center gap-2">
                                                            <span class="badge bg-primary">EN</span> {{ __('المحتوى بالإنجليزية (English Content)') }}
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-muted">{{ __('صورة أو فيديو الشريحة (بالإنجليزية)') }}</label>
                                                            <input type="hidden" name="hero_slides[{{ $idx }}][image_path_en]" value="{{ $slide['image_en'] ?? '' }}">
                                                            @php
                                                                $imgEn = $slide['image_en'] ?? null;
                                                            @endphp
                                                            @if($imgEn)
                                                                <div class="mb-2 rounded-3 overflow-hidden border bg-dark d-flex align-items-center justify-content-center" style="height:100px;">
                                                                    @if(in_array(strtolower(pathinfo($imgEn, PATHINFO_EXTENSION)), ['mp4', 'webm', 'ogg', 'mov', 'avi', 'wmv']))
                                                                        <video class="w-100 h-100" style="object-fit:cover;" muted playsinline controls>
                                                                            <source src="{{ asset('storage/' . $imgEn) }}">
                                                                        </video>
                                                                    @else
                                                                        <img src="{{ asset('storage/' . $imgEn) }}" class="w-100 h-100" style="object-fit:cover;">
                                                                    @endif
                                                                </div>
                                                            @endif
                                                            <input type="file" name="hero_slides[{{ $idx }}][image_en]" class="form-control bg-light border-0 shadow-none text-sm" accept="image/*,video/*">
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-muted">{{ __('رابط يوتيوب (بالإنجليزية - اختياري)') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-light border-0"><i class="bi bi-youtube text-danger"></i></span>
                                                                <input type="url" name="hero_slides[{{ $idx }}][youtube_link_en]" class="form-control bg-light border-0 shadow-none py-2 text-start" dir="ltr" value="{{ $slide['youtube_link_en'] ?? '' }}" placeholder="https://www.youtube.com/watch?v=...">
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-muted">{{ __('رابط الشريحة (بالإنجليزية)') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-light border-0"><i class="bi bi-link-45deg"></i></span>
                                                                <input type="url" name="hero_slides[{{ $idx }}][link_en]" class="form-control bg-light border-0 shadow-none py-2 text-start" dir="ltr" value="{{ $slide['link_en'] ?? '' }}" placeholder="https://grmotors.sa/en/cars">
                                                            </div>
                                                        </div>

                                                        <div class="mb-0">
                                                            <label class="form-label fw-bold small text-muted">{{ __('نص الزر (بالإنجليزية)') }}</label>
                                                            <input type="text" name="hero_slides[{{ $idx }}][button_text_en]" class="form-control bg-light border-0 shadow-none py-2" value="{{ $slide['button_text_en'] ?? '' }}" placeholder="e.g.: Explore Offers">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    <div id="no-slides-msg" class="text-center py-5 bg-light rounded-4 border border-dashed mt-3 {{ count($heroSlides) > 0 ? 'd-none' : '' }}">
                                        <i class="bi bi-images fs-1 text-muted opacity-25 d-block mb-2"></i>
                                        <span class="text-muted">{{ __('لا توجد شرائح حالياً. اضغط على "إضافة شريحة جديدة" للبدء.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 6: Promo Popup --}}
                        <div class="tab-pane fade" id="popup" role="tabpanel">
                            @php
                                $promoPopup = [];
                                if (isset($settings['promo_popup'])) {
                                    $promoPopup = is_array($settings['promo_popup']) ? $settings['promo_popup'] : (json_decode($settings['promo_popup'], true) ?: []);
                                }
                            @endphp
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-header bg-white border-0 pt-4 px-4 d-flex align-items-center gap-3">
                                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width:46px;height:46px;background:linear-gradient(135deg,#EE1E26,#a8151b)">
                                        <i class="bi bi-megaphone-fill text-white fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">{{ __('إعدادات الـ Popup الترويجي') }}</h6>
                                        <p class="text-muted small mb-0">{{ __('يظهر للزوار بعد 5 دقائق من تصفح الصفحة الرئيسية') }}</p>
                                    </div>
                                </div>
                                <div class="card-body p-4 pt-2">

                                    {{-- Enable/Disable Toggle --}}
                                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-4 border border-light-subtle mb-4">
                                        <div>
                                            <h6 class="fw-bold mb-1"><i class="bi bi-toggle-on me-2 text-danger"></i>{{ __('تفعيل الـ Popup') }}</h6>
                                            <p class="text-muted small mb-0">{{ __('عند التفعيل، سيظهر الـ Popup تلقائياً للزوار بعد 5 دقائق.') }}</p>
                                        </div>
                                        <div class="form-check form-switch fs-4">
                                            <input type="hidden" name="popup_enabled" value="0">
                                            <input class="form-check-input" type="checkbox" name="popup_enabled" id="popupEnabled" value="1"
                                                {{ ($promoPopup['enabled'] ?? false) ? 'checked' : '' }}
                                                style="cursor:pointer;">
                                        </div>
                                    </div>

                                    <div class="row g-4">
                                        {{-- Image Upload --}}
                                        <div class="col-md-5">
                                            <label class="form-label fw-bold small text-muted d-block mb-2">{{ __('صورة الـ Popup') }}</label>
                                            <div class="rounded-4 overflow-hidden border bg-light d-flex align-items-center justify-content-center mb-3" style="min-height:180px;">
                                                @if(!empty($promoPopup['image']))
                                                    <img src="{{ asset('storage/' . $promoPopup['image']) }}" alt="Popup" class="w-100" style="object-fit:cover;max-height:200px;">
                                                @else
                                                    <div class="text-center text-muted opacity-50">
                                                        <i class="bi bi-image fs-1 d-block mb-1"></i>
                                                        <span class="small">{{ __('لا توجد صورة') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <input type="file" name="popup_image" class="form-control bg-light border-0 shadow-none" accept="image/*">
                                            <div class="mt-2 small text-muted"><i class="bi bi-info-circle me-1"></i>{{ __('يفضل صور بنسبة 16:9 أو 4:3') }}</div>
                                        </div>

                                        {{-- Text Fields --}}
                                        <div class="col-md-7">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label fw-bold small text-muted">{{ __('عنوان الـ Popup') }}</label>
                                                    <input type="text" name="popup_title" class="form-control bg-light border-0 shadow-none py-2"
                                                        value="{{ $promoPopup['title'] ?? '' }}"
                                                        placeholder="{{ __('مثال: عروض مميزة لهذا الشهر!') }}">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-bold small text-muted">{{ __('نص الوصف') }}</label>
                                                    <textarea name="popup_text" class="form-control bg-light border-0 shadow-none" rows="3"
                                                        placeholder="{{ __('اكتب نص قصير جذاب يشجع الزوار على التفاعل...') }}">{{ $promoPopup['text'] ?? '' }}</textarea>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-bold small text-muted">{{ __('رابط الزر (URL)') }}</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-0"><i class="bi bi-link-45deg"></i></span>
                                                        <input type="text" name="popup_link" class="form-control bg-light border-0 shadow-none py-2 text-start" dir="ltr"
                                                            value="{{ $promoPopup['link'] ?? '' }}"
                                                            placeholder="https://...">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-bold small text-muted">{{ __('نص زر الإجراء') }}</label>
                                                    <input type="text" name="popup_button_text" class="form-control bg-light border-0 shadow-none py-2"
                                                        value="{{ $promoPopup['button_text'] ?? __('تصفح العروض') }}"
                                                        placeholder="{{ __('مثال: اعرف أكثر') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Preview Box --}}
                                    <div class="mt-4 p-3 bg-light rounded-4 border border-light-subtle">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="bi bi-eye text-danger"></i>
                                            <span class="fw-bold small text-dark">{{ __('معلومة') }}</span>
                                        </div>
                                        <p class="small text-muted mb-0">
                                            {{ __('سيظهر الـ Popup للزائر بعد مرور 5 دقائق من فتح الصفحة الرئيسية، ولن يتكرر في نفس جلسة التصفح. إذا كان الـ Popup معطلاً أو لا تتوفر بيانات، لن يظهر شيء.') }}
                                        </p>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Tab 7: Loader Settings --}}
                        <div class="tab-pane fade" id="loader" role="tabpanel">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-header bg-white border-0 pt-4 px-4 d-flex align-items-center gap-3">
                                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width:46px;height:46px;background:linear-gradient(135deg,#EE1E26,#a8151b)">
                                        <i class="bi bi-hourglass-split text-white fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">{{ __('إعدادات شاشة التحميل (Loader)') }}</h6>
                                        <p class="text-muted small mb-0">{{ __('التحكم في الشاشة التي تظهر أثناء تحميل الصفحة') }}</p>
                                    </div>
                                </div>
                                <div class="card-body p-4 pt-2">

                                    {{-- Enable/Disable Toggle --}}
                                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-4 border border-light-subtle mb-4">
                                        <div>
                                            <h6 class="fw-bold mb-1"><i class="bi bi-toggle-on me-2 text-danger"></i>{{ __('تفعيل شاشة التحميل') }}</h6>
                                            <p class="text-muted small mb-0">{{ __('عند التفعيل، ستظهر شاشة التحميل قبل اكتمال تحميل عناصر الصفحة.') }}</p>
                                        </div>
                                        <div class="form-check form-switch fs-4">
                                            <input class="form-check-input" type="checkbox" name="page_loader_enabled" id="loaderEnabled" value="1"
                                                {{ ($settings['page_loader_enabled'] ?? '1') == '1' ? 'checked' : '' }}
                                                style="cursor:pointer;">
                                        </div>
                                    </div>

                                    <div class="row g-4">
                                        {{-- Image Upload --}}
                                        <div class="col-md-5">
                                            <label class="form-label fw-bold small text-muted d-block mb-2">{{ __('صورة أو GIF للتحميل') }}</label>
                                            <div class="rounded-4 overflow-hidden border bg-light d-flex align-items-center justify-content-center mb-3 p-3" style="min-height:180px; background-color: #0f0f11 !important;">
                                                @if(!empty($settings['page_loader_image']))
                                                    <img src="{{ asset('storage/' . $settings['page_loader_image']) }}" alt="Loader" class="w-100" style="object-fit:contain;max-height:150px;">
                                                @else
                                                    <div class="text-center text-white opacity-50">
                                                        <i class="bi bi-image fs-1 d-block mb-1"></i>
                                                        <span class="small">{{ __('اللوجو الافتراضي') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <input type="file" name="page_loader_image" class="form-control bg-light border-0 shadow-none" accept="image/*">
                                            <div class="mt-2 small text-muted"><i class="bi bi-info-circle me-1"></i>{{ __('يمكنك رفع صورة بصيغة PNG أو GIF متحرك') }}</div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Tab 8: Coming Soon --}}
                        <div class="tab-pane fade" id="coming-soon" role="tabpanel">
                            @php
                                $comingSoonEnabled = ($settings['coming_soon_enabled'] ?? '0') == '1';
                            @endphp
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-header bg-white border-0 pt-4 px-4 d-flex align-items-center gap-3">
                                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width:46px;height:46px;background:linear-gradient(135deg,#EE1E26,#a8151b)">
                                        <i class="bi bi-clock-history text-white fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">{{ __('إعدادات صفحة قريباً (Coming Soon)') }}</h6>
                                        <p class="text-muted small mb-0">{{ __('عند التفعيل، يُعاد توجيه زوار الموقع إلى صفحة "قريباً" بدلاً من الصفحة الرئيسية') }}</p>
                                    </div>
                                </div>
                                <div class="card-body p-4 pt-2">

                                    {{-- Enable/Disable --}}
                                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-4 border border-light-subtle mb-4">
                                        <div>
                                            <h6 class="fw-bold mb-1"><i class="bi bi-toggle-on me-2 text-danger"></i>{{ __('تفعيل وضع قريباً') }}</h6>
                                            <p class="text-muted small mb-0">{{ __('سيتم توجيه جميع الزوار إلى صفحة "قريباً" عند التفعيل.') }}</p>
                                        </div>
                                        <div class="form-check form-switch fs-4">
                                            <input type="hidden" name="coming_soon_enabled" value="0">
                                            <input class="form-check-input" type="checkbox" name="coming_soon_enabled" id="comingSoonEnabled" value="1"
                                                {{ $comingSoonEnabled ? 'checked' : '' }}
                                                style="cursor:pointer;">
                                        </div>
                                    </div>

                                    <div class="row g-4">
                                        {{-- BG Image --}}
                                        <div class="col-md-5">
                                            <label class="form-label fw-bold small text-muted d-block mb-2">{{ __('صورة خلفية الصفحة') }}</label>
                                            <div class="rounded-4 overflow-hidden border bg-dark d-flex align-items-center justify-content-center mb-3" style="min-height:180px;">
                                                @if(!empty($settings['coming_soon_bg_image']))
                                                    <img src="{{ asset('storage/' . $settings['coming_soon_bg_image']) }}" alt="Coming Soon BG" class="w-100" style="object-fit:cover;max-height:200px;">
                                                @else
                                                    <div class="text-center text-white opacity-50">
                                                        <i class="bi bi-image fs-1 d-block mb-1"></i>
                                                        <span class="small">{{ __('لا توجد صورة') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <input type="file" name="coming_soon_bg_image" class="form-control bg-light border-0 shadow-none" accept="image/*">
                                            <div class="mt-2 small text-muted"><i class="bi bi-info-circle me-1"></i>{{ __('يفضل صور عالية الجودة 1920x1080') }}</div>
                                        </div>

                                        {{-- Text Fields --}}
                                        <div class="col-md-7">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label fw-bold small text-muted">{{ __('تاريخ الإطلاق (للعداد التنازلي)') }}</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-0"><i class="bi bi-calendar-event"></i></span>
                                                        <input type="datetime-local" name="coming_soon_date" class="form-control bg-light border-0 shadow-none py-2 text-start" dir="ltr"
                                                            value="{{ $settings['coming_soon_date'] ?? '' }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold small text-muted">{{ __('العنوان الرئيسي (عربي)') }}</label>
                                                    <input type="text" name="coming_soon_title_ar" class="form-control bg-light border-0 shadow-none py-2"
                                                        value="{{ $settings['coming_soon_title_ar'] ?? '' }}"
                                                        placeholder="{{ __('مثال: قادمون قريباً!') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold small text-muted">{{ __('العنوان الرئيسي (إنجليزي)') }}</label>
                                                    <input type="text" name="coming_soon_title_en" class="form-control bg-light border-0 shadow-none py-2" dir="ltr"
                                                        value="{{ $settings['coming_soon_title_en'] ?? '' }}"
                                                        placeholder="Coming Soon!">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold small text-muted">{{ __('النص التوضيحي (عربي)') }}</label>
                                                    <textarea name="coming_soon_subtitle_ar" class="form-control bg-light border-0 shadow-none" rows="2"
                                                        placeholder="{{ __('نعمل على تطوير تجربتك...') }}">{{ $settings['coming_soon_subtitle_ar'] ?? '' }}</textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold small text-muted">{{ __('النص التوضيحي (إنجليزي)') }}</label>
                                                    <textarea name="coming_soon_subtitle_en" class="form-control bg-light border-0 shadow-none" rows="2" dir="ltr"
                                                        placeholder="We are working on something amazing...">{{ $settings['coming_soon_subtitle_en'] ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 p-3 bg-warning-subtle rounded-4 border border-warning-subtle">
                                        <i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>
                                        <span class="small fw-bold text-dark">{{ __('تنبيه: عند تفعيل وضع قريباً، لن يتمكن أحد من الوصول إلى الموقع باستثناء فريق الإدارة.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 9: Cookie Consent --}}
                        <div class="tab-pane fade" id="cookies" role="tabpanel">
                            @php
                                $cookieEnabled = ($settings['cookie_consent_enabled'] ?? '0') == '1';
                            @endphp
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-header bg-white border-0 pt-4 px-4 d-flex align-items-center gap-3">
                                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width:46px;height:46px;background:linear-gradient(135deg,#EE1E26,#a8151b)">
                                        <i class="bi bi-cookie text-white fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">{{ __('إعدادات إشعار الكوكيز (Cookie Consent)') }}</h6>
                                        <p class="text-muted small mb-0">{{ __('يظهر شريط في أسفل الصفحة يطلب موافقة الزوار على استخدام ملفات الكوكيز') }}</p>
                                    </div>
                                </div>
                                <div class="card-body p-4 pt-2">

                                    {{-- Enable/Disable --}}
                                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-4 border border-light-subtle mb-4">
                                        <div>
                                            <h6 class="fw-bold mb-1"><i class="bi bi-toggle-on me-2 text-danger"></i>{{ __('تفعيل إشعار الكوكيز') }}</h6>
                                            <p class="text-muted small mb-0">{{ __('سيظهر شريط الكوكيز في أسفل الصفحة لجميع الزوار الجدد.') }}</p>
                                        </div>
                                        <div class="form-check form-switch fs-4">
                                            <input type="hidden" name="cookie_consent_enabled" value="0">
                                            <input class="form-check-input" type="checkbox" name="cookie_consent_enabled" id="cookieConsentEnabled" value="1"
                                                {{ $cookieEnabled ? 'checked' : '' }}
                                                style="cursor:pointer;">
                                        </div>
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-muted">{{ __('نص الإشعار (عربي)') }}</label>
                                            <textarea name="cookie_consent_text_ar" class="form-control bg-light border-0 shadow-none" rows="3"
                                                placeholder="{{ __('نستخدم ملفات الكوكيز لتحسين تجربتك على موقعنا...') }}">{{ $settings['cookie_consent_text_ar'] ?? '' }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-muted">{{ __('نص الإشعار (إنجليزي)') }}</label>
                                            <textarea name="cookie_consent_text_en" class="form-control bg-light border-0 shadow-none" rows="3" dir="ltr"
                                                placeholder="We use cookies to improve your experience...">{{ $settings['cookie_consent_text_en'] ?? '' }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold small text-muted">{{ __('رابط سياسة الخصوصية') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-0"><i class="bi bi-link-45deg"></i></span>
                                                <input type="text" name="cookie_consent_link" class="form-control bg-light border-0 shadow-none py-2 text-start" dir="ltr"
                                                    value="{{ $settings['cookie_consent_link'] ?? '' }}"
                                                    placeholder="https://grmotors.sa/privacy">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Preview --}}
                                    <div class="mt-4">
                                        <label class="form-label fw-bold small text-muted d-block mb-2"><i class="bi bi-eye me-1 text-danger"></i>{{ __('معاينة شريط الكوكيز') }}</label>
                                        <div class="p-3 rounded-4" style="background:#1a1a1a;color:#fff;display:flex;align-items:center;justify-content:space-between;gap:12px;">
                                            <p class="mb-0 small" style="opacity:0.85;">{{ __('نستخدم ملفات الكوكيز لتحسين تجربتك على موقعنا. بمواصلة التصفح، فإنك توافق على استخدامنا لها.') }}</p>
                                            <div class="d-flex gap-2 flex-shrink-0">
                                                <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3" style="font-size:12px;">{{ __('معرفة المزيد') }}</button>
                                                <button type="button" class="btn btn-sm rounded-pill px-3" style="background:linear-gradient(180deg, #ED1C24 0%, #B1161C 32.21%, #8A1217 55.77%, #5A0D10 100%);color:#fff;border:none;font-size:12px;font-weight:700;">{{ __('قبول') }}</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Sidebar Action --}}
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 bg-primary text-white overflow-hidden shadow-lg sticky-top" style="top: 20px;">
                        <div class="card-body p-4 position-relative"style="background-color: black !important;">
                            <i class="bi bi-save position-absolute opacity-10" style="font-size: 80px; right: -10px; bottom: -20px;"></i>
                            <h5 class="fw-bold mb-3">{{ __('حفظ التغييرات') }}</h5>
                            <p class="small opacity-75 mb-4">{{ __('تأكد من مراجعة كافة التبويبات قبل الحفظ.') }}</p>
                            @can('manage-settings')
                            <button type="submit" class="btn btn-white w-100 py-3 fw-black text-primary border-0 rounded-3 shadow-sm">
                                <i class="bi bi-check2-circle me-2"style="color: #ee1b24 !important;"></i> {{ __('تحديث الإعدادات') }}
                            </button>
                            @endcan
                        </div>
                    </div>

                    {{-- Clear Cache Action --}}
                    @can('manage-settings')
                    <div class="card border-0 shadow-sm rounded-4 mt-3 overflow-hidden shadow-sm">
                        <div class="card-body p-4 text-center">
                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-trash3 me-2 text-danger"></i>{{ __('ذاكرة التخزين المؤقت') }}</h6>
                            <p class="small text-muted mb-3">{{ __('مسح الكاش لتحديث البيانات وإجبار النظام على قراءة الإعدادات الجديدة فوراً.') }}</p>
                            <button type="button" onclick="clearSystemCache(this)" class="btn btn-outline-danger w-100 py-2.5 rounded-3 fw-bold small" style="cursor: pointer;">
                                <i class="bi bi-arrow-clockwise me-1"></i> {{ __('مسح الكاش (Clear Cache)') }}
                            </button>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>
        </form>
    </div>
@endsection

@section('css')
<style>
    .nav-pills .nav-link { color: #64748b; }
    .nav-pills .nav-link.active { background-color: #fff !important; color: var(--crm-red) !important; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .btn-white { background: #fff; }
    .fw-black { font-weight: 900; }
    .bg-info-subtle { background: #e0f2fe; }
    .bg-primary-subtle { background: #e7f1ff; }
    .object-fit-cover { object-fit: cover; }

    /* ===== Video Upload Zone ===== */
    .video-upload-zone {
        border: 2px dashed #d1d5db;
        border-radius: 16px;
        padding: 28px 20px;
        cursor: pointer;
        transition: border-color 0.25s, background 0.25s;
        background: #fafafa;
        margin-bottom: 12px;
    }
    .video-upload-zone:hover {
        border-color: #EE1E26;
        background: #fff5f5;
    }
    .upload-zone-inner {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .upload-icon-wrap {
        width: 52px; height: 52px;
        background: linear-gradient(135deg, #EE1E26, #a8151b);
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .upload-icon { font-size: 24px; color: #fff; }
    .upload-text-wrap { flex: 1; }
    .upload-title { display: block; font-weight: 700; font-size: 15px; color: #1a1a1a; }
    .upload-sub   { display: block; font-size: 12px; color: #888; margin-top: 3px; }
    .upload-btn {
        background: linear-gradient(180deg, #ED1C24 0%, #B1161C 32.21%, #8A1217 55.77%, #5A0D10 100%); color: #fff; border: none;
        padding: 8px 20px; border-radius: 10px;
        font-weight: 700; font-size: 13px;
        white-space: nowrap; flex-shrink: 0;
        transition: opacity 0.2s;
    }
    .upload-btn:hover { opacity: 0.88; }

    /* ===== File Info Card ===== */
    .video-upload-info {
        background: #f8faff;
        border: 1px solid #e0e7ff;
        border-radius: 14px;
        padding: 14px 16px;
        margin-bottom: 10px;
    }
    .file-meta {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .file-icon {
        width: 40px; height: 40px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 18px; flex-shrink: 0;
    }
    .file-details { flex: 1; overflow: hidden; }
    .file-name {
        font-weight: 700; font-size: 13px; color: #1a1a1a;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .file-size { font-size: 12px; color: #888; margin-top: 2px; }
    .file-remove {
        background: none; border: none; color: #ef4444;
        font-size: 20px; cursor: pointer; padding: 0; flex-shrink: 0;
        transition: transform 0.2s;
    }
    .file-remove:hover { transform: scale(1.15); }

    /* ===== Progress Bar ===== */
    .progress-wrap { margin-top: 10px; }
    .progress-bar-bg {
        height: 8px; background: #e2e8f0;
        border-radius: 100px; overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%; width: 0%;
        background: linear-gradient(90deg, #EE1E26, #ff6b6b);
        border-radius: 100px;
        transition: width 0.4s ease;
        position: relative;
    }
    .progress-bar-fill::after {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.4) 50%, transparent 100%);
        animation: shimmer 1.5s infinite;
        border-radius: 100px;
    }
    @keyframes shimmer {
        0%   { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    .progress-labels {
        display: flex;
        justify-content: space-between;
        margin-top: 6px;
    }
    .progress-pct    { font-weight: 800; font-size: 12px; color: #EE1E26; }
    .progress-status { font-size: 12px; color: #64748b; }
</style>
@endsection

@section('scripts')
<script>
// Hero Slider Dynamic Rows
function addHeroSlide() {
    const container = document.getElementById('hero-slides-container');
    const items = document.querySelectorAll('.hero-slide-item');
    const idx = items.length > 0 ? parseInt(items[items.length - 1].id.split('-').pop()) + 1 : 0;

    document.getElementById('no-slides-msg').classList.add('d-none');

    const div = document.createElement('div');
    div.className = 'hero-slide-item card border border-light-subtle rounded-4 shadow-sm';
    div.id = 'hero-slide-' + idx;

    div.innerHTML = `
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-muted"><i class="bi bi-arrows-move me-2 cursor-move"></i>{{ __('شريحة جديدة') }}</h6>
                <button type="button" class="btn btn-sm btn-light text-danger rounded-circle p-2" onclick="removeHeroSlide(${idx})" title="{{ __('حذف الشريحة') }}">
                    <i class="bi bi-trash-fill"></i>
                </button>
            </div>
            <div class="row g-4">
                {{-- Arabic Content --}}
                <div class="col-md-6 border-end pe-md-4">
                    <div class="p-2 mb-3 bg-light rounded-3 fw-bold small text-dark d-flex align-items-center gap-2">
                        <span class="badge bg-danger">AR</span> {{ __('المحتوى بالعربية (Arabic Content)') }}
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">{{ __('صورة أو فيديو الشريحة (بالعربية)') }}</label>
                        <input type="file" name="hero_slides[${idx}][image_ar]" class="form-control bg-light border-0 shadow-none text-sm" accept="image/*,video/*">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">{{ __('رابط يوتيوب (بالعربية - اختياري)') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-youtube text-danger"></i></span>
                            <input type="url" name="hero_slides[${idx}][youtube_link_ar]" class="form-control bg-light border-0 shadow-none py-2 text-start" dir="ltr" placeholder="https://www.youtube.com/watch?v=...">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">{{ __('رابط الشريحة (بالعربية)') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-link-45deg"></i></span>
                            <input type="url" name="hero_slides[${idx}][link_ar]" class="form-control bg-light border-0 shadow-none py-2 text-start" dir="ltr" placeholder="https://grmotors.sa/cars">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold small text-muted">{{ __('نص الزر (بالعربية)') }}</label>
                        <input type="text" name="hero_slides[${idx}][button_text_ar]" class="form-control bg-light border-0 shadow-none py-2" value="{{ __('اكتشف السيارات') }}" placeholder="{{ __('مثال: اكتشف العروض') }}">
                    </div>
                </div>

                {{-- English Content --}}
                <div class="col-md-6 ps-md-4">
                    <div class="p-2 mb-3 bg-light rounded-3 fw-bold small text-dark d-flex align-items-center gap-2">
                        <span class="badge bg-primary">EN</span> {{ __('المحتوى بالإنجليزية (English Content)') }}
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">{{ __('صورة أو فيديو الشريحة (بالإنجليزية)') }}</label>
                        <input type="file" name="hero_slides[${idx}][image_en]" class="form-control bg-light border-0 shadow-none text-sm" accept="image/*,video/*">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">{{ __('رابط يوتيوب (بالإنجليزية - اختياري)') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-youtube text-danger"></i></span>
                            <input type="url" name="hero_slides[${idx}][youtube_link_en]" class="form-control bg-light border-0 shadow-none py-2 text-start" dir="ltr" placeholder="https://www.youtube.com/watch?v=...">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">{{ __('رابط الشريحة (بالإنجليزية)') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-link-45deg"></i></span>
                            <input type="url" name="hero_slides[${idx}][link_en]" class="form-control bg-light border-0 shadow-none py-2 text-start" dir="ltr" placeholder="https://grmotors.sa/en/cars">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold small text-muted">{{ __('نص الزر (بالإنجليزية)') }}</label>
                        <input type="text" name="hero_slides[${idx}][button_text_en]" class="form-control bg-light border-0 shadow-none py-2" placeholder="e.g.: Explore Offers">
                    </div>
                </div>
            </div>
        </div>
    `;
    container.appendChild(div);
}

function removeHeroSlide(idx) {
    document.getElementById('hero-slide-' + idx)?.remove();
    if (document.querySelectorAll('.hero-slide-item').length === 0) {
        document.getElementById('no-slides-msg').classList.remove('d-none');
    }
}

// Social Media Dynamic Rows
let socialCount = {{ count($socialMedia) }};
function addSocialRow() {
    const idx = socialCount++;
    document.getElementById('no-social-msg').classList.add('d-none');

    const container = document.getElementById('social-container');
    const div = document.createElement('div');
    div.className = 'social-row d-flex align-items-center gap-2 p-3 bg-light rounded-4 border border-light-subtle';
    div.id = 'social-row-' + idx;

    div.innerHTML = `
        <div class="input-group w-auto">
            <span class="input-group-text bg-white border-0 text-muted"><i class="bi bi-bootstrap"></i></span>
            <input type="text" name="social_icon[]" class="form-control border-0 shadow-none" placeholder="{{ __('أيقونة (مثل: bi-facebook)') }}" required style="max-width: 180px;">
        </div>
        <div class="d-flex align-items-center bg-white rounded px-2">
            <input type="color" name="social_color[]" class="form-control form-control-color border-0 p-0 shadow-none bg-transparent" value="#333333" title="{{ __('اختر لون الأيقونة') }}" style="width: 32px; height: 32px;">
        </div>
        <div class="input-group flex-grow-1">
            <span class="input-group-text bg-white border-0 text-muted"><i class="bi bi-link-45deg"></i></span>
            <input type="url" name="social_link[]" class="form-control border-0 shadow-none text-start" dir="ltr" placeholder="https://..." required>
        </div>
        <button type="button" class="btn btn-sm btn-light text-danger rounded-circle p-2" onclick="removeSocialRow(${idx})" title="{{ __('حذف') }}">
            <i class="bi bi-trash-fill"></i>
        </button>
    `;
    container.appendChild(div);
}

function removeSocialRow(idx) {
    document.getElementById('social-row-' + idx)?.remove();
    if (document.querySelectorAll('.social-row').length === 0) {
        document.getElementById('no-social-msg').classList.remove('d-none');
    }
}

function clearSystemCache(btn) {
    if(!confirm("{{ __('هل أنت متأكد من رغبتك في مسح الكاش؟') }}")) return;

    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __('جاري المسح...') }}';

    fetch('{{ route("crm.settings.clear-cache") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        if(data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        alert('{{ __('حدث خطأ أثناء مسح الكاش، يرجى المحاولة لاحقاً.') }}');
        console.error(error);
    });
}
</script>
@endsection
