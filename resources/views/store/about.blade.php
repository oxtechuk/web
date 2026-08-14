@extends('store.layouts.app')

@section('title', __('من نحن') . ' | ' . (is_array($globalSettings['site_name'] ?? null) ? ($globalSettings['site_name'][App::getLocale()] ?? ($globalSettings['site_name']['ar'] ?? 'GR Motors')) : ($globalSettings['site_name'] ?? 'GR Motors')))

@section('breadcrumb-title', __('من نحن'))

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
  /* ===== ABOUT PAGE EXACT MATCH TO USER REFERENCE SCREENSHOT ===== */
  .about-exact-page {
    background: #ffffff;
    border-radius: 24px;
    overflow: hidden;
    margin: 2rem auto;
  }

  /* 1. HERO & STATS SECTION */
  .hero-exact {
    padding: 60px 24px 40px;
    text-align: center;
    background: #ffffff;
  }

  .hero-exact-title {
    font-size: 40px;
    font-weight: 900;
    color: #0f172a;
    margin-bottom: 12px;
  }

  .hero-exact-title span {
    color: #EE1E26;
  }

  .hero-exact-subtitle {
    font-size: 16px;
    color: #64748b;
    margin-bottom: 48px;
    font-weight: 600;
  }

  .hero-stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    max-width: 900px;
    margin: 0 auto;
  }

  @media (max-width: 991px) {
    .hero-stats-row { grid-template-columns: repeat(2, 1fr); }
    .hero-exact-title { font-size: 30px; }
  }

  @media (max-width: 576px) {
    .hero-stats-row { grid-template-columns: repeat(2, 1fr); gap: 12px; }
  }

  .stat-exact-card {
    background: #ffffff;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    border-radius: 20px;
    padding: 24px 16px;
    text-align: center;
    transition: all 0.3s ease;
  }

  .stat-exact-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.06);
    border-color: #EE1E26;
  }

  .stat-exact-icon-wrap {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #fef2f2;
    color: #EE1E26;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin: 0 auto 12px;
  }

  .stat-exact-value {
    font-size: 28px;
    font-weight: 900;
    color: #0f172a;
    margin-bottom: 2px;
  }

  .stat-exact-label {
    font-size: 13.5px;
    color: #64748b;
    font-weight: 700;
  }

  /* 2. GALLERY BENTO GRID */
  .gallery-exact-section {
    padding: 60px 24px;
    background: #ffffff;
  }

  .section-exact-header {
    text-align: center;
    margin-bottom: 40px;
  }

  .section-exact-title {
    font-size: 32px;
    font-weight: 900;
    color: #0f172a;
  }

  .section-exact-title span {
    color: #EE1E26;
  }

  .bento-exact-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    max-width: 1140px;
    margin: 0 auto;
  }

  @media (max-width: 991px) {
    .bento-exact-grid { grid-template-columns: repeat(1, 1fr); }
  }

  .bento-exact-col {
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  .bento-exact-card {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 16px rgba(0,0,0,0.03);
    transition: all 0.3s ease;
  }

  .bento-exact-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.08);
  }

  .bento-exact-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.4s ease;
  }

  .bento-exact-card:hover img {
    transform: scale(1.05);
  }

  .bento-exact-logo-card {
    background: #0f172a;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 30px 20px;
    min-height: 180px;
    border: 1px solid #1e293b;
  }

  .bento-exact-logo-card img {
    max-width: 180px;
    max-height: 70px;
    object-fit: contain;
  }

  /* 3. PARTNERS SECTION */
  .partners-exact-section {
    padding: 50px 24px 70px;
    background: #ffffff;
  }

  .partners-exact-box {
    background: #f8fafc;
    border-radius: 24px;
    border: 1px solid #e2e8f0;
    padding: 40px 30px;
    max-width: 1140px;
    margin: 0 auto;
  }

  .partners-exact-grid {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 40px;
  }

  .partner-exact-logo-item {
    max-width: 130px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.85;
    transition: all 0.3s ease;
  }

  .partner-exact-logo-item:hover {
    opacity: 1;
    transform: scale(1.08);
  }

  .partner-exact-logo-item img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    filter: grayscale(20%);
    transition: filter 0.3s ease;
  }

  .partner-exact-logo-item:hover img {
    filter: grayscale(0%);
  }

  /* 4. CONTACT / LOCATION SECTION */
  .contact-exact-section {
    padding: 60px 24px;
    background: #ffffff;
  }

  .contact-exact-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    max-width: 1140px;
    margin: 0 auto 30px;
  }

  @media (max-width: 991px) {
    .contact-exact-grid { grid-template-columns: repeat(1, 1fr); }
  }

  .contact-exact-card {
    background: #ffffff;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    border-radius: 20px;
    padding: 30px 24px;
    text-align: center;
    transition: all 0.3s ease;
  }

  .contact-exact-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.06);
    border-color: #EE1E26;
  }

  .contact-exact-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #fef2f2;
    color: #EE1E26;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin: 0 auto 16px;
  }

  .contact-exact-card-title {
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 8px;
  }

  .contact-exact-card-info {
    font-size: 14px;
    color: #64748b;
    font-weight: 600;
    line-height: 1.6;
    margin: 0;
  }

  .contact-exact-map-wrap {
    max-width: 1140px;
    margin: 0 auto;
    border-radius: 24px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    box-shadow: 0 8px 24px rgba(0,0,0,0.04);
  }

  /* 5. TESTIMONIALS SECTION */
  .testimonials-exact-section {
    padding: 70px 24px;
    background: #ffffff;
  }
</style>
@endsection

@section('content')
@include('partials.Store.breadcrumb')

<div class="container p-0">
  <div class="about-exact-page border shadow-sm">

    <!-- 1. HERO & STATS SECTION -->
    <section class="hero-exact">
      <h1 class="hero-exact-title">
        {{ __('نحن لسنا مجرد') }} <span>{{ __('معرض سيارات') }}</span>
      </h1>
      <p class="hero-exact-subtitle">
        {{ __('نحن شركاؤك نجاحك في رحلة اختيار سيارة أحلامك.') }}
      </p>

      <!-- 4 HORIZONTAL STAT CARDS -->
      <div class="hero-stats-row">
        <div class="stat-exact-card">
          <div class="stat-exact-icon-wrap"><i class="bi bi-people-fill"></i></div>
          <div class="stat-exact-value">50K+</div>
          <div class="stat-exact-label">{{ __('عميل سعيد') }}</div>
        </div>

        <div class="stat-exact-card">
          <div class="stat-exact-icon-wrap"><i class="bi bi-grid-3x3-gap-fill"></i></div>
          <div class="stat-exact-value">200+</div>
          <div class="stat-exact-label">{{ __('علامة تجارية') }}</div>
        </div>

        <div class="stat-exact-card">
          <div class="stat-exact-icon-wrap"><i class="bi bi-hand-thumbs-up-fill"></i></div>
          <div class="stat-exact-value">98%</div>
          <div class="stat-exact-label">{{ __('رضا العملاء') }}</div>
        </div>

        <div class="stat-exact-card">
          <div class="stat-exact-icon-wrap"><i class="bi bi-trophy-fill"></i></div>
          <div class="stat-exact-value">10+</div>
          <div class="stat-exact-label">{{ __('سنة خبرة') }}</div>
        </div>
      </div>
    </section>

    <!-- 2. GALLERY BENTO GRID SECTION -->
    <section class="gallery-exact-section">
      <div class="section-exact-header">
        <h2 class="section-exact-title">{{ __('صور من') }} <span>{{ __('معرضنا') }}</span></h2>
      </div>

      <div class="bento-exact-grid">
        <!-- Column 1 (Left) -->
        <div class="bento-exact-col">
          <div class="bento-exact-card" style="height: 240px;">
            @if(isset($mainGallery[0]))
              <img loading="lazy" src="{{ asset('storage/' . $mainGallery[0]) }}" alt="Gallery Image 1" />
            @elseif(isset($bentoCars[0]))
              <img loading="lazy" src="{{ asset('storage/' . $bentoCars[0]->thumbnail) }}" alt="Car Image" />
            @else
              <img loading="lazy" src="{{ asset('assets/images/cars/car-1.jpg') }}" alt="Car Image" />
            @endif
          </div>
          <div class="bento-exact-card" style="height: 220px;">
            @if(isset($mainGallery[1]))
              <img loading="lazy" src="{{ asset('storage/' . $mainGallery[1]) }}" alt="Gallery Image 2" />
            @elseif(isset($bentoCars[1]))
              <img loading="lazy" src="{{ asset('storage/' . $bentoCars[1]->thumbnail) }}" alt="Car Image" />
            @else
              <img loading="lazy" src="{{ asset('assets/images/cars/car-2.jpg') }}" alt="Car Image" />
            @endif
          </div>
        </div>

        <!-- Column 2 (Center - Main Image & Black Logo Box) -->
        <div class="bento-exact-col">
          <div class="bento-exact-card" style="height: 290px;">
            @if(isset($mainGallery[2]))
              <img loading="lazy" src="{{ asset('storage/' . $mainGallery[2]) }}" alt="Gallery Main" />
            @elseif(isset($bentoCars[2]))
              <img loading="lazy" src="{{ asset('storage/' . $bentoCars[2]->thumbnail) }}" alt="Car Main" />
            @else
              <img loading="lazy" src="{{ asset('assets/images/cars/car-3.jpg') }}" alt="Car Main" />
            @endif
          </div>

          <!-- GR MOTORS BLACK LOGO CARD -->
          <div class="bento-exact-logo-card">
            <img src="{{ asset('assets/images/logo.png') }}" alt="GR Motors" />
          </div>
        </div>

        <!-- Column 3 (Right) -->
        <div class="bento-exact-col">
          <div class="bento-exact-card" style="height: 240px;">
            @if(isset($mainGallery[3]))
              <img loading="lazy" src="{{ asset('storage/' . $mainGallery[3]) }}" alt="Gallery Image 4" />
            @elseif(isset($bentoCars[3]))
              <img loading="lazy" src="{{ asset('storage/' . $bentoCars[3]->thumbnail) }}" alt="Car Image" />
            @else
              <img loading="lazy" src="{{ asset('assets/images/cars/car-4.jpg') }}" alt="Car Image" />
            @endif
          </div>
          <div class="bento-exact-card" style="height: 220px;">
            @if(isset($mainGallery[4]))
              <img loading="lazy" src="{{ asset('storage/' . $mainGallery[4]) }}" alt="Gallery Image 5" />
            @elseif(isset($bentoCars[4]))
              <img loading="lazy" src="{{ asset('storage/' . $bentoCars[4]->thumbnail) }}" alt="Car Image" />
            @else
              <img loading="lazy" src="{{ asset('assets/images/cars/car-5.jpg') }}" alt="Car Image" />
            @endif
          </div>
        </div>
      </div>
    </section>

    <!-- 3. PARTNERS & BANKS SECTION -->
    <section class="partners-exact-section">
      <div class="section-exact-header">
        <h2 class="section-exact-title">{{ __('شركاؤنا من') }} <span>{{ __('الشركات والبنوك') }}</span></h2>
      </div>

      <div class="partners-exact-box">
        <div class="partners-exact-grid">
          @if(isset($partners) && count($partners) > 0)
            @foreach($partners as $partner)
              <div class="partner-exact-logo-item">
                <img loading="lazy" src="{{ asset('storage/' . $partner->logo) }}" alt="{{ $partner->name }}" />
              </div>
            @endforeach
          @else
            <!-- Fallback Partners Logos -->
            <div class="partner-exact-logo-item"><img src="{{ asset('assets/images/partners/bsf.png') }}" alt="BSF Bank" /></div>
            <div class="partner-exact-logo-item"><img src="{{ asset('assets/images/partners/alinma.png') }}" alt="Alinma Bank" /></div>
            <div class="partner-exact-logo-item"><img src="{{ asset('assets/images/partners/rajhi.png') }}" alt="Al Rajhi Bank" /></div>
            <div class="partner-exact-logo-item"><img src="{{ asset('assets/images/partners/snb.png') }}" alt="SNB Bank" /></div>
            <div class="partner-exact-logo-item"><img src="{{ asset('assets/images/partners/emkan.png') }}" alt="Emkan Finance" /></div>
          @endif
        </div>
      </div>
    </section>

    <!-- 4. CONTACT / BRANCHES LOCATION SECTION (GRID 2) -->
    <section class="contact-exact-section">
      <div class="section-exact-header mb-4 text-center">
        <h2 class="section-exact-title">{{ __('أين يمكن أن') }} <span>{{ __('تجدنا — فروعنا') }}</span></h2>
        <p class="text-muted fs-15 mt-2">{{ __('يسعدنا استقبالكم في فروعنا بالمملكة العربية السعودية') }}</p>
      </div>

      <div class="branches-2col-grid">
        <!-- BRANCH 1: RIYADH -->
        <div class="branch-grid2-box">
          <div>
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="branch-grid2-icon" style="width: 48px; height: 48px; border-radius: 12px; color: #EE1E26; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">
                <i class="bi bi-geo-alt-fill"></i>
              </div>
              <div>
                <span class="badge bg-danger text-white mb-1" style="font-size: 11px;">{{ __('الفرع الرئيسي') }}</span>
                <h3 class="h5 fw-bold mb-0 text-dark">
                  {{ $globalSettings['branch_1_name_' . App::getLocale()] ?? ($globalSettings['branch_1_name_ar'] ?? __('فرع جدة — حي الجوهرة')) }}
                </h3>
              </div>
            </div>

            <div class="branch-grid2-info mb-3 fs-14">
              <div class="d-flex align-items-start gap-2 mb-2 text-muted">
                <i class="bi bi-pin-map text-danger mt-1 fs-5"></i>
                <span class="fw-semibold text-dark">
                  {{ $globalSettings['branch_1_address_' . App::getLocale()] ?? ($globalSettings['branch_1_address_ar'] ?? ($globalSettings['contact_address'] ?? __('جدة، حي الجوهرة، معارض السيارات، معرض جي آر'))) }}
                </span>
              </div>
              <div class="d-flex align-items-center gap-2 mb-2 text-muted">
                <i class="bi bi-telephone text-danger fs-5"></i>
                <a href="tel:{{ $globalSettings['branch_1_phone'] ?? ($globalSettings['contact_phone'] ?? '0549088126') }}" class="text-dark fw-bold text-decoration-none" dir="ltr">
                  {{ $globalSettings['branch_1_phone'] ?? ($globalSettings['contact_phone'] ?? '0549088126') }}
                </a>
              </div>
              <div class="d-flex align-items-center gap-2 text-muted">
                <i class="bi bi-clock text-danger fs-5"></i>
                <span>{{ __('السبت – الخميس: 9:00 ص – 10:00 م') }}</span>
              </div>
            </div>
          </div>

          <!-- Map Iframe Branch 1 -->
          <div class="branch-grid2-map-wrap">
            <iframe
              src="{{ $globalSettings['branch_1_map'] ?? ('https://maps.google.com/maps?q=' . rawurlencode(__('معارض السيارات الجوهرة جدة')) . '&z=16&output=embed') }}"
              allowfullscreen=""
              loading="lazy"
              title="{{ __('فرع جدة') }}">
            </iframe>
          </div>
        </div>

        <!-- BRANCH 2: MAKKAH - WALYAL AHD DIST -->
        <div class="branch-grid2-box">
          <div>
            <div class="d-flex align-items-center gap-3 mb-3">

              <div>
                <span class="badge bg-primary text-white mb-1" style="font-size: 11px; color: white;">{{ __('فرع مكة المكرمة') }}</span>
                <h3 class="h5 fw-bold mb-0 text-dark">
                  {{ $globalSettings['branch_2_name_' . App::getLocale()] ?? ($globalSettings['branch_2_name_ar'] ?? __('فرع مكة المكرمة — حي ولي العهد')) }}
                </h3>
              </div>
            </div>

            <div class="branch-grid2-info mb-3 fs-14">
              <div class="d-flex align-items-start gap-2 mb-2 text-muted">
                <i class="bi bi-pin-map text-primary mt-1 fs-5"></i>
                <span class="fw-semibold text-dark">
                  @if(App::getLocale() == 'en')
                    {{ $globalSettings['branch_2_address_en'] ?? 'Ibrahim Al-Khalil Road, Walyal Ahd Dist., Makkah, Saudi Arabia' }}
                  @else
                    {{ $globalSettings['branch_2_address_ar'] ?? __('مكة المكرمة، حي ولي العهد، طريق إبراهيم الخليل') }}
                  @endif
                </span>
              </div>
              <div class="d-flex align-items-center gap-2 mb-2 text-muted">
                <i class="bi bi-telephone text-primary fs-5"></i>
                <a href="tel:{{ $globalSettings['branch_2_phone'] ?? '0546376229' }}" class="text-dark fw-bold text-decoration-none" dir="ltr">
                  {{ $globalSettings['branch_2_phone'] ?? '0546376229' }}
                </a>
              </div>
              <div class="d-flex align-items-center gap-2 text-muted">
                <i class="bi bi-clock text-primary fs-5"></i>
                <span>{{ __('السبت – الخميس: 9:00 ص – 10:00 م') }}</span>
              </div>
            </div>
          </div>

          <!-- Map Iframe Branch 2 -->
          <div class="branch-grid2-map-wrap">
            <iframe
              src="{{ $globalSettings['branch_2_map'] ?? 'https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3715.258814761406!2d39.8199462890625!3d21.274839401245117!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMjHCsDE2JzI5LjQiTiAzOcKwNDknMTEuOCJF!5e0!3m2!1sar!2ssa!4v1700000000000!5m2!1sar!2ssa' }}"
              allowfullscreen=""
              loading="lazy"
              title="{{ __('فرع مكة المكرمة') }}">
            </iframe>
          </div>
        </div>
      </div>
    </section>
    <style>
    /* ===== FORCED GRID 2 BRANCHES CSS ===== */
    .branches-2col-grid {
        display: grid !important;
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 24px !important;
        max-width: 1140px !important;
        margin: 0 auto !important;
        width: 100% !important;
    }

    @media (max-width: 991px) {
        .branches-2col-grid {
            grid-template-columns: repeat(1, 1fr) !important;
        }
    }

    .branch-grid2-box {
        background: #ffffff !important;
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 20px !important;
        padding: 24px !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        position: relative !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03) !important;
        width: 100% !important;
        box-sizing: border-box !important;
        min-height: 480px !important;
    }

    .branch-grid2-map-wrap {
        position: relative !important;
        width: 100% !important;
        height: 240px !important;
        border-radius: 16px !important;
        overflow: hidden !important;
        margin-top: 16px !important;
        border: 1px solid #e2e8f0 !important;
        clear: both !important;
    }

    .branch-grid2-map-wrap iframe {
        position: relative !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        border: 0 !important;
        display: block !important;
        float: none !important;
    }
    </style>

    <!-- 5. TESTIMONIALS SECTION -->
    <section class="testimonials-exact-section border-top">
      <div class="testimonials__container">
        <div class="text-center mb-60" style="padding-bottom: 2rem;">
          <span class="premium-badge mb-16">{{ __('آراء عملاؤنا') }}</span>
          <h2 class="section-title-premium mb-16">{{ __('ماذا يقول') }} <span>{{ __('عملاؤنا السعداء') }}</span></h2>
          <p class="text-muted fs-18 fw-700 max-w-600 mx-auto">{{ __('اكتشف تجارب عملاؤنا المميزة وآراءهم حول خدماتنا وسياراتنا الفاخرة') }}</p>
        </div>

        <div class="position-relative px-lg-60">
          <div class="swiper testimonialsSwiper">
            <div class="swiper-wrapper">
              @foreach($testimonials as $testimonial)
                <div class="swiper-slide h-auto">
                  <div class="testimonial-premium-card-v2 h-100">
                    @if($testimonial->review_image)
                      <div class="testimonial-review-img-wrap">
                        <img loading="lazy" src="{{ asset('storage/' . $testimonial->review_image) }}" alt="Review Screenshot" class="testimonial-review-img">
                      </div>
                    @endif

                    <div class="testimonial-footer-v2">
                      <div class="author-avatar-v2">
                        @if($testimonial->image)
                          <img loading="lazy" src="{{ asset('storage/' . $testimonial->image) }}" alt="{{ $testimonial->name }}">
                        @else
                          <img loading="lazy" src="{{ asset('assets/images/default-avatar.jpg') }}" alt="{{ $testimonial->name }}">
                        @endif
                      </div>

                      <div class="author-info-v2">
                        <div class="author-name-wrap">
                          <h4 class="author-name-v2">{{ $testimonial->name }}</h4>
                          <span class="status-dot"></span>
                        </div>
                        <div class="testimonial-meta-v2">
                          <span>{{ number_format($testimonial->rating ?? 5.0, 1) }}</span>
                          <i class="bi bi-star-fill"></i>
                          <span class="ms-2">{{ $testimonial->title }}</span>
                        </div>
                        <p class="testimonial-car-model-v2">{{ $testimonial->content }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>

          {{-- Custom Navigation --}}
          <div class="swiper-button-next-testimonials swiper-nav-custom d-none d-lg-flex">
            <i class="bi bi-chevron-{{ App::getLocale() == 'ar' ? 'left' : 'right' }}"></i>
          </div>
          <div class="swiper-button-prev-testimonials swiper-nav-custom d-none d-lg-flex">
            <i class="bi bi-chevron-{{ App::getLocale() == 'ar' ? 'right' : 'left' }}"></i>
          </div>

          {{-- Pagination --}}
          <div class="swiper-pagination-testimonials mt-40 d-flex justify-content-center gap-2"></div>
        </div>
      </div>
    </section>

  </div>
</div>
@endsection

@section('js')
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const testimonialsSwiper = new Swiper('.testimonialsSwiper', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: '.swiper-button-next-testimonials',
                prevEl: '.swiper-button-prev-testimonials',
            },
            pagination: {
                el: '.swiper-pagination-testimonials',
                clickable: true,
                bulletClass: 'swiper-pagination-bullet-custom',
                bulletActiveClass: 'swiper-pagination-bullet-custom-active',
            },
            breakpoints: {
                768: {
                    slidesPerView: 2,
                },
                1200: {
                    slidesPerView: 3,
                },
            },
        });
    });
  </script>
@endsection
