<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - ABS Cafe (Accessories & Cafe)</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-badge.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-badge.png') }}">

    <!-- Font lokal: Noto Serif & Poppins (self-hosted) -->
    @include('partials.font-preloads')
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">

    <!-- Tailwind CSS (via CDN with ABS Cafe Palette) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                colors: {
                    transparent: 'transparent',
                    current: 'currentColor',
                    inherit: 'inherit',
                    beige: '#F2F5E2',
                    vanilla: '#E3DEA4',
                    custard: '#E3DEA4',
                    apricot: '#D4954D',
                    olive: '#775533',
                    midnight: '#290024',
                    plum: '#290024',
                    bronze: '#D4954D',
                    success: '#775533',
                    warning: '#D4954D',
                    danger: '#775533',
                    info: '#D4954D',
                    cafe: {
                        dark: '#290024',
                        plum: '#290024',
                        brown: '#775533',
                        olive: '#775533',
                        gold: '#D4954D',
                        apricot: '#D4954D',
                        bronze: '#D4954D',
                        cream: '#F2F5E2',
                        beige: '#E3DEA4',
                        custard: '#E3DEA4',
                        card: '#F2F5E2',
                        text: '#290024',
                        muted: '#775533',
                        success: '#775533',
                        warning: '#D4954D',
                        danger: '#775533',
                        info: '#D4954D',
                    }
                },
                extend: {
                    fontFamily: {
                        serif: ['"Noto Serif"', 'Georgia', 'serif'],
                        sans: ['"Poppins"', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                    },
                    borderRadius: {
                        '2xl': '1rem',
                        '3xl': '1.5rem',
                        '4xl': '2rem',
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(41, 0, 36, 0.06), 0 2px 6px -1px rgba(41, 0, 36, 0.04)',
                        'soft-lg': '0 10px 25px -3px rgba(41, 0, 36, 0.12), 0 4px 12px -2px rgba(41, 0, 36, 0.06)',
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root { color-scheme: light; accent-color: #D4954D; caret-color: #290024; }
        ::selection { background: #D4954D; color: #290024; }
        :focus-visible { outline: 2px solid #D4954D; outline-offset: 2px; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(212, 149, 77, 0.3); border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(212, 149, 77, 0.5); }
    </style>
</head>
<body class="antialiased min-h-screen bg-[#F2F5E2] lg:bg-[#120410] lg:bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] lg:from-[#2a0624] lg:via-[#160214] lg:to-[#0a0109] flex items-center justify-center p-0 sm:p-4 lg:p-8 xl:p-12 font-sans text-[#290024] selection:bg-[#D4954D] selection:text-[#290024] relative overflow-x-hidden">

    <!-- Background Ambient Glow Halus untuk Desktop -->
    <div class="hidden lg:block fixed -top-32 -left-32 w-[480px] h-[480px] bg-[#D4954D]/15 rounded-full blur-[140px] pointer-events-none"></div>
    <div class="hidden lg:block fixed -bottom-32 -right-32 w-[480px] h-[480px] bg-[#D4954D]/10 rounded-full blur-[160px] pointer-events-none"></div>

    <!-- Container Mockup Mobile Frame di Mobile / Kartu Modern Warm Luxury di Desktop -->
    <div class="w-full max-w-md sm:max-w-[420px] lg:max-w-4xl xl:max-w-5xl h-screen sm:h-auto sm:min-h-[720px] lg:min-h-[600px] lg:h-auto max-h-screen sm:max-h-none bg-[#290024] lg:bg-transparent sm:rounded-[2.5rem] lg:rounded-[2.25rem] shadow-[0_20px_60px_-15px_rgba(41,0,36,0.3)] lg:shadow-[0_25px_80px_-20px_rgba(41,0,36,0.6),_0_0_50px_rgba(212,149,77,0.1)] sm:border border-[#E3DEA4] lg:border-[#D4954D]/30 overflow-hidden flex flex-col lg:grid lg:grid-cols-12 justify-between relative z-10 lg:backdrop-blur-xl">

        <!-- ========================================================================= -->
        <!-- AREA ATAS (MOBILE) / KOLOM KIRI (DESKTOP): BRANDING & ATMOSPHERE KAFE     -->
        <!-- ========================================================================= -->
        <div class="relative bg-gradient-to-b from-[#290024] via-[#20011c] to-[#140012] lg:from-[#260322] lg:via-[#190117] lg:to-[#10010e] p-6 sm:p-8 lg:p-10 xl:p-12 flex flex-col justify-between overflow-hidden shrink-0 lg:col-span-5 xl:col-span-5 lg:h-full">

            <!-- Foto Latar Kafe Berkelas dengan Vignette Overlay Halus (Khusus Desktop) -->
            <div class="absolute inset-0 bg-cover bg-center opacity-20 mix-blend-overlay filter blur-[0.4px] scale-105 pointer-events-none hidden lg:block"
                 style="background-image: url('{{ asset('images/landing-bg-1280.webp') }}');"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#10010e] via-[#1a0117]/85 to-transparent pointer-events-none hidden lg:block"></div>

            <!-- Background Ambient Glow Halus di Sudut Kolom Kiri -->
            <div class="absolute -top-16 -left-16 w-56 h-56 rounded-full bg-[#D4954D]/20 blur-2xl pointer-events-none hidden lg:block"></div>
            <div class="absolute -bottom-16 -right-16 w-56 h-56 rounded-full bg-[#D4954D]/15 blur-2xl pointer-events-none hidden lg:block"></div>

            <!-- Baris Navigasi: Logo Brand & Badge Status -->
            <div class="relative z-10 flex items-center justify-between">
                <div class="inline-flex items-center gap-2.5">
                    <img src="{{ asset('images/logo-badge.png') }}"
                         alt="Logo ABS Cafe"
                         class="w-7 h-7 lg:w-9 lg:h-9 object-contain drop-shadow-md">
                    <div>
                        <span class="font-serif font-bold text-sm lg:text-base text-[#F2F5E2] tracking-tight block">
                            ABS Cafe
                        </span>
                        <span class="text-[9px] uppercase tracking-[0.2em] text-[#D4954D] font-semibold hidden lg:block">
                            ACCESSORIES & CAFE
                        </span>
                    </div>
                </div>

                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/[0.08] text-[#D4954D] text-[11px] font-semibold border border-[#D4954D]/35 backdrop-blur-md shadow-xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Portal Resmi</span>
                </div>
            </div>

            <!-- Teks Utama Header Sesuai Desain Modern Warm Luxury -->
            <div class="relative z-10 mt-6 lg:mt-8 mb-1 lg:mb-0">
                <div class="hidden lg:block mb-2">
                    <span class="text-[10px] font-bold tracking-[0.25em] uppercase text-[#D4954D]/90">
                        SISTEM MANAJEMEN INTERNAL
                    </span>
                </div>
                <h1 class="font-serif text-2xl sm:text-[26px] lg:text-[28px] xl:text-3xl font-bold text-[#F2F5E2] leading-snug lg:leading-tight">
                    Masuk untuk mengelola operasional dan memantau pesanan.
                </h1>
                <p class="font-serif italic text-[#D4954D] text-sm lg:text-base mt-2 hidden lg:block tracking-wide">
                    Good Coffee, Better Moments
                </p>
                <p class="text-xs lg:text-sm text-[#E3DEA4]/90 mt-2 leading-relaxed">
                    Satu pintu masuk untuk Administrator, Owner, dan Kasir ABS Cafe.
                </p>

                <!-- Floating Live Card: Sistem Terintegrasi (Desain Eksklusif Desktop) -->
                <div class="hidden lg:flex items-center justify-between p-3.5 rounded-2xl bg-white/[0.05] backdrop-blur-md border border-white/[0.1] shadow-md mt-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#D4954D] to-[#b3732c] flex items-center justify-center text-[#1a0815] shadow-xs shrink-0">
                            <i data-lucide="coffee" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-[#F2F5E2] text-xs">Sistem Terintegrasi</p>
                            <p class="text-[10px] text-[#E3DEA4]/75">POS Kasir &bull; Meja &bull; Laporan</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-[10px] font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span>Aktif</span>
                    </div>
                </div>

                <!-- 3 Mini Feature Pills -->
                <div class="hidden lg:grid grid-cols-3 gap-2 mt-2.5 text-[10px] text-[#E3DEA4]/90">
                    <div class="flex flex-col items-center justify-center text-center p-2 rounded-xl bg-white/[0.03] border border-white/[0.06] hover:border-[#D4954D]/30 transition-all">
                        <i data-lucide="receipt" class="w-3.5 h-3.5 text-[#D4954D] mb-1"></i>
                        <span class="font-medium text-[10px]">Transaksi POS</span>
                    </div>
                    <div class="flex flex-col items-center justify-center text-center p-2 rounded-xl bg-white/[0.03] border border-white/[0.06] hover:border-[#D4954D]/30 transition-all">
                        <i data-lucide="layout-grid" class="w-3.5 h-3.5 text-[#D4954D] mb-1"></i>
                        <span class="font-medium text-[10px]">Live Meja</span>
                    </div>
                    <div class="flex flex-col items-center justify-center text-center p-2 rounded-xl bg-white/[0.03] border border-white/[0.06] hover:border-[#D4954D]/30 transition-all">
                        <i data-lucide="trending-up" class="w-3.5 h-3.5 text-[#D4954D] mb-1"></i>
                        <span class="font-medium text-[10px]">Rekap Kas</span>
                    </div>
                </div>
            </div>

            <!-- Footer Kolom Kiri Desktop -->
            <div class="relative z-10 hidden lg:flex items-center justify-between pt-5 border-t border-white/[0.08] text-[11px] text-[#E3DEA4]/60">
                <span>&copy; {{ date('Y') }} ABS Cafe</span>
                <span class="text-[#D4954D]/80 font-medium">SMKN 1 Boyolangu</span>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- AREA BAWAH (MOBILE) / KOLOM KANAN (DESKTOP): FORMULIR LOGIN MODERN        -->
        <!-- ========================================================================= -->
        <div class="bg-[#F2F5E2] lg:bg-[#FAF9F5] rounded-t-[2.5rem] lg:rounded-none p-6 sm:p-8 lg:p-9 xl:p-10 pt-5 sm:pt-8 flex flex-col justify-between flex-1 shadow-[0_-12px_30px_rgba(41,0,36,0.15)] lg:shadow-none lg:border-l lg:border-[#D4954D]/20 relative z-20 overflow-y-auto lg:overflow-visible lg:col-span-7 xl:col-span-7 lg:h-full">
            <div class="space-y-4 lg:space-y-4.5">
                <!-- Mobile Drag Indicator -->
                <div class="w-10 h-1 bg-[#775533]/25 rounded-full mx-auto mb-2 lg:hidden"></div>

                <!-- Judul Form Login dengan Badge Estetik & Link Beranda -->
                <div class="text-center lg:text-left">
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="hidden lg:inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-[#D4954D]/10 border border-[#D4954D]/25 text-[#775533] text-[10px] font-semibold tracking-wider uppercase">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#D4954D]"></span>
                            <span>Portal Staf & Pengelola</span>
                        </div>
                        <a href="{{ route('pelanggan.beranda') }}"
                           class="inline-flex items-center gap-1.5 text-[11px] font-medium text-[#775533] hover:text-[#290024] lg:hover:text-[#D4954D] transition-colors ml-auto group py-0.5 px-2 rounded-lg hover:bg-[#D4954D]/10">
                            <i data-lucide="arrow-left" class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-0.5"></i>
                            <span>Ke Beranda</span>
                        </a>
                    </div>
                    <h2 class="font-serif text-2xl sm:text-3xl font-bold text-[#290024] tracking-tight">
                        Masuk ke Akun
                    </h2>
                    <p class="text-xs sm:text-sm text-[#775533] mt-1 font-medium">
                        Masuk ke Dashboard staf & pengelola
                    </p>
                </div>

                <!-- Notifikasi Status / Pesan Kesalahan / Berhasil Keluar -->
                @if(session('error'))
                    <div class="p-3.5 rounded-xl bg-red-50/90 border border-red-200/80 text-red-900 text-xs leading-relaxed flex items-start gap-2.5 shadow-xs">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 shrink-0 mt-0.5"></i>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('success'))
                    <div class="p-3.5 rounded-xl bg-emerald-50/90 border border-emerald-200/80 text-emerald-900 text-xs leading-relaxed flex items-start gap-2.5 shadow-xs">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('status'))
                    <div class="p-3.5 rounded-xl bg-amber-50/90 border border-amber-200/80 text-amber-900 text-xs leading-relaxed flex items-start gap-2.5 shadow-xs">
                        <i data-lucide="info" class="w-4 h-4 text-[#D4954D] shrink-0 mt-0.5"></i>
                        <span class="font-medium">{{ session('status') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-3.5 rounded-xl bg-red-50/90 border border-red-200/80 text-red-900 text-xs leading-relaxed space-y-1 shadow-xs">
                        @foreach ($errors->all() as $err)
                            <p class="flex items-center gap-1.5 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 shrink-0"></span>
                                <span>{{ $err }}</span>
                            </p>
                        @endforeach
                    </div>
                @endif

                <!-- Formulir Login Utama (Logic Sama Persis Seperti Login Awal) -->
                <form action="{{ route('login.post') }}" method="POST" class="space-y-3.5 lg:space-y-4">
                    @csrf

                    <!-- Field 1: Username -->
                    <div class="space-y-1.5">
                        <label for="login-input" class="block text-xs font-semibold text-[#290024]">
                            Username
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#D4954D]">
                                <i data-lucide="user" class="w-4 h-4"></i>
                            </span>
                            <input type="text"
                                   name="login"
                                   id="login-input"
                                   value="{{ old('login') }}"
                                   required
                                   autofocus
                                   autocomplete="username"
                                   autocapitalize="none"
                                   spellcheck="false"
                                   placeholder="Masukkan username Anda"
                                   class="w-full bg-[#F2F5E2] lg:bg-white text-xs sm:text-sm text-[#290024] placeholder:text-[#775533]/50 pl-11 pr-4 py-3.5 rounded-xl border border-[#775533]/25 lg:border-[#D4954D]/30 focus:border-[#D4954D] focus:ring-4 focus:ring-[#D4954D]/15 focus:outline-none transition-all shadow-xs">
                        </div>
                    </div>

                    <!-- Field 2: Kata Sandi -->
                    <div class="space-y-1.5">
                        <label for="password-input" class="block text-xs font-semibold text-[#290024]">
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#D4954D]">
                                <i data-lucide="lock" class="w-4 h-4"></i>
                            </span>
                            <input type="password"
                                   name="password"
                                   id="password-input"
                                   required
                                   autocomplete="current-password"
                                   placeholder="••••••••"
                                   class="w-full bg-[#F2F5E2] lg:bg-white text-xs sm:text-sm text-[#290024] placeholder:text-[#775533]/50 pl-11 pr-11 py-3.5 rounded-xl border border-[#775533]/25 lg:border-[#D4954D]/30 focus:border-[#D4954D] focus:ring-4 focus:ring-[#D4954D]/15 focus:outline-none transition-all shadow-xs">
                            <button type="button"
                                    onclick="togglePasswordVisibility()"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-[#775533] hover:text-[#290024] lg:hover:text-[#D4954D] transition-colors p-1"
                                    aria-label="Tampilkan atau sembunyikan kata sandi">
                                <i id="eye-icon" data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Baris Opsi: Ingat Saya & Lupa Kata Sandi -->
                    <div class="flex items-center justify-between text-xs pt-0.5">
                        <label class="flex items-center gap-2 cursor-pointer text-[#290024] select-none">
                            <input type="checkbox"
                                   name="remember"
                                   value="1"
                                   @checked(old('remember'))
                                   class="w-4 h-4 rounded border-[#E3DEA4] text-[#290024] accent-[#290024] focus:ring-1 focus:ring-[#290024] cursor-pointer">
                            <span class="text-xs font-medium">Ingat Saya</span>
                        </label>

                        <span class="text-[11px] sm:text-xs text-[#775533]">
                            Lupa kata sandi? Hubungi admin.
                        </span>
                    </div>

                    <!-- Tombol Masuk (Label: "Masuk") -->
                    <div class="pt-1.5">
                        <button type="submit"
                                id="btn-login-submit"
                                class="w-full py-3.5 rounded-full bg-gradient-to-r from-[#D4954D] via-[#dc9f57] to-[#c7883e] hover:brightness-105 active:scale-[0.99] text-[#1a0815] font-bold text-xs sm:text-sm tracking-wide shadow-md hover:shadow-[0_8px_25px_rgba(212,149,77,0.35)] transition-all flex items-center justify-center gap-2 group">
                            <span>Masuk</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 stroke-[2.5] transition-transform group-hover:translate-x-1"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer Informasi Peran: Admin • Owner • Kasir -->
            <div class="pt-4 text-center border-t border-[#775533]/15 lg:border-[#D4954D]/20 mt-4">
                <div class="flex items-center justify-center gap-2 sm:gap-2.5 text-[11px] sm:text-xs text-[#775533]">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/80 border border-[#D4954D]/25 shadow-2xs font-medium text-[#290024]">
                        <i data-lucide="shield" class="w-3.5 h-3.5 text-[#D4954D]"></i> Admin
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/80 border border-[#D4954D]/25 shadow-2xs font-medium text-[#290024]">
                        <i data-lucide="crown" class="w-3.5 h-3.5 text-[#D4954D]"></i> Owner
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/80 border border-[#D4954D]/25 shadow-2xs font-medium text-[#290024]">
                        <i data-lucide="calculator" class="w-3.5 h-3.5 text-[#D4954D]"></i> Kasir
                    </span>
                </div>
            </div>

            <!-- Watermark Logo & Nama Team -->
            <x-team-watermark theme="light" class="mt-3" />
        </div>

    </div>

    <!-- Script Logika: Toggle Password, Loading State & Ikon Lucide -->
    <script>
        lucide.createIcons();

        function togglePasswordVisibility() {
            const passInput = document.getElementById('password-input');
            const eyeIcon = document.getElementById('eye-icon');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passInput.type = 'password';
                eyeIcon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }

        const loginForm = document.querySelector('form');
        if (loginForm) {
            loginForm.addEventListener('submit', function () {
                const btn = document.getElementById('btn-login-submit');
                if (btn) {
                    btn.classList.add('opacity-85', 'cursor-not-allowed');
                    btn.innerHTML = `<span class="inline-block w-4 h-4 border-2 border-[#1a0815] border-t-transparent rounded-full animate-spin"></span><span>Memproses...</span>`;
                }
            });
        }
    </script>
</body>
</html>
