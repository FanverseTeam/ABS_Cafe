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
    </style>
</head>
<body class="antialiased min-h-screen bg-[#F2F5E2] flex items-center justify-center p-0 sm:p-4 lg:p-8 xl:p-12 font-sans text-[#290024] selection:bg-[#D4954D] selection:text-[#290024]">

    <!-- Container Mockup Mobile Frame di Mobile / Kartu 2-Kolom Elegan di Desktop -->
    <div class="w-full max-w-md sm:max-w-[420px] lg:max-w-4xl xl:max-w-5xl h-screen sm:h-[820px] lg:h-auto lg:min-h-[620px] max-h-screen sm:max-h-[860px] lg:max-h-[92vh] bg-[#290024] sm:rounded-[2.5rem] shadow-[0_20px_60px_-15px_rgba(41,0,36,0.3)] lg:shadow-[0_25px_70px_-15px_rgba(41,0,36,0.25)] sm:border border-[#E3DEA4] overflow-hidden flex flex-col lg:grid lg:grid-cols-12 justify-between relative">

        <!-- ============================================================== -->
        <!-- AREA ATAS (MOBILE) / KOLOM KIRI (DESKTOP): BRANDING & HEADER   -->
        <!-- ============================================================== -->
        <div class="relative bg-gradient-to-b from-[#290024] to-[#290024] p-6 pb-8 lg:p-10 xl:p-12 flex flex-col justify-between overflow-hidden shrink-0 lg:col-span-5 xl:col-span-5 lg:h-full">

            <!-- Background Ambient Glow & Curve Line Art (Khusus Desktop) -->
            <div class="absolute -top-24 -left-24 w-72 h-72 rounded-full bg-[#D4954D]/10 blur-3xl pointer-events-none hidden lg:block"></div>
            <div class="absolute -bottom-20 -right-20 w-80 h-80 rounded-full bg-[#E3DEA4]/10 blur-3xl pointer-events-none hidden lg:block"></div>
            <div class="absolute -bottom-10 -right-10 w-64 h-64 pointer-events-none opacity-20 hidden lg:block">
                <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="100" cy="100" r="80" stroke="#D4954D" stroke-width="2"/>
                    <path d="M30 150 C60 80, 140 120, 170 50" stroke="#E3DEA4" stroke-width="2"/>
                </svg>
            </div>

            <!-- Baris Navigasi: Logo Brand & Badge Status -->
            <div class="relative z-10 flex items-center justify-between">
                <div class="inline-flex items-center gap-2">
                    <img src="{{ asset('images/logo-badge.png') }}"
                         alt="Logo ABS Cafe"
                         class="w-7 h-7 lg:w-8 lg:h-8 object-contain drop-shadow-xs">
                    <span class="font-serif font-bold text-sm lg:text-base text-[#F2F5E2] tracking-tight">
                        ABS Cafe
                    </span>
                </div>

                <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-[#290024] text-[#D4954D] text-[11px] font-semibold border border-[#D4954D]">
                    <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                    <span>Akses Terautentikasi</span>
                </div>
            </div>

            <!-- Teks Utama Header Sesuai Desain -->
            <div class="relative z-10 mt-6 lg:mt-10 mb-1 lg:mb-0">
                <h1 class="font-serif text-2xl sm:text-[26px] lg:text-3xl xl:text-[32px] font-bold text-[#F2F5E2] leading-snug lg:leading-tight">
                    Masuk untuk mengelola operasional dan memantau pesanan.
                </h1>
                <p class="text-xs lg:text-sm text-[#E3DEA4] mt-2 lg:mt-3 leading-relaxed">
                    Satu pintu masuk untuk Administrator, Owner, dan Kasir ABS Cafe.
                </p>

                <!-- Poin Fitur Utama (Eksklusif Tampil di Desktop Saja) -->
                <div class="hidden lg:flex flex-col gap-3.5 mt-8 pt-6 border-t border-[#E3DEA4]/20 text-xs text-[#E3DEA4]">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-[#D4954D]/20 border border-[#D4954D]/40 flex items-center justify-center text-[#D4954D] shrink-0">
                            <i data-lucide="receipt" class="w-4 h-4"></i>
                        </div>
                        <span class="font-medium">Kasir & Transaksi Cepat Terintegrasi</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-[#D4954D]/20 border border-[#D4954D]/40 flex items-center justify-center text-[#D4954D] shrink-0">
                            <i data-lucide="utensils" class="w-4 h-4"></i>
                        </div>
                        <span class="font-medium">Katalog Menu & Manajemen Meja</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-[#D4954D]/20 border border-[#D4954D]/40 flex items-center justify-center text-[#D4954D] shrink-0">
                            <i data-lucide="trending-up" class="w-4 h-4"></i>
                        </div>
                        <span class="font-medium">Pantauan Bisnis & Laporan Penjualan</span>
                    </div>
                </div>
            </div>

            <!-- Footer Kolom Kiri Desktop -->
            <div class="relative z-10 hidden lg:block pt-6 border-t border-[#E3DEA4]/10 text-[11px] text-[#E3DEA4]/60">
                &copy; {{ date('Y') }} ABS Cafe &bull; Portal Internal Staf & Pengelola
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- AREA BAWAH (MOBILE) / KOLOM KANAN (DESKTOP): FORMULIR LOGIN    -->
        <!-- ============================================================== -->
        <div class="bg-[#F2F5E2] rounded-t-[2.5rem] lg:rounded-none p-6 sm:p-8 lg:p-10 xl:p-12 pt-7 flex flex-col justify-between flex-1 shadow-[0_-12px_30px_rgba(41,0,36,0.15)] lg:shadow-none lg:border-l lg:border-[#E3DEA4] relative z-20 overflow-y-auto lg:col-span-7 xl:col-span-7 lg:h-full">
            <div class="space-y-5">
                <!-- Judul Form Login -->
                <div class="text-center lg:text-left">
                    <h2 class="font-serif text-2xl sm:text-3xl font-bold text-[#290024]">
                        Masuk
                    </h2>
                    <p class="text-xs sm:text-sm text-[#775533] mt-1">
                        Masuk ke Dashboard staf & pengelola
                    </p>
                </div>

                <!-- Notifikasi Status / Pesan Kesalahan / Berhasil Keluar -->
                @if(session('error'))
                    <div class="p-3.5 rounded-2xl bg-[#E3DEA4] border border-[#775533] text-[#290024] text-xs leading-relaxed flex items-start gap-2.5">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-[#D4954D] shrink-0 mt-0.5"></i>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('success'))
                    <div class="p-3.5 rounded-2xl bg-[#E3DEA4] border border-[#D4954D] text-[#290024] text-xs leading-relaxed flex items-start gap-2.5 shadow-2xs">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-[#775533] shrink-0 mt-0.5"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('status'))
                    <div class="p-3.5 rounded-2xl bg-[#E3DEA4] border border-[#D4954D] text-[#290024] text-xs leading-relaxed flex items-start gap-2.5 shadow-2xs">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-[#775533] shrink-0 mt-0.5"></i>
                        <span class="font-medium">{{ session('status') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-3.5 rounded-2xl bg-[#E3DEA4] border border-[#775533] text-[#290024] text-xs leading-relaxed space-y-1">
                        @foreach ($errors->all() as $err)
                            <p class="flex items-center gap-1.5 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#D4954D] shrink-0"></span>
                                <span>{{ $err }}</span>
                            </p>
                        @endforeach
                    </div>
                @endif

                <!-- Formulir Login Utama (Logic Sama Persis Seperti Login Awal) -->
                <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Field 1: Username -->
                    <div class="space-y-1.5">
                        <label for="login-input" class="block text-xs font-semibold text-[#290024]">
                            Username
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-[#775533]">
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
                                   class="w-full bg-[#F2F5E2] text-xs sm:text-sm text-[#290024] placeholder:text-[#775533] pl-11 pr-4 py-3.5 rounded-2xl border-2 border-[#290024] focus:outline-none transition-all">
                        </div>
                    </div>

                    <!-- Field 2: Kata Sandi -->
                    <div class="space-y-1.5">
                        <label for="password-input" class="block text-xs font-semibold text-[#290024]">
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-[#775533]">
                                <i data-lucide="lock" class="w-4 h-4"></i>
                            </span>
                            <input type="password"
                                   name="password"
                                   id="password-input"
                                   required
                                   autocomplete="current-password"
                                   placeholder="••••••••"
                                   class="w-full bg-[#F2F5E2] text-xs sm:text-sm text-[#290024] placeholder:text-[#775533] pl-11 pr-11 py-3.5 rounded-2xl border border-[#E3DEA4] focus:border-[#290024] focus:outline-none transition-all">
                            <button type="button"
                                    onclick="togglePasswordVisibility()"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-[#775533] hover:text-[#290024] transition-colors p-1"
                                    aria-label="Tampilkan atau sembunyikan kata sandi">
                                <i id="eye-icon" data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Baris Opsi: Ingat Saya & Lupa Kata Sandi -->
                    <div class="flex items-center justify-between text-xs pt-1">
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
                    <div class="pt-2">
                        <button type="submit"
                                class="w-full py-4 rounded-full bg-[#D4954D] hover:bg-[#E3DEA4] active:scale-[0.98] text-[#290024] font-bold text-xs sm:text-sm tracking-wide shadow-md transition-all flex items-center justify-center gap-2">
                            <span>Masuk</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 stroke-[2.5]"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer Informasi Peran: Admin • Owner • Kasir -->
            <div class="pt-5 text-center border-t border-[#E3DEA4] mt-6">
                <div class="flex items-center justify-center gap-2 sm:gap-3 text-[11px] sm:text-xs text-[#775533]">
                    <span class="inline-flex items-center gap-1 font-medium">
                        <i data-lucide="shield" class="w-3.5 h-3.5 text-[#D4954D]"></i> Admin
                    </span>
                    <span>&bull;</span>
                    <span class="inline-flex items-center gap-1 font-medium">
                        <i data-lucide="crown" class="w-3.5 h-3.5 text-[#D4954D]"></i> Owner
                    </span>
                    <span>&bull;</span>
                    <span class="inline-flex items-center gap-1 font-medium">
                        <i data-lucide="calculator" class="w-3.5 h-3.5 text-[#D4954D]"></i> Kasir
                    </span>
                </div>
            </div>
        </div>

    </div>

    <!-- Script Logika Asli: Toggle Password & Ikon Lucide -->
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
    </script>
</body>
</html>
