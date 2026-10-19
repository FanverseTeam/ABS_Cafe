<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABS Cafe – Good Coffee, Good Mood</title>
    <meta name="description" content="Portal Staf ABS Cafe. Kelola operasional dan pantau bisnis ABS Cafe dalam satu tempat. Akses dashboard Admin, Owner, dan Kasir.">
    <meta name="theme-color" content="#F2F5E2">

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

    <script>document.documentElement.classList.add('js');</script>

    <style>
        html {
            scroll-behavior: smooth;
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }
        }

        .hero-arch {
            border-radius: 999px 999px 2.5rem 2.5rem;
        }

        .hero-arch-pattern {
            background-image: radial-gradient(circle at 1px 1px, rgba(41, 0, 36, 0.22) 1px, transparent 0);
            background-size: 18px 18px;
        }

        .js .reveal {
            opacity: 0;
            transform: translateY(28px);
        }

        @media (prefers-reduced-motion: no-preference) {
            .js .reveal {
                transition:
                    opacity 700ms cubic-bezier(0.22, 1, 0.36, 1),
                    transform 700ms cubic-bezier(0.22, 1, 0.36, 1);
            }

            .js .reveal.is-visible {
                opacity: 1;
                transform: translateY(0);
            }

            .js .reveal-delay-1 {
                transition-delay: 90ms;
            }

            .js .reveal-delay-2 {
                transition-delay: 180ms;
            }

            .js .reveal-delay-3 {
                transition-delay: 270ms;
            }

            .float-slow {
                animation: float-slow 7s ease-in-out infinite;
            }

            .float-slower {
                animation: float-slow 9s ease-in-out infinite;
            }

            @keyframes float-slow {
                0%,
                100% {
                    transform: translateY(0);
                }
                50% {
                    transform: translateY(-10px);
                }
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .js .reveal {
                opacity: 1;
                transform: none;
                transition: none;
            }

            .float-slow,
            .float-slower {
                animation: none;
            }
        }
    </style>
</head>
<body class="min-h-screen bg-beige font-sans text-midnight antialiased">
    @php
        $currentUser = auth()->user();
        $dashboardRoute = null;
        $dashboardLabel = 'Dashboard';

        if ($currentUser) {
            $dashboardRoute = match ($currentUser->primaryRole()) {
                'admin' => config('features.legacy_admin')
                    ? route('admin.dashboard')
                    : route('admin.staff.index'),
                'owner' => route('owner.dashboard'),
                'kasir' => route('kasir.dashboard'),
                default => route('login'),
            };

            $dashboardLabel = match ($currentUser->primaryRole()) {
                'admin' => config('features.legacy_admin') ? 'Dashboard Admin' : 'Portal Admin',
                'owner' => 'Dashboard Owner',
                'kasir' => 'Dashboard Kasir',
                default => 'Dashboard',
            };
        }

        $primaryCtaUrl = $currentUser ? $dashboardRoute : route('login');
        $primaryCtaLabel = $currentUser ? 'Buka Dashboard' : 'Login';

        $roles = [
            [
                'key' => 'admin',
                'title' => 'Admin',
                'description' => 'Kelola menu, stok, dan staf cafe dari satu panel.',
                'icon' => 'settings',
            ],
            [
                'key' => 'owner',
                'title' => 'Owner',
                'description' => 'Pantau laporan penjualan dan kinerja bisnis secara berkala.',
                'icon' => 'chart',
            ],
            [
                'key' => 'kasir',
                'title' => 'Kasir',
                'description' => 'Kelola pesanan, pembayaran, dan shift harian dengan cepat.',
                'icon' => 'cash',
            ],
        ];

        $hasCafePhoto = file_exists(public_path('images/cafe-hero.webp'));
    @endphp

    <div class="overflow-x-hidden">
        {{-- Navbar --}}
        <header class="relative z-30">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-5 sm:px-8 lg:px-10">
                <a href="{{ route('staf.landing') }}"
                   class="flex min-w-0 items-center gap-3 rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-apricot focus-visible:ring-offset-2 focus-visible:ring-offset-beige"
                   aria-label="ABS Cafe - Portal Staf">
                    <x-logo size="md" loading="eager" class="min-w-0" />
                </a>

                <a href="{{ $primaryCtaUrl }}"
                   class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-midnight px-4 py-2.5 text-xs font-semibold text-beige shadow-[0_12px_25px_-16px_rgba(41,0,36,0.65)] transition hover:bg-midnight-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-apricot focus-visible:ring-offset-2 focus-visible:ring-offset-beige sm:gap-2 sm:px-5 sm:text-sm"
                   aria-label="{{ $primaryCtaLabel }}">
                    <span>{{ $primaryCtaLabel }}</span>
                    <x-staf.icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>
        </header>

        <main>
            {{-- Hero --}}
            <section class="relative overflow-hidden" aria-labelledby="hero-title">
                <div class="pointer-events-none absolute -left-24 top-24 h-64 w-64 rounded-full bg-custard/60 blur-3xl" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -right-24 top-0 h-72 w-72 rounded-full bg-apricot/20 blur-3xl" aria-hidden="true"></div>

                <div class="relative mx-auto max-w-7xl px-5 pb-14 pt-4 sm:px-8 sm:pb-20 sm:pt-6 lg:flex lg:min-h-[calc(100svh-5.5rem)] lg:items-center lg:px-10 lg:pb-20 lg:pt-6">
                    <div class="grid w-full items-center gap-12 lg:grid-cols-[1.05fr_0.95fr] lg:gap-10 xl:gap-16">
                        {{-- Hero copy --}}
                        <div class="reveal max-w-2xl">
                            <span class="inline-flex items-center gap-2 rounded-full border border-custard-300 bg-custard px-4 py-2 text-[10px] font-bold uppercase tracking-[0.18em] text-olive sm:text-[11px]">
                                <x-staf.icon name="sparkles" class="h-4 w-4 text-midnight" />
                                Portal Staf ABS Cafe
                            </span>

                            <h1 id="hero-title" class="mt-6 font-serif text-[2.75rem] font-bold leading-[0.95] tracking-tight text-midnight sm:text-6xl lg:text-7xl xl:text-[5.5rem]">
                                Good Coffee,
                                <span class="mt-1 block italic text-apricot-400">Good Mood.</span>
                            </h1>

                            <p class="mt-6 max-w-xl text-base leading-relaxed text-olive sm:text-lg">
                                Kelola operasional dan pantau bisnis ABS Cafe dalam satu tempat.
                                Akses cepat untuk Admin, Owner, dan Kasir.
                            </p>

                            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                                <a href="{{ $primaryCtaUrl }}"
                                   class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-apricot px-7 py-4 text-base font-bold text-midnight shadow-[0_18px_35px_-18px_rgba(41,0,36,0.5)] transition hover:bg-apricot-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-midnight focus-visible:ring-offset-2 focus-visible:ring-offset-beige sm:w-auto"
                                   aria-label="{{ $primaryCtaLabel }}">
                                    <span>{{ $primaryCtaLabel }}</span>
                                    <x-staf.icon name="arrow-right" class="h-5 w-5" />
                                </a>

                                <a href="#peran"
                                   class="inline-flex w-full items-center justify-center gap-2 rounded-full px-6 py-4 text-sm font-semibold text-midnight underline decoration-apricot decoration-2 underline-offset-8 transition hover:text-olive focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-apricot focus-visible:ring-offset-2 focus-visible:ring-offset-beige sm:w-auto">
                                    Lihat peran
                                    <x-staf.icon name="arrow-down" class="h-4 w-4" />
                                </a>
                            </div>
                        </div>

                        {{-- Hero visual --}}
                        <div class="reveal reveal-delay-2 relative mx-auto w-full max-w-[520px] lg:max-w-[560px]">
                            <div class="pointer-events-none absolute left-1/2 top-1/2 h-[82%] w-[82%] -translate-x-1/2 -translate-y-1/2 rounded-full bg-custard/70" aria-hidden="true"></div>
                            <div class="pointer-events-none absolute left-1/2 top-1/2 h-[94%] w-[94%] -translate-x-1/2 -translate-y-1/2 rounded-full border border-olive/20" aria-hidden="true"></div>

                            <svg class="absolute -left-1 top-6 z-20 h-9 w-9 text-apricot sm:left-2" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M12 0l2.6 9.4L24 12l-9.4 2.6L12 24l-2.6-9.4L0 12l9.4-2.6L12 0Z" />
                            </svg>
                            <svg class="absolute -right-2 bottom-12 z-20 h-6 w-6 text-olive/60 sm:right-1" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M12 0l2.6 9.4L24 12l-9.4 2.6L12 24l-2.6-9.4L0 12l9.4-2.6L12 0Z" />
                            </svg>
                            <svg class="absolute right-12 top-0 z-20 h-4 w-4 text-apricot/70" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M12 0l2.6 9.4L24 12l-9.4 2.6L12 24l-2.6-9.4L0 12l9.4-2.6L12 0Z" />
                            </svg>

                            <div class="hero-arch relative z-10 mx-auto aspect-[4/5] w-[86%] max-w-[380px] overflow-hidden border-[6px] border-beige-50 bg-gradient-to-br from-apricot via-apricot-200 to-custard shadow-[0_30px_80px_-40px_rgba(41,0,36,0.55)] sm:max-w-[400px]">
                                @if ($hasCafePhoto)
                                    <img src="{{ asset('images/cafe-hero.webp') }}"
                                         alt="Suasana cafe ABS Cafe Tulungagung"
                                         width="800"
                                         height="1000"
                                         loading="eager"
                                         fetchpriority="high"
                                         decoding="async"
                                         class="h-full w-full object-cover">
                                @else
                                    <div class="hero-arch-pattern absolute inset-0 opacity-50" aria-hidden="true"></div>
                                    <div class="absolute inset-0 bg-gradient-to-b from-beige/10 via-transparent to-olive/20" aria-hidden="true"></div>

                                    <svg class="absolute inset-0 h-full w-full p-8 text-midnight" viewBox="0 0 320 400" fill="none" aria-hidden="true">
                                        <circle cx="160" cy="190" r="112" fill="#F2F5E2" fill-opacity="0.32" />
                                        <path d="M74 180h156v52c0 44-35 80-78 80s-78-36-78-80v-52Z" fill="#F2F5E2" fill-opacity="0.45" stroke="#290024" stroke-width="3" />
                                        <path d="M230 190h20c22 0 40 18 40 40s-18 40-40 40h-20" stroke="#290024" stroke-width="3" />
                                        <path d="M92 180h120" stroke="#290024" stroke-width="3" stroke-linecap="round" />
                                        <path d="M110 202h82" stroke="#D4954D" stroke-width="3" stroke-linecap="round" />
                                        <path d="M66 316h188" stroke="#290024" stroke-width="3" stroke-linecap="round" />
                                        <path d="M86 334h148" stroke="#775533" stroke-width="2" stroke-linecap="round" opacity="0.65" />
                                        <path d="M118 148c-14-18 12-28 0-48" stroke="#775533" stroke-width="3" stroke-linecap="round" />
                                        <path d="M152 144c-14-18 12-28 0-48" stroke="#775533" stroke-width="3" stroke-linecap="round" />
                                        <path d="M186 148c-14-18 12-28 0-48" stroke="#775533" stroke-width="3" stroke-linecap="round" />
                                        <path d="M252 118l3 10 10 3-10 3-3 10-3-10-10-3 10-3 3-10Z" fill="#D4954D" />
                                        <path d="M58 240l2 7 7 2-7 2-2 7-2-7-7-2 7-2 2-7Z" fill="#775533" opacity="0.55" />
                                    </svg>
                                @endif

                                <div class="absolute inset-x-0 bottom-0 flex items-center justify-between gap-3 bg-gradient-to-t from-midnight/60 to-transparent p-5 pt-12">
                                    <span class="text-xs font-semibold uppercase tracking-[0.18em] text-beige">ABS Cafe</span>
                                    <span class="rounded-full bg-beige/90 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-midnight">Tulungagung</span>
                                </div>
                            </div>

                            {{-- Floating feature cards --}}
                            <x-staf.stat-card label="Pesanan masuk" value="24 pesanan" class="float-slow absolute -left-1 top-[16%] z-20 w-[158px] sm:-left-6 sm:w-[186px]">
                                <x-slot:icon>
                                    <x-staf.icon name="receipt" class="h-4 w-4" />
                                </x-slot:icon>
                            </x-staf.stat-card>

                            <x-staf.stat-card label="Laporan hari ini" value="Rp 2,4 jt" tone="midnight" class="absolute -right-1 top-[44%] z-20 w-[158px] sm:-right-6 sm:w-[186px]">
                                <x-slot:icon>
                                    <x-staf.icon name="chart" class="h-4 w-4" />
                                </x-slot:icon>
                            </x-staf.stat-card>

                            <x-staf.stat-card label="Stok aman" value="98% tersedia" class="float-slower absolute -left-1 bottom-[7%] z-20 w-[158px] sm:-left-4 sm:w-[186px]">
                                <x-slot:icon>
                                    <x-staf.icon name="box" class="h-4 w-4" />
                                </x-slot:icon>
                            </x-staf.stat-card>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Role section --}}
            <section id="peran" class="relative scroll-mt-20 overflow-hidden border-t border-custard-200 bg-custard/40 py-16 sm:py-20 lg:py-24" aria-labelledby="peran-title">
                <div class="pointer-events-none absolute -right-20 top-10 h-56 w-56 rounded-full bg-apricot/10 blur-3xl" aria-hidden="true"></div>

                <div class="relative mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                    <div class="reveal flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-olive">Akses Staf</p>
                            <h2 id="peran-title" class="mt-2 font-serif text-3xl font-bold tracking-tight text-midnight sm:text-4xl">Masuk sebagai</h2>
                        </div>
                        <p class="max-w-md text-sm leading-relaxed text-olive">
                            Pilih peran Anda. Setiap peran punya akses dan tugas yang berbeda di portal ABS Cafe.
                        </p>
                    </div>

                    <div class="mt-8 grid gap-5 md:grid-cols-3">
                        @foreach ($roles as $index => $role)
                            @php
                                $isCurrentRole = $currentUser && $currentUser->primaryRole() === $role['key'];
                                $roleLink = $isCurrentRole ? $dashboardRoute : route('login');
                                $roleLinkLabel = $isCurrentRole ? 'Buka Dashboard' : 'Login';
                            @endphp

                            <div class="reveal reveal-delay-{{ ($index % 3) + 1 }} h-full">
                                <x-staf.role-card
                                    :title="$role['title']"
                                    :description="$role['description']"
                                    :icon="$role['icon']"
                                    :href="$roleLink"
                                    :link-label="$roleLinkLabel"
                                    :variant="$index % 2 === 0 ? 'custard' : 'cream'"
                                />
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        </main>

        {{-- Footer --}}
        <footer class="border-t border-custard-200 bg-beige">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-5 py-5 text-xs text-olive sm:flex-row sm:px-8 lg:px-10">
                <div class="flex items-center gap-3">
                    <x-logo size="sm" :show-text="false" />
                    <p class="font-medium">ABS Cafe &bull; Tulungagung</p>
                </div>
                <p class="text-[10px] font-semibold uppercase tracking-[0.18em]">Portal Staf</p>
            </div>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const revealItems = document.querySelectorAll('.reveal');
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            if (reduceMotion || !('IntersectionObserver' in window)) {
                revealItems.forEach(function (item) {
                    item.classList.add('is-visible');
                });

                return;
            }

            const observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });

            revealItems.forEach(function (item) {
                observer.observe(item);
            });
        });
    </script>
</body>
</html>
