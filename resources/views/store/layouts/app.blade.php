<!DOCTYPE html>
@php
    $globalSettings = app(\App\Services\Cache\BaseCacheService::class)->rememberSettings();
    $socialMedia = $globalSettings['social_media'] ?? [];
    if (!is_array($socialMedia) && is_string($socialMedia)) {
        $socialMedia = json_decode($socialMedia, true) ?: [];
    }
    $googleAnalyticsId = $globalSettings['google_analytics_id'] ?? '';
    $metaPixelId = $globalSettings['meta_pixel_id'] ?? '';

    // Promo Popup
    $promoPopup = $globalSettings['promo_popup'] ?? [];
    if (!is_array($promoPopup) && is_string($promoPopup)) {
        $promoPopup = json_decode($promoPopup, true) ?: [];
    }
    $popupEnabled = !empty($promoPopup['enabled']);

    // Cookie Consent
    $cookieEnabled = ($globalSettings['cookie_consent_enabled'] ?? '0') == '1';
    $cookieText = App::getLocale() == 'ar'
        ? ($globalSettings['cookie_consent_text_ar'] ?? __('نستخدم ملفات الكوكيز لتحسين تجربتك على موقعنا. بمواصلة التصفح، فإنك توافق على استخدامنا لها.'))
        : ($globalSettings['cookie_consent_text_en'] ?? 'We use cookies to improve your experience on our website. By continuing to browse, you agree to our use of cookies.');
    $cookieLink = $globalSettings['cookie_consent_link'] ?? '';
@endphp
<html lang="{{ App::getLocale() }}" dir="{{ App::getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $siteName = $globalSettings['site_name'] ?? 'GR Motors';
        if (is_array($siteName)) {
            $siteName = $siteName[App::getLocale()] ?? ($siteName['ar'] ?? 'GR Motors');
        }
        $metaTitle = $globalSettings['meta_title'] ?? $siteName;
        $metaDescription = $globalSettings['meta_description'] ?? __('GR Motors') . ' - ' . __('معرض سيارات فاخر');
        $metaKeywords = $globalSettings['meta_keywords'] ?? '';
    @endphp
    <title>@yield('title', $metaTitle)</title>
    <meta name="description" content="@yield('meta_description', $metaDescription)">
    @if($metaKeywords)
    <meta name="keywords" content="{{ $metaKeywords }}">
    @endif

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', $metaTitle)">
    <meta property="og:description" content="@yield('meta_description', $metaDescription)">
    <meta property="og:image" content="@yield('og_image', isset($globalSettings['site_logo']) ? asset('storage/' . $globalSettings['site_logo']) : asset('assets/images/logo.png'))">

    {{-- Twitter --}}
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('title', $metaTitle)">
    <meta property="twitter:description" content="@yield('meta_description', $metaDescription)">
    <meta property="twitter:image" content="@yield('og_image', isset($globalSettings['site_logo']) ? asset('storage/' . $globalSettings['site_logo']) : asset('assets/images/logo.png'))">

    {{-- Schema.org Structured Data --}}
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "AutoDealer",
      "name": "{{ $siteName }}",
      "url": "{{ url('/') }}",
      "logo": "{{ isset($globalSettings['site_logo']) ? asset('storage/' . $globalSettings['site_logo']) : asset('assets/images/logo.png') }}",
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "{{ $globalSettings['contact_phone'] ?? '+966558890899' }}",
        "contactType": "sales",
        "areaServed": "SA",
        "availableLanguage": ["ar", "en"]
      },
      "sameAs": [
        "https://wa.me/966558890899",
        "https://www.instagram.com/gr_motors50/",
        "https://www.tiktok.com/@gr_motors50",
        "https://www.facebook.com/profile.php?id=61579469071719",
        "https://x.com/gr_motors50",
        "https://www.snapchat.com/add/gr_motors50?share_id=SX2dcF4Hi94&locale=en-US"
      ]
    }
    </script>

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ isset($globalSettings['site_favicon']) && $globalSettings['site_favicon'] ? asset('storage/' . $globalSettings['site_favicon']) : asset('assets/images/k_favicon_32x.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('store.css') }}">

    @php
        $siteFont = $globalSettings['site_font'] ?? 'default';
    @endphp
    @if($siteFont !== 'default')
    <style>
        @font-face {
            font-family: 'Bahij-SemiBold';
            src: url('{{ asset("assets/fonts/Bahij_TheSansArabic-SemiBold.ttf") }}') format('truetype'),
                 url('{{ asset("fonts/Bahij_TheSansArabic-SemiBold.ttf") }}') format('truetype');
            font-weight: 600;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Bahij-Plain';
            src: url('{{ asset("assets/fonts/Bahij_TheSansArabic-Plain.ttf") }}') format('truetype'),
                 url('{{ asset("fonts/Bahij_TheSansArabic-Plain.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        :root {
            @if($siteFont === 'bahij_semibold')
                --font: 'Bahij-SemiBold', 'Cairo', sans-serif !important;
                --font-primary: 'Bahij-SemiBold', 'Cairo', sans-serif !important;
                --font-main: 'Bahij-SemiBold', 'Cairo', sans-serif !important;
            @elseif($siteFont === 'bahij_plain')
                --font: 'Bahij-Plain', 'Cairo', sans-serif !important;
                --font-primary: 'Bahij-Plain', 'Cairo', sans-serif !important;
                --font-main: 'Bahij-Plain', 'Cairo', sans-serif !important;
            @endif
        }

        body, html, button, input, select, textarea, p, h1, h2, h3, h4, h5, h6, span, a, div {
            @if($siteFont === 'bahij_semibold')
                font-family: 'Bahij-SemiBold', 'Cairo', sans-serif !important;
            @elseif($siteFont === 'bahij_plain')
                font-family: 'Bahij-Plain', 'Cairo', sans-serif !important;
            @endif
        }
    </style>
    @endif

    @if($googleAnalyticsId)
    {{-- Google Analytics (GA4) --}}
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleAnalyticsId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $googleAnalyticsId }}');
    </script>
    @endif

    @yield('css')

    <style>
    /* Custom Header Dropdown */
    .nav-item-dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-toggle-nav {
        cursor: pointer;
        display: flex !important;
        align-items: center;
        gap: 4px;
        text-decoration: none;
    }

    .nav-dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        z-index: 1000;
        min-width: 220px;
        padding: 12px 0;
        margin: 10px 0 0;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 14px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        
        /* Smooth fade-out delay on mouse leave */
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s ease;
        transition-delay: 0.15s;
    }

    /* Transparent bridge to prevent losing hover in the margin gap */
    .nav-dropdown-menu::before {
        content: '';
        position: absolute;
        top: -15px;
        left: 0;
        right: 0;
        height: 15px;
        background: transparent;
    }

    /* Direction adjustment for English and Arabic */
    html[lang="en"] .nav-dropdown-menu {
        right: auto;
        left: 0;
    }

    .nav-dropdown-menu a {
        display: flex !important;
        align-items: center;
        width: 100%;
        padding: 12px 24px !important;
        clear: both;
        font-weight: 700 !important;
        font-size: 15px !important;
        color: var(--color-text, #333) !important;
        text-align: inherit;
        text-decoration: none;
        white-space: nowrap;
        background-color: transparent;
        border: none;
        transition: all 0.25s ease;
    }

    .nav-dropdown-menu a:hover {
        background-color: rgba(238, 30, 38, 0.04);
        color: var(--primary) !important;
        padding-right: 30px !important;
    }

    html[lang="en"] .nav-dropdown-menu a:hover {
        padding-right: 24px !important;
        padding-left: 30px !important;
    }

    /* Hover show on Desktop */
    @media (min-width: 992px) {
        .nav-item-dropdown:hover .nav-dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            transition-delay: 0s; /* Open instantly on hover */
        }
    }

    /* Mobile Submenu Styling */
    @media (max-width: 991px) {
        .nav-item-dropdown {
            width: 100%;
            display: block;
        }
        .dropdown-toggle-nav {
            padding: 16px 24px !important;
            width: 100%;
            justify-content: space-between;
            border-right: 4px solid transparent;
        }
        .nav-dropdown-menu {
            position: static;
            display: none;
            box-shadow: none;
            border: none;
            background-color: #fbfbfb;
            margin: 0;
            padding: 0;
            border-radius: 0;
            width: 100%;
            border-right: 4px solid rgba(238, 30, 38, 0.08);
            opacity: 1 !important;
            visibility: visible !important;
            transform: none !important;
            transition: none !important;
        }
        .nav-dropdown-menu a {
            padding: 12px 35px !important;
            font-size: 15px !important;
        }
        .nav-dropdown-menu.show {
            display: block !important;
        }
        .dropdown-toggle-nav.active-toggle {
            color: var(--primary) !important;
            border-right-color: var(--primary) !important;
        }
    }

    @keyframes fadeInDropdown {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    </style>
</head>

<body class="{{ App::getLocale() == 'en' ? 'ltr' : '' }}">

    @if($metaPixelId)
    {{-- Meta Pixel (Facebook) --}}
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $metaPixelId }}');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id={{ $metaPixelId }}&ev=PageView&noscript=1"
    /></noscript>
    @endif

    {{-- Top Bar --}}
    <div class="top-bar">
        <div class="container top-bar-inner">
            <div class="top-bar-contact d-flex align-items-center gap-3">
                @if($contactPhone = $globalSettings['contact_phone'] ?? null)
                <a href="tel:{{ $contactPhone }}"><i class="bi bi-telephone"></i> {{ $contactPhone }}</a>
                @endif
                @if($contactEmail = $globalSettings['contact_email'] ?? null)
                <a href="mailto:{{ $contactEmail }}"><i class="bi bi-envelope"></i> {{ $contactEmail }}</a>
                @endif
                @php
                    $altLang = App::getLocale() === 'ar' ? 'en' : 'ar';
                    $altLangText = App::getLocale() === 'ar' ? 'English' : 'العربية';
                @endphp
                <a href="{{ route('lang.switch', $altLang) }}" class="lang-switch ms-3">
                    <i class="bi bi-globe"></i> {{ $altLangText }}
                </a>
            </div>
            @if($contactAddress = $globalSettings['contact_address'] ?? null)
            <div class="top-bar-location">
                <span><i class="bi bi-geo-alt"></i> {{ $contactAddress }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Main Header --}}
    <header id="site-header">
        <div class="container header-inner">

            {{-- Call To Action --}}
            <div class="d-flex align-items-center gap-2">
                @auth('web')
                    <a href="{{ route('store.account.orders') }}" class="btn btn-outline-danger me-2 d-inline-flex align-items-center gap-1" style="border-radius: 20px; padding: 6px 16px; font-weight: 700; border-color: #ED1C24;">
                        <i class="bi bi-person-circle"></i>
                    </a>
                @else
                    <a href="{{ route('store.auth.login') }}" class="btn btn-outline-danger me-2 d-inline-flex align-items-center gap-1" style="border-radius: 20px; padding: 6px 16px; font-weight: 700; border-color: #ED1C24;">
                        <i class="bi bi-person"></i> 
                    </a>
                @endauth

                @if($contactPhone = $globalSettings['contact_phone'] ?? null)
                <a href="tel:{{ $contactPhone }}" class="btn-call-now">
                    {{ __('اتصل الآن') }} <i class="bi bi-telephone"></i>
                </a>
                @endif
            </div>

            {{-- Main Navigation --}}
            <nav class="site-nav" id="siteNav">
                {{-- Sidebar Header: Logo & Close --}}
                <div class="sidebar-header d-lg-none">
                    <div class="sidebar-logo">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
                    </div>
                    <button class="close-nav-btn" onclick="closeNav()">&times;</button>
                </div>

                {{-- Navigation Links --}}
                <div class="sidebar-links">
                    <a href="{{ route('store.home') }}" class="{{ request()->routeIs('store.home') ? 'active' : '' }}">{{ __('الرئيسية') }}</a>
                    <a href="{{ route('store.cars.index') }}" class="{{ request()->routeIs('store.cars.index') ? 'active' : '' }}">{{ __('السيارات') }}</a>
                    <a href="{{ route('store.cars.coming-soon') }}" class="{{ request()->routeIs('store.cars.coming-soon') ? 'active' : '' }}">{{ __('قريباً في السوق') }}</a>
                    <a href="{{ route('store.offers.index') }}" class="{{ request()->routeIs('store.offers.index') ? 'active' : '' }}">{{ __('الإعلانات') }}</a>
                    {{-- Dropdown Submenu under About Us --}}
                    <div class="nav-item-dropdown">
                        <a href="{{ route('store.about') }}" class="dropdown-toggle-nav {{ request()->routeIs('store.about') || request()->routeIs('store.faq') ? 'active' : '' }}" id="aboutDropdownBtn">
                            {{ __('من نحن') }} <i class="bi bi-chevron-down ms-1"></i>
                        </a>
                        <div class="nav-dropdown-menu" id="aboutDropdownMenu">
                            <a href="{{ route('store.about') }}" class="{{ request()->routeIs('store.about') ? 'active' : '' }}">
                                {{ __('من نحن') }}
                            </a>
                            @php
                                $locale = App::getLocale();
                                $portfolioPdfAr = $globalSettings['portfolio_pdf_ar'] ?? null;
                                $portfolioPdfEn = $globalSettings['portfolio_pdf_en'] ?? null;
                                // Fallback to old URL links if PDF not uploaded yet
                                $portfolioLinkAr = $globalSettings['portfolio_link_ar'] ?? null;
                                $portfolioLinkEn = $globalSettings['portfolio_link_en'] ?? null;

                                $portfolioUrl = null;
                                if ($locale === 'ar') {
                                    $portfolioUrl = $portfolioPdfAr
                                        ? Storage::disk('public')->url($portfolioPdfAr)
                                        : $portfolioLinkAr;
                                } else {
                                    $portfolioUrl = $portfolioPdfEn
                                        ? Storage::disk('public')->url($portfolioPdfEn)
                                        : $portfolioLinkEn;
                                }
                            @endphp
                            @if($portfolioUrl)
                            <a href="{{ $portfolioUrl }}" target="_blank">
                                {{ __('البرتفوليو') }}
                            </a>
                            @endif
                            <a href="{{ route('store.faq') }}" class="{{ request()->routeIs('store.faq') ? 'active' : '' }}">
                                {{ __('الأسئلة الشائعة') }}
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('store.blog.index') }}" class="{{ request()->routeIs('store.blog.index') ? 'active' : '' }}">{{ __('المقالات') }}</a>
                    <a href="{{ route('store.calculator') }}" class="{{ request()->routeIs('store.calculator') ? 'active' : '' }} text-primary fw-bold">{{ __('الحاسبة') }}</a>
                </div>

                {{-- Sidebar Footer: Language & Social --}}
                <div class="sidebar-footer d-lg-none">
                    <div class="sidebar-section-title">{{ __('اللغة') }}</div>
                    <div class="lang-switch-wrapper">
                        <div class="lang-switch-pill">
                            <a href="{{ route('lang.switch', 'ar') }}" class="{{ App::getLocale() == 'ar' ? 'active' : '' }}">العربية</a>
                            <a href="{{ route('lang.switch', 'en') }}" class="{{ App::getLocale() == 'en' ? 'active' : '' }}">English</a>
                            <div class="lang-switch-bg {{ App::getLocale() == 'en' ? 'slide' : '' }}"></div>
                        </div>
                    </div>

                    <div class="sidebar-section-title">{{ __('تابعنا') }}</div>
                    <div class="sidebar-social">
                        @if(isset($socialMedia) && count($socialMedia) > 0)
                            @foreach($socialMedia as $social)
                                <a href="{{ $social['link'] ?? '#' }}" target="_blank" class="social-item" style="color: {{ $social['color'] ?? '#fff' }}">
                                    <i class="{{ $social['icon'] }}"></i>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </nav>

            {{-- Mobile Menu Toggle --}}
            <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Menu">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>

            {{-- Logo --}}
            <a href="{{ route('store.home') }}" class="site-logo">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
            </a>

        </div>
    </header>

    {{-- Mobile Nav Overlay --}}
    <div class="mob-nav-overlay" id="mobNavOverlay"></div>

    {{-- Main Content --}}
    <main id="main-content">
        @yield('content')
    </main>

    {{-- Newsletter Section Redesigned --}}
    <section class="newsletter-premium-section">
        <div class="container">
            <div class="newsletter-inner-wrap">
                <div class="newsletter-text-side">
                    <h3 class="newsletter-title">
                        {{ __('اشترك في') }} <br>
                        <span class="text-primary" style="background: linear-gradient(270deg, #FD7277 0%, #ED1C24 100%);background-clip: text;-webkit-background-clip: text;-webkit-text-fill-color: transparent;">{{ __('نشرتنا الإخبارية') }}</span>
                    </h3>
                    <p class="newsletter-subtitle">{{ __('احصل على آخر العروض والتحديثات مباشرة في بريدك الإلكتروني') }}</p>
                </div>
                <div class="newsletter-form-side">
                    <form class="premium-newsletter-form" id="newsletterForm" novalidate>
                        @csrf
                        <input type="email" name="email" id="newsletterEmail" class="form-control" placeholder="{{ __('أدخل بريدك الإلكتروني') }}" required>
                        <button type="submit" class="btn-newsletter-submit" id="newsletterBtn">
                             {{ __('اشتراك') }} <i class="bi bi-send-fill ms-2"></i>
                        </button>
                    </form>
                    <div id="newsletterMsg" style="display:none; margin-top:10px; font-size:13px; font-weight:700; padding:8px 14px; border-radius:10px;"></div>
                </div>
            </div>
        </div>
        <div class="newsletter-glow"></div>
    </section>

    <script>
    (function() {
        const form    = document.getElementById('newsletterForm');
        const msgBox  = document.getElementById('newsletterMsg');
        const btn     = document.getElementById('newsletterBtn');
        if (!form) return;

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const email = document.getElementById('newsletterEmail').value.trim();
            if (!email) return;

            btn.disabled = true;
            btn.innerHTML = '<span style="opacity:0.7">{{ __("جاري الإرسال...") }}</span>';

            try {
                const res = await fetch('{{ route("store.newsletter.subscribe") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email }),
                });

                const data = await res.json();

                msgBox.style.display = 'block';
                if (data.success) {
                    msgBox.style.background = 'rgba(0,201,80,0.12)';
                    msgBox.style.color = '#007a36';
                    msgBox.style.border = '1px solid rgba(0,201,80,0.25)';
                    msgBox.textContent = data.message;
                    form.reset();
                    btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> {{ __("تم الاشتراك") }}';
                    btn.style.background = '#16a34a';
                    trackEvent('NewsletterSignup', { email: email });
                } else {
                    msgBox.style.background = 'rgba(237,28,36,0.08)';
                    msgBox.style.color = '#C0152A';
                    msgBox.style.border = '1px solid rgba(237,28,36,0.2)';
                    msgBox.textContent = data.message;
                    btn.disabled = false;
                    btn.innerHTML = '{{ __("اشتراك") }} <i class="bi bi-send-fill ms-2"></i>';
                }

                setTimeout(() => { msgBox.style.display = 'none'; }, 5000);
            } catch (err) {
                msgBox.style.display = 'block';
                msgBox.style.background = 'rgba(237,28,36,0.08)';
                msgBox.style.color = '#C0152A';
                msgBox.textContent = '{{ __("حدث خطأ، يرجى المحاولة مجدداً") }}';
                btn.disabled = false;
                btn.innerHTML = '{{ __("اشتراك") }} <i class="bi bi-send-fill ms-2"></i>';
            }
        });
    })();
    </script>


    {{-- Site Footer Redesigned --}}
    <footer id="site-footer-premium">
        <div class="container">
            <div class="footer-premium-grid">

                {{-- Column 1: Brand & Social --}}
                <div class="footer-premium-col brand-col">
                    <a href="{{ route('store.home') }}" class="footer-logo mb-24">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
                    </a>
                    <p class="footer-desc mb-32">
                        {{ __('وجهتك الأولى للحصول على أفخم وأرقى السيارات في العالم. نقدم لك تجربة استثنائية مع خدمة متميزة.') }}
                    </p>
                    @if(isset($socialMedia) && count($socialMedia) > 0)
                    <div class="social-premium-wrap">
                        @foreach($socialMedia as $social)
                            <a href="{{ $social['link'] ?? '#' }}" target="_blank" class="social-premium-item">
                                <i class="{{ $social['icon'] }}"></i>
                            </a>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Column 2: Quick Links --}}
                <div class="footer-premium-col">
                    <h4 class="footer-title-premium">{{ __('روابط سريعة') }}</h4>
                    <ul class="footer-links-premium">
                        <li><a href="{{ route('store.home') }}">{{ __('الرئيسية') }} <i class="bi bi-arrow-{{ App::getLocale()=='ar' ? 'left' : 'right' }}"></i></a></li>
                        <li><a href="{{ route('store.cars.index') }}">{{ __('السيارات') }} <i class="bi bi-arrow-{{ App::getLocale()=='ar' ? 'left' : 'right' }}"></i></a></li>
                        <li><a href="{{ route('store.offers.index') }}">{{ __('الإعلانات') }} <i class="bi bi-arrow-{{ App::getLocale()=='ar' ? 'left' : 'right' }}"></i></a></li>
                        <li><a href="#">{{ __('آراء العملاء') }} <i class="bi bi-arrow-{{ App::getLocale()=='ar' ? 'left' : 'right' }}"></i></a></li>
                        <li><a href="{{ route('store.careers.create') }}">{{ __('انضم لفريق وظائف') }} <i class="bi bi-arrow-{{ App::getLocale()=='ar' ? 'left' : 'right' }}"></i></a></li>
                    </ul>
                </div>

                {{-- Column 3: Services --}}
                <div class="footer-premium-col">
                    <h4 class="footer-title-premium">{{ __('خدماتنا') }}</h4>
                    <ul class="footer-services-premium">
                        <li><span class="dot"></span> {{ __('بيع السيارات الفاخرة') }}</li>
                        <li><span class="dot"></span> {{ __('استبدال السيارات') }}</li>
                        <li><span class="dot"></span> {{ __('تمويل سيارات') }}</li>
                        <li><span class="dot"></span> {{ __('صيانة دورية') }}</li>
                        <li><span class="dot"></span> {{ __('خدمة ما بعد البيع') }}</li>
                        <li><span class="dot"></span> {{ __('توصيل السيارة') }}</li>
                    </ul>
                </div>

                {{-- Column 4: Branches & Contact --}}
                <div class="footer-premium-col contact-col">
                    <h4 class="footer-title-premium">{{ __('فروعنا وتواصلنا') }}</h4>
                    <div class="contact-premium-list">
                        {{-- Branch 1: Jeddah --}}
                        <div class="footer-branch-item">
                            <div class="footer-branch-name">
                                <span class="dot"></span>
                                {{ $globalSettings['branch_1_name_' . App::getLocale()] ?? ($globalSettings['branch_1_name_ar'] ?? __('فرع جدة — حي الجوهرة')) }}
                                <span class="footer-branch-tag">{{ __('الفرع الرئيسي') }}</span>
                            </div>
                            <div class="footer-branch-sub">
                                <span>{{ $globalSettings['branch_1_address_' . App::getLocale()] ?? ($globalSettings['branch_1_address_ar'] ?? ($globalSettings['contact_address'] ?? __('جدة، حي الجوهرة، معارض السيارات، معرض جي آر'))) }}</span>
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <div class="footer-branch-sub">
                                <a href="tel:{{ $globalSettings['branch_1_phone'] ?? ($globalSettings['contact_phone'] ?? '0549088126') }}" dir="ltr">
                                    {{ $globalSettings['branch_1_phone'] ?? ($globalSettings['contact_phone'] ?? '0549088126') }}
                                </a>
                                <i class="bi bi-telephone"></i>
                            </div>
                            <div class="footer-branch-sub">
                                <span>{{ __('السبت – الخميس: 9:00 ص – 10:00 م') }}</span>
                                <i class="bi bi-clock"></i>
                            </div>
                        </div>

                        {{-- Branch 2: Makkah --}}
                        <div class="footer-branch-item">
                            <div class="footer-branch-name">
                                <span class="dot"></span>
                                {{ $globalSettings['branch_2_name_' . App::getLocale()] ?? ($globalSettings['branch_2_name_ar'] ?? __('فرع مكة المكرمة — حي ولي العهد')) }}
                                <span class="footer-branch-tag">{{ __('فرع مكة المكرمة') }}</span>
                            </div>
                            <div class="footer-branch-sub">
                                <span>@if(App::getLocale() == 'en')
                                    {{ $globalSettings['branch_2_address_en'] ?? 'Ibrahim Al-Khalil Road, Walyal Ahd Dist., Makkah, Saudi Arabia' }}
                                @else
                                    {{ $globalSettings['branch_2_address_ar'] ?? __('مكة المكرمة، حي ولي العهد، طريق إبراهيم الخليل') }}
                                @endif</span>
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <div class="footer-branch-sub">
                                <a href="tel:{{ $globalSettings['branch_2_phone'] ?? '0546376229' }}" dir="ltr">
                                    {{ $globalSettings['branch_2_phone'] ?? '0546376229' }}
                                </a>
                                <i class="bi bi-telephone"></i>
                            </div>
                            <div class="footer-branch-sub">
                                <span>{{ __('السبت – الخميس: 9:00 ص – 10:00 م') }}</span>
                                <i class="bi bi-clock"></i>
                            </div>
                        </div>

                        {{-- Email & WhatsApp --}}
                        @if($contactWhatsApp = $globalSettings['contact_whatsapp'] ?? null)
                        <a href="https://wa.me/{{ $contactWhatsApp }}" target="_blank" class="contact-premium-item" style="text-decoration:none;color:inherit;">
                            <div class="cp-info">
                                <span class="cp-label">{{ __('واتساب الخدمة الموحد') }}</span>
                                <strong dir="ltr">{{ $contactWhatsApp }}</strong>
                            </div>
                            <div class="cp-icon" style="color:#25D366;"><i class="bi bi-whatsapp"></i></div>
                        </a>
                        @endif
                    </div>
                </div>

            </div>

            <div class="footer-bottom-premium">
                <div class="fb-links">
                    <a href="#">{{ __('سياسة الخصوصية') }}</a>
                    <a href="#">{{ __('الشروط والأحكام') }}</a>
                </div>
                <div class="fb-copy">
                    &copy; {{ date('Y') }} GR Motors. {{ __('جميع الحقوق محفوظة.') }}
                </div>
            </div>
        </div>
    </footer>

    @if($whatsappNumber = $globalSettings['contact_whatsapp'] ?? null)
    {{-- WhatsApp Widget --}}
    <a href="https://wa.me/{{ $whatsappNumber }}" class="whatsapp-widget" target="_blank" rel="noopener noreferrer">
        <i class="bi bi-whatsapp"></i>
    </a>
    @endif

    {{-- Cookie Consent Banner --}}
    @if($cookieEnabled)
    <div id="cookie-consent-bar" style="
        display: none;
        position: fixed;
        bottom: 0; left: 0; right: 0;
        z-index: 99999;
        background: rgba(15,15,18,0.97);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-top: 1px solid rgba(255,255,255,0.08);
        padding: 16px 24px;
        color: #fff;
        font-family: 'Cairo', sans-serif;
        animation: slideUpBar 0.4s ease both;
    ">
        <div style="max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:14px;flex:1;min-width:200px;">
                <i class="bi bi-cookie" style="font-size:24px;color: #EE1E26;flex-shrink:0;"></i>
                <p style="margin:0;font-size:13.5px;line-height:1.6;color:rgba(255,255,255,0.8);">{{ $cookieText }}
                    @if($cookieLink)
                    <a href="{{ $cookieLink }}" target="_blank" style="color: #EE1E26;text-decoration:none;font-weight:700;margin-{{ App::getLocale()=='ar' ? 'right' : 'left' }}:6px;">{{ __('معرفة المزيد') }}</a>
                    @endif
                </p>
            </div>
            <div style="display:flex;gap:10px;flex-shrink:0;">
                <button onclick="rejectCookies()" style="padding:9px 20px;border-radius:100px;border:1px solid rgba(255,255,255,0.2);background:transparent;color:rgba(255,255,255,0.65);font-size:13px;font-weight:600;font-family:inherit;cursor:pointer;transition:all 0.25s;" onmouseover="this.style.borderColor='rgba(255,255,255,0.4)';this.style.color='#fff';" onmouseout="this.style.borderColor='rgba(255,255,255,0.2)';this.style.color='rgba(255,255,255,0.65)';">{{ __('رفض') }}</button>
                <button onclick="acceptCookies()" style="padding:9px 24px;border-radius:100px;border:none;background:linear-gradient(135deg,#EE1E26,#c0141a);color:#fff;font-size:13px;font-weight:700;font-family:inherit;cursor:pointer;transition:opacity 0.25s;box-shadow:0 4px 16px rgba(238,30,38,0.3);" onmouseover="this.style.opacity='0.88';" onmouseout="this.style.opacity='1';">{{ __('قبول') }}</button>
            </div>
        </div>
    </div>
    <style>
    @keyframes slideUpBar {
        from { transform: translateY(100%); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    </style>
    <script>
    (function() {
        if (!localStorage.getItem('cookie_consent_given')) {
            setTimeout(function() {
                const bar = document.getElementById('cookie-consent-bar');
                if (bar) bar.style.display = 'block';
            }, 1500);
        }
    })();
    function acceptCookies() {
        localStorage.setItem('cookie_consent_given', 'accepted');
        hideCookieBar();
    }
    function rejectCookies() {
        localStorage.setItem('cookie_consent_given', 'rejected');
        hideCookieBar();
    }
    function hideCookieBar() {
        const bar = document.getElementById('cookie-consent-bar');
        if (bar) {
            bar.style.transition = 'transform 0.35s ease, opacity 0.35s ease';
            bar.style.transform = 'translateY(100%)';
            bar.style.opacity = '0';
            setTimeout(() => bar.style.display = 'none', 350);
        }
    }
    </script>
    @endif

    {{-- Promo Popup --}}
    @if($popupEnabled)
    <div id="promo-popup-overlay" style="
        display:none;
        position:fixed;inset:0;z-index:99998;
        background:rgba(0,0,0,0.7);
        backdrop-filter:blur(6px);
        -webkit-backdrop-filter:blur(6px);
        align-items:center;justify-content:center;
        animation: fadeInOverlay 0.3s ease both;
    ">
        <div id="promo-popup-box" style="
            position:relative;
            background:#fff;
            border-radius:24px;
            overflow:hidden;
            max-width:520px;
            width:calc(100% - 32px);
            box-shadow:0 30px 80px rgba(0,0,0,0.4);
            animation: popIn 0.4s cubic-bezier(0.175,0.885,0.32,1.275) both;
            margin:16px;
        ">
            {{-- Close button --}}
            <button onclick="closePromoPopup()" style="position:absolute;top:14px;{{ App::getLocale()=='ar'?'left':'right' }}:14px;z-index:10;width:36px;height:36px;border-radius:50%;border:none;background:rgba(0,0,0,0.15);color:#fff;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;transition:background 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.3)';" onmouseout="this.style.background='rgba(0,0,0,0.15)';">&times;</button>

            @if(!empty($promoPopup['image']))
            <div style="width:100%;max-height:240px;overflow:hidden;">
                <img src="{{ asset('storage/' . $promoPopup['image']) }}" alt="Promo" style="width:100%;height:240px;object-fit:cover;display:block;">
            </div>
            @else
            <div style="height:8px;background:linear-gradient(90deg,#EE1E26,#ff6b70);"></div>
            @endif

            <div style="padding:28px 32px 32px;">
                @if(!empty($promoPopup['title']))
                <h3 style="font-family:'Cairo',sans-serif;font-weight:800;font-size:1.4rem;color:#0f0f12;margin:0 0 10px;">{{ $promoPopup['title'] }}</h3>
                @endif

                @if(!empty($promoPopup['text']))
                <p style="font-family:'Cairo',sans-serif;color:#555;font-size:14px;line-height:1.7;margin:0 0 24px;">{{ $promoPopup['text'] }}</p>
                @endif

                @if(!empty($promoPopup['link']))
                <a href="{{ $promoPopup['link'] }}" style="display:inline-block;padding:12px 28px;background:linear-gradient(135deg,#EE1E26,#c0141a);color:#fff;font-family:'Cairo',sans-serif;font-weight:700;font-size:14px;border-radius:12px;text-decoration:none;box-shadow:0 6px 20px rgba(238,30,38,0.35);transition:opacity 0.2s;" onmouseover="this.style.opacity='0.88';" onmouseout="this.style.opacity='1';">
                    {{ $promoPopup['button_text'] ?? __('تصفح العروض') }}
                    <i class="bi bi-arrow-{{ App::getLocale()=='ar'?'left':'right' }} ms-2"></i>
                </a>
                @endif
            </div>
        </div>
    </div>
    <style>
    @keyframes fadeInOverlay { from { opacity:0; } to { opacity:1; } }
    @keyframes popIn {
        from { opacity:0; transform:scale(0.85) translateY(20px); }
        to   { opacity:1; transform:scale(1) translateY(0); }
    }
    </style>
    <script>
    (function() {
        if (sessionStorage.getItem('promo_popup_shown')) return;
        // Show after 5 minutes (300000ms) — or 30s for dev testing
        setTimeout(function() {
            const overlay = document.getElementById('promo-popup-overlay');
            if (overlay) {
                overlay.style.display = 'flex';
                sessionStorage.setItem('promo_popup_shown', '1');
            }
        }, 300000);
    })();
    function closePromoPopup() {
        const overlay = document.getElementById('promo-popup-overlay');
        if (overlay) {
            overlay.style.transition = 'opacity 0.3s ease';
            overlay.style.opacity = '0';
            setTimeout(() => overlay.style.display = 'none', 300);
        }
    }
    // Close on overlay click
    document.addEventListener('DOMContentLoaded', function() {
        const overlay = document.getElementById('promo-popup-overlay');
        if (overlay) {
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) closePromoPopup();
            });
        }
    });
    </script>
    @endif

    <script>
        const menuBtn = document.getElementById('mobileMenuBtn');
        const nav = document.getElementById('siteNav');
        const overlay = document.getElementById('mobNavOverlay');

        function openNav() {
            nav.classList.add('open');
            overlay.classList.add('visible');
            document.body.style.overflow = 'hidden';
        }
        function closeNav() {
            nav.classList.remove('open');
            overlay.classList.remove('visible');
            document.body.style.overflow = '';
        }

        if (menuBtn) menuBtn.addEventListener('click', openNav);
        if (overlay) overlay.addEventListener('click', closeNav);
    </script>

    {{-- Global Tracking Helpers --}}
    <script>
        @if($googleAnalyticsId)
        function trackGA(action, params) {
            if (typeof gtag === 'function') {
                gtag('event', action, params || {});
            }
        }
        @endif
        @if($metaPixelId)
        function trackPixel(action, params) {
            if (typeof fbq === 'function') {
                fbq('track', action, params || {});
            }
        }
        @endif
        function trackEvent(action, params) {
            @if($googleAnalyticsId)trackGA(action, params);@endif
            @if($metaPixelId)trackPixel(action, params);@endif
        }
    </script>

    {{-- Lazy loaded images smooth fade-in script --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const handleImageLoad = (img) => {
                img.classList.add('img-loaded');
            };

            const initLazyImages = () => {
                const lazyImages = document.querySelectorAll('img[loading="lazy"]');
                lazyImages.forEach(img => {
                    if (img.complete) {
                        handleImageLoad(img);
                    } else {
                        img.addEventListener('load', () => handleImageLoad(img));
                        img.addEventListener('error', () => handleImageLoad(img));
                    }
                });
            };

            initLazyImages();

            // Observe dynamic additions (e.g. car sliders, filters)
            const observer = new MutationObserver(() => {
                initLazyImages();
            });

            observer.observe(document.body, { childList: true, subtree: true });
        });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownToggle = document.getElementById('aboutDropdownBtn');
        const dropdownMenu = document.getElementById('aboutDropdownMenu');
        
        if (dropdownToggle && dropdownMenu) {
            dropdownToggle.addEventListener('click', function(e) {
                if (window.innerWidth <= 991) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropdownMenu.classList.toggle('show');
                    dropdownToggle.classList.toggle('active-toggle');
                    
                    // Toggle chevron direction
                    const icon = dropdownToggle.querySelector('i');
                    if (icon) {
                        if (dropdownMenu.classList.contains('show')) {
                            icon.className = 'bi bi-chevron-up ms-1';
                        } else {
                            icon.className = 'bi bi-chevron-down ms-1';
                        }
                    }
                }
            });
            
            // Close mobile dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 991 && !dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    if (dropdownMenu.classList.contains('show')) {
                        dropdownMenu.classList.remove('show');
                        dropdownToggle.classList.remove('active-toggle');
                        const icon = dropdownToggle.querySelector('i');
                        if (icon) icon.className = 'bi bi-chevron-down ms-1';
                    }
                }
            });
        }
    });
    </script>

    @yield('js')

</body>

</html>
