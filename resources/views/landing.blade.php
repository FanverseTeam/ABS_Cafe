<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>ABS Cafe – Good Coffee, Good Mood</title>
    <meta name="description" content="Portal Staf ABS Cafe. Kelola operasional dan pantau bisnis ABS Cafe dalam satu tempat. Akses dashboard Admin, Owner, dan Kasir.">
    <meta name="theme-color" content="#290024">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="ABS Cafe – Good Coffee, Good Mood">
    <meta property="og:description" content="Portal Staf ABS Cafe. Kelola operasional dan pantau bisnis ABS Cafe dalam satu tempat.">
    <meta property="og:image" content="{{ asset('images/logo-og.png') }}">
    <meta property="og:site_name" content="ABS Cafe">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-badge.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-badge.png') }}">

    <!-- Fonts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $currentUser = auth()->user();

        $dashboardRoute = null;

        if ($currentUser) {
            $dashboardRoute = match ($currentUser->primaryRole()) {
                'admin' => config('features.legacy_admin')
                    ? route('admin.dashboard')
                    : route('admin.staff.index'),
                'owner' => route('owner.dashboard'),
                'kasir' => route('kasir.dashboard'),
                default => route('login'),
            };
        }

        $primaryCtaUrl = $dashboardRoute ?? route('login');
        $primaryCtaLabel = $currentUser ? 'Buka Dashboard' : 'Login';

        $landingBgSources = [];

        foreach ([768, 1280, 1920] as $width) {
            $path = "images/landing-bg-{$width}.webp";

            if (is_file(public_path($path))) {
                $landingBgSources[$width] = asset($path).'?v='.filemtime(public_path($path));
            }
        }

        $landingBgFallbackPath = 'images/landing-bg-1920.jpg';
        $landingBgFallbackUrl = is_file(public_path($landingBgFallbackPath))
            ? asset($landingBgFallbackPath).'?v='.filemtime(public_path($landingBgFallbackPath))
            : null;

        $landingBgSrcset = collect($landingBgSources)
            ->map(fn (string $url, int $width): string => "{$url} {$width}w")
            ->implode(', ');

        $landingBgLargestWidth = array_key_last($landingBgSources);
        $landingBgPreloadUrl = $landingBgLargestWidth !== null
            ? $landingBgSources[$landingBgLargestWidth]
            : $landingBgFallbackUrl;

        $landingBgImageUrl = $landingBgFallbackUrl ?? $landingBgPreloadUrl;
        $hasLandingBackground = $landingBgImageUrl !== null;
    @endphp

    @if ($hasLandingBackground)
        <link rel="preload" as="image"
              href="{{ $landingBgPreloadUrl }}"
              @if ($landingBgSrcset !== '') imagesrcset="{{ $landingBgSrcset }}" imagesizes="100vw" @endif
              fetchpriority="high">
    @endif

    <style>
        html,
        body {
            height: 100%;
        }

        body {
            overflow: hidden;
        }

        .landing-screen {
            position: relative;
            display: flex;
            height: 100svh;
            min-height: 100svh;
            flex-direction: column;
            overflow: hidden;
            isolation: isolate;
        }

        .landing-fallback {
            background-image: linear-gradient(135deg, #F2F5E2 0%, #E3DEA4 52%, #D4954D 100%);
        }

        .landing-bg {
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .landing-bg-failed .landing-bg {
            display: none;
        }

        .landing-overlay {
            background-image:
                linear-gradient(to top, rgba(41, 0, 36, 0.12) 0%, rgba(41, 0, 36, 0) 160px),
                linear-gradient(to bottom, rgba(41, 0, 36, 0.20) 0%, rgba(41, 0, 36, 0.46) 50%, rgba(41, 0, 36, 0.50) 100%),
                radial-gradient(ellipse 72% 58% at 50% 48%, rgba(41, 0, 36, 0.20) 0%, rgba(41, 0, 36, 0.10) 58%, rgba(41, 0, 36, 0) 100%),
                linear-gradient(135deg, rgba(212, 149, 77, 0.16) 0%, rgba(227, 222, 164, 0.05) 55%, rgba(212, 149, 77, 0.12) 100%);
        }

        .landing-copy {
            text-shadow:
                0 1px 2px rgba(41, 0, 36, 0.55),
                0 10px 30px rgba(41, 0, 36, 0.35);
        }

        .landing-content {
            display: flex;
            flex: 1;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1.25rem 0.75rem;
            text-align: center;
        }

        @keyframes landing-rise {
            from {
                opacity: 0;
                transform: translateY(22px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (prefers-reduced-motion: no-preference) {
            .landing-reveal {
                animation: landing-rise 800ms cubic-bezier(0.22, 1, 0.36, 1) both;
            }

            .landing-reveal-delay-1 {
                animation-delay: 90ms;
            }

            .landing-reveal-delay-2 {
                animation-delay: 180ms;
            }

            .landing-reveal-delay-3 {
                animation-delay: 270ms;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .landing-reveal {
                animation: none;
                opacity: 1;
                transform: none;
            }
        }

        @media (min-width: 640px) {
            .landing-content {
                padding: 3rem 2rem 1rem;
            }
        }

        @media (max-height: 640px) {
            .landing-content {
                padding-top: 1rem;
                padding-bottom: 0.25rem;
            }

            .landing-kicker {
                margin-top: 1rem;
            }

            .landing-title {
                font-size: clamp(1.9rem, 8vh, 3rem);
            }

            .landing-subtitle {
                margin-top: 0.85rem;
                font-size: 0.875rem;
            }

            .landing-cta {
                margin-top: 1rem;
                min-height: 3rem;
                padding-top: 0.75rem;
                padding-bottom: 0.75rem;
            }
        }
    </style>
</head>
<body class="landing-body bg-midnight font-sans text-beige antialiased">
    <main class="landing-screen" aria-labelledby="landing-title">
        <div class="landing-fallback absolute inset-0 -z-20" aria-hidden="true"></div>

        @if ($hasLandingBackground)
            <picture class="landing-bg absolute inset-0 -z-20 block h-full w-full">
                @if ($landingBgSrcset !== '')
                    <source type="image/webp" srcset="{{ $landingBgSrcset }}" sizes="100vw">
                @endif

                <img src="{{ $landingBgImageUrl }}"
                     alt=""
                     aria-hidden="true"
                     width="1920"
                     height="1280"
                     loading="eager"
                     fetchpriority="high"
                     decoding="async"
                     onerror="document.documentElement.classList.add('landing-bg-failed')"
                     class="h-full w-full object-cover object-center">
            </picture>
        @endif

        <div class="landing-overlay absolute inset-0 -z-10" aria-hidden="true"></div>

        <div class="landing-content landing-copy">
            <x-logo size="lg"
                    :show-text="false"
                    loading="eager"
                    class="landing-reveal drop-shadow-[0_10px_30px_rgba(41,0,36,0.55)]" />

            <span class="landing-kicker landing-reveal landing-reveal-delay-1 mt-7 inline-flex items-center gap-2 rounded-full border border-custard/40 bg-midnight/35 px-4 py-2 text-[10px] font-bold uppercase tracking-[0.2em] text-beige backdrop-blur-sm sm:text-[11px]">
                <span class="h-1.5 w-1.5 rounded-full bg-apricot" aria-hidden="true"></span>
                Portal Staf ABS Cafe
            </span>

            <h1 id="landing-title"
                class="landing-title landing-reveal landing-reveal-delay-2 mt-5 font-serif text-[clamp(2.25rem,8.5vw,5.25rem)] font-bold leading-[0.98] tracking-tight">
                <span class="block text-beige">Good Coffee,</span>
                <span class="mt-1 block italic text-apricot">Good Mood.</span>
            </h1>

            <p class="landing-subtitle landing-reveal landing-reveal-delay-2 mt-5 max-w-md text-sm leading-relaxed text-custard sm:max-w-xl sm:text-base">
                Kelola operasional dan pantau bisnis ABS Cafe dalam satu tempat.
            </p>

            <a href="{{ $primaryCtaUrl }}"
               class="landing-cta landing-reveal landing-reveal-delay-3 group mt-8 inline-flex min-h-14 w-full max-w-[320px] items-center justify-center gap-2.5 rounded-full bg-apricot px-8 py-4 text-base font-bold text-midnight shadow-[0_20px_50px_-20px_rgba(41,0,36,0.75)] transition hover:bg-apricot-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-beige focus-visible:ring-offset-2 focus-visible:ring-offset-midnight active:bg-apricot-300 sm:text-lg">
                <span>{{ $primaryCtaLabel }}</span>
                <x-staf.icon name="arrow-right" class="h-5 w-5 transition-transform duration-200 group-hover:translate-x-1 motion-reduce:transform-none" />
            </a>
        </div>

        <footer class="relative z-10 shrink-0 px-5 pb-[max(1.25rem,env(safe-area-inset-bottom))] pt-2 text-center">
            <p class="text-[11px] font-medium tracking-[0.16em] text-beige/90 sm:text-xs">ABS Cafe • Tulungagung</p>
        </footer>
    </main>
</body>
</html>
