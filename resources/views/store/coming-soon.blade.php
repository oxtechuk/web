<!DOCTYPE html>
<html lang="{{ App::getLocale() }}" dir="{{ App::getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $globalSettings = app(\App\Services\Cache\BaseCacheService::class)->rememberSettings();
        $siteName = $globalSettings['site_name'] ?? 'GR Motors';
        if (is_array($siteName)) {
            $siteName = $siteName[App::getLocale()] ?? ($siteName['ar'] ?? 'GR Motors');
        }
        $title = App::getLocale() == 'ar'
            ? ($globalSettings['coming_soon_title_ar'] ?? __('قادمون قريباً'))
            : ($globalSettings['coming_soon_title_en'] ?? 'Coming Soon');
        $subtitle = App::getLocale() == 'ar'
            ? ($globalSettings['coming_soon_subtitle_ar'] ?? __('نعمل على تطوير تجربتك. نعود قريباً بشيء رائع!'))
            : ($globalSettings['coming_soon_subtitle_en'] ?? 'We are working on something amazing. Stay tuned!');
        $launchDate = $globalSettings['coming_soon_date'] ?? null;
        $bgImage = !empty($globalSettings['coming_soon_bg_image'])
            ? asset('storage/' . $globalSettings['coming_soon_bg_image'])
            : null;
        $altLang = App::getLocale() === 'ar' ? 'en' : 'ar';
        $altLangText = App::getLocale() === 'ar' ? 'English' : 'العربية';
    @endphp
    <title>{{ $title }} | {{ $siteName }}</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="shortcut icon" href="{{ isset($globalSettings['site_favicon']) && $globalSettings['site_favicon'] ? asset('storage/' . $globalSettings['site_favicon']) : asset('assets/images/k_favicon_32x.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --red: linear-gradient(180deg, #ED1C24 0%, #B1161C 32.21%, #8A1217 55.77%, #5A0D10 100%);
            --red-dark: #a8151b;
            --font: 'Cairo', sans-serif;
        }

        html, body {
            height: 100%;
            font-family: var(--font);
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            background: #0d0d0f;
        }

        /* Background */
        .cs-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: #0d0d0f;
        }
        @if($bgImage)
        .cs-bg {
            background: url('{{ $bgImage }}') center center / cover no-repeat;
        }
        .cs-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(160deg, rgba(10,10,12,0.88) 0%, rgba(10,10,12,0.75) 50%, rgba(10,10,12,0.92) 100%);
        }
        @else
        /* Animated gradient background when no image */
        .cs-bg {
            background: linear-gradient(135deg, #0d0d0f 0%, #1a0a0c 50%, #0d0d0f 100%);
        }
        .cs-bg::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(238,30,38,0.18) 0%, transparent 70%);
            top: -100px;
            right: -100px;
            animation: pulseGlow 6s ease-in-out infinite;
        }
        .cs-bg::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(238,30,38,0.12) 0%, transparent 70%);
            bottom: -80px;
            left: -80px;
            animation: pulseGlow 8s ease-in-out infinite reverse;
        }
        @keyframes pulseGlow {
            0%, 100% { transform: scale(1); opacity: 0.6; }
            50% { transform: scale(1.1); opacity: 1; }
        }
        @endif

        /* Particles */
        .particles {
            position: fixed;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            overflow: hidden;
        }
        .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            border-radius: 50%;
            background: rgba(255,255,255,0.4);
            animation: float linear infinite;
        }

        @keyframes float {
            0% { transform: translateY(110vh) translateX(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-10vh) translateX(30px); opacity: 0; }
        }

        /* Content */
        .cs-content {
            position: relative;
            z-index: 10;
            text-align: center;
            color: #fff;
            padding: 40px 20px;
            max-width: 700px;
            width: 100%;
            animation: fadeInUp 0.8s ease both;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Logo */
        .cs-logo {
            margin-bottom: 36px;
        }
        .cs-logo img {
            height: 60px;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        /* Badge */
        .cs-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(238,30,38,0.15);
            border: 1px solid rgba(238,30,38,0.35);
            color: #ff6b70;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 8px 20px;
            border-radius: 100px;
            margin-bottom: 24px;
        }
        .cs-badge .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: linear-gradient(180deg, #ED1C24 0%, #B1161C 32.21%, #8A1217 55.77%, #5A0D10 100%);
            animation: blink 1.5s ease-in-out infinite;
        }
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.2; }
        }

        /* Title */
        .cs-title {
            font-size: clamp(2rem, 6vw, 4rem);
            font-weight: 900;
            line-height: 1.15;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #fff 0%, rgba(255,255,255,0.7) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .cs-subtitle {
            font-size: clamp(0.95rem, 2.5vw, 1.15rem);
            color: rgba(255,255,255,0.6);
            line-height: 1.75;
            margin-bottom: 48px;
            font-weight: 400;
        }

        /* Countdown */
        .cs-countdown {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-bottom: 48px;
            flex-wrap: wrap;
        }

        .cs-countdown-item {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 20px 28px;
            min-width: 90px;
            backdrop-filter: blur(10px);
            transition: border-color 0.3s, background 0.3s;
        }

        .cs-countdown-item:hover {
            border-color: rgba(238,30,38,0.4);
            background: rgba(238,30,38,0.08);
        }

        .cs-countdown-num {
            display: block;
            font-size: 2.5rem;
            font-weight: 900;
            color: #fff;
            line-height: 1;
            font-variant-numeric: tabular-nums;
        }

        .cs-countdown-label {
            display: block;
            font-size: 11px;
            color: rgba(255,255,255,0.45);
            margin-top: 6px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Divider */
        .cs-divider {
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--red), transparent);
            margin: 0 auto 40px;
            border-radius: 100px;
        }

        /* Social links */
        .cs-social {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .cs-social a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            color: rgba(255,255,255,0.6);
            font-size: 18px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .cs-social a:hover {
            background: var(--red);
            border-color: var(--red);
            color: #fff;
            transform: translateY(-3px);
        }

        /* Lang switch */
        .cs-lang {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 20;
        }

        .cs-lang a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 100px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .cs-lang a:hover {
            background: rgba(255,255,255,0.15);
            color: #fff;
        }

        /* Watermark */
        .cs-watermark {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 20;
            font-size: 12px;
            color: rgba(255,255,255,0.25);
            letter-spacing: 0.5px;
        }

        @media (max-width: 480px) {
            .cs-countdown-item { padding: 16px 18px; min-width: 72px; }
            .cs-countdown-num { font-size: 2rem; }
            .cs-logo img { height: 46px; }
        }
    </style>
</head>
<body>

    <div class="cs-bg"></div>

    {{-- Floating particles --}}
    <div class="particles" id="particles"></div>

    {{-- Language switcher --}}
    <div class="cs-lang">
        <a href="{{ route('lang.switch', $altLang) }}">
            <i class="bi bi-globe"></i> {{ $altLangText }}
        </a>
    </div>

    <div class="cs-content">

        {{-- Logo --}}
        <div class="cs-logo">
            @if(!empty($globalSettings['site_logo']))
                <img src="{{ asset('storage/' . $globalSettings['site_logo']) }}" alt="{{ $siteName }}">
            @else
                <img src="{{ asset('assets/images/logo.png') }}" alt="{{ $siteName }}" onerror="this.style.display='none'">
            @endif
        </div>

        {{-- Badge --}}
        <div class="cs-badge">
            <span class="dot"></span>
            {{ App::getLocale() == 'ar' ? 'قريباً' : 'Coming Soon' }}
        </div>

        {{-- Title --}}
        <h1 class="cs-title">{{ $title }}</h1>

        {{-- Subtitle --}}
        <p class="cs-subtitle">{{ $subtitle }}</p>

        {{-- Countdown --}}
        @if($launchDate)
        <div class="cs-countdown" id="countdown">
            <div class="cs-countdown-item">
                <span class="cs-countdown-num" id="cd-days">00</span>
                <span class="cs-countdown-label">{{ App::getLocale() == 'ar' ? 'يوم' : 'Days' }}</span>
            </div>
            <div class="cs-countdown-item">
                <span class="cs-countdown-num" id="cd-hours">00</span>
                <span class="cs-countdown-label">{{ App::getLocale() == 'ar' ? 'ساعة' : 'Hours' }}</span>
            </div>
            <div class="cs-countdown-item">
                <span class="cs-countdown-num" id="cd-mins">00</span>
                <span class="cs-countdown-label">{{ App::getLocale() == 'ar' ? 'دقيقة' : 'Minutes' }}</span>
            </div>
            <div class="cs-countdown-item">
                <span class="cs-countdown-num" id="cd-secs">00</span>
                <span class="cs-countdown-label">{{ App::getLocale() == 'ar' ? 'ثانية' : 'Seconds' }}</span>
            </div>
        </div>
        @endif

        <div class="cs-divider"></div>

        {{-- Social Links --}}
        @php
            $socialMedia = $globalSettings['social_media'] ?? [];
            if (!is_array($socialMedia) && is_string($socialMedia)) {
                $socialMedia = json_decode($socialMedia, true) ?: [];
            }
        @endphp
        @if(count($socialMedia) > 0)
        <div class="cs-social">
            @foreach($socialMedia as $social)
                <a href="{{ $social['link'] ?? '#' }}" target="_blank" rel="noopener">
                    <i class="bi {{ $social['icon'] ?? 'bi-link' }}"></i>
                </a>
            @endforeach
        </div>
        @endif

    </div>

    <div class="cs-watermark">&copy; {{ date('Y') }} {{ $siteName }}. {{ App::getLocale() == 'ar' ? 'جميع الحقوق محفوظة.' : 'All rights reserved.' }}</div>

    <script>
        // === Particles ===
        (function() {
            const container = document.getElementById('particles');
            const count = 40;
            for (let i = 0; i < count; i++) {
                const p = document.createElement('span');
                p.className = 'particle';
                const size = Math.random() * 3 + 1;
                p.style.cssText = `
                    left: ${Math.random() * 100}%;
                    width: ${size}px;
                    height: ${size}px;
                    animation-duration: ${Math.random() * 15 + 10}s;
                    animation-delay: ${Math.random() * 10}s;
                    opacity: ${Math.random() * 0.5 + 0.1};
                `;
                container.appendChild(p);
            }
        })();

        @if($launchDate)
        // === Countdown ===
        (function() {
            const target = new Date('{{ $launchDate }}').getTime();
            const pad = n => String(n).padStart(2, '0');

            function tick() {
                const now = Date.now();
                const diff = target - now;

                if (diff <= 0) {
                    document.getElementById('cd-days').textContent = '00';
                    document.getElementById('cd-hours').textContent = '00';
                    document.getElementById('cd-mins').textContent = '00';
                    document.getElementById('cd-secs').textContent = '00';
                    return;
                }

                const days  = Math.floor(diff / 86400000);
                const hours = Math.floor((diff % 86400000) / 3600000);
                const mins  = Math.floor((diff % 3600000) / 60000);
                const secs  = Math.floor((diff % 60000) / 1000);

                document.getElementById('cd-days').textContent  = pad(days);
                document.getElementById('cd-hours').textContent = pad(hours);
                document.getElementById('cd-mins').textContent  = pad(mins);
                document.getElementById('cd-secs').textContent  = pad(secs);
            }

            tick();
            setInterval(tick, 1000);
        })();
        @endif
    </script>

</body>
</html>
