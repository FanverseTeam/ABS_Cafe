<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - ABS Cafe (Accessories & Cafe)</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">

    <!-- Google Fonts: Playfair Display & Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,600&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (via CDN with ABS Cafe Palette) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cafe: {
                            dark: '#290024',
                            plum: '#3D1236',
                            brown: '#775533',
                            olive: '#775533',
                            gold: '#D4954D',
                            apricot: '#D4954D',
                            bronze: '#B87C3C',
                            cream: '#F2F5E2',
                            beige: '#E3DEA4',
                            custard: '#E3DEA4',
                            card: '#FAFCF3',
                            text: '#290024',
                            muted: '#775533',
                            success: '#4F7A4A',
                            warning: '#C98A2B',
                            danger: '#B03A2E',
                            info: '#5B7C99',
                        }
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', 'Georgia', 'serif'],
                        sans: ['"Poppins"', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                    },
                    borderRadius: { '2xl': '1rem', '3xl': '1.5rem' },
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
</head>
<body class="antialiased min-h-screen bg-cafe-cream flex flex-col lg:flex-row items-center justify-center p-0 sm:p-6 lg:p-10 font-sans text-cafe-text">

    <div class="w-full max-w-5xl bg-cafe-card sm:rounded-3xl shadow-soft-lg border-0 sm:border border-cafe-beige/60 overflow-hidden grid grid-cols-1 lg:grid-cols-2 min-h-screen sm:min-h-0">
        <!-- Kolom Kiri: Ilustrasi Suasana Kafe & Tagline (Di mobile: Header Organik Atas) -->
        <div class="relative bg-[#290024] text-[#F2F5E2] p-6 sm:p-10 lg:p-12 flex flex-col justify-between overflow-hidden shrink-0 pb-12 lg:pb-12">
            <!-- Background Texture & Gradient Overlay -->
            <div class="absolute inset-0 opacity-25 bg-cover bg-center mix-blend-overlay" style="background-image: url('https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=1000&auto=format&fit=crop&q=80')"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-[#290024]/80 via-[#290024] to-[#290024]"></div>
            
            <!-- Organic Curve Line Art (Khas ABS Cafe Referensi) -->
            <div class="absolute -bottom-10 -right-10 w-64 h-64 pointer-events-none opacity-20">
                <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="100" cy="100" r="80" stroke="#D4954D" stroke-width="2"/>
                    <path d="M30 150 C60 80, 140 120, 170 50" stroke="#E3DEA4" stroke-width="2"/>
                </svg>
            </div>

            <!-- Top Logo & Brand -->
            <div class="relative z-10 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/Logo.png') }}" alt="ABS Cafe Logo" class="w-11 h-11 sm:w-12 sm:h-12 rounded-full object-cover shrink-0 shadow-md border border-[#D4954D]/40">
                    <div class="flex flex-col">
                        <span class="font-serif font-bold text-lg sm:text-xl tracking-tight leading-none text-[#F2F5E2]">ABS Cafe</span>
                        <span class="text-[9px] sm:text-[10px] tracking-widest uppercase font-medium text-[#D4954D] mt-0.5">Accessories & Cafe</span>
                    </div>
                </div>

                <div class="lg:hidden inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-[#D4954D] text-[10px] font-medium border border-[#D4954D]/20">
                    <i data-lucide="shield-check" class="w-3 h-3"></i>
                    <span>Portal Staf</span>
                </div>
            </div>

            <!-- Middle Tagline & Info -->
            <div class="relative z-10 my-6 sm:my-10 lg:my-16 space-y-3 sm:space-y-4">
                <div class="hidden lg:inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-md text-[#D4954D] text-xs font-medium border border-[#D4954D]/20">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                    <span>Satu Akses untuk Semua Peran</span>
                </div>

                <h2 class="font-serif text-2xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-[#FAFCF3] leading-tight">
                    Good Coffee, <br class="hidden sm:inline">
                    <span class="text-[#D4954D] italic font-serif">Good Mood.</span>
                </h2>

                <p class="text-xs sm:text-sm text-[#F2F5E2]/80 max-w-md leading-relaxed hidden sm:block">
                    Portal resmi ABS Cafe Boyolangu, Tulungagung. Masuk dengan akun Staf untuk mengelola operasional maupun
                    memantau performa bisnis Anda.
                </p>

                <!-- Role Pills -->
                <div class="flex flex-wrap gap-2 pt-1 sm:pt-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/10 text-[#F2F5E2]/90 text-[10px] sm:text-[11px] font-medium">
                        <i data-lucide="settings" class="w-3 h-3 text-[#D4954D]"></i> Admin
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/10 text-[#F2F5E2]/90 text-[10px] sm:text-[11px] font-medium">
                        <i data-lucide="crown" class="w-3 h-3 text-[#D4954D]"></i> Owner
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/10 text-[#F2F5E2]/90 text-[10px] sm:text-[11px] font-medium">
                        <i data-lucide="calculator" class="w-3 h-3 text-[#D4954D]"></i> Kasir
                    </span>
                </div>
            </div>

            <!-- Footer Badge -->
            <div class="relative z-10 pt-4 sm:pt-6 border-t border-white/10 hidden sm:flex items-center justify-between text-xs text-[#F2F5E2]/60">
                <div class="flex items-center gap-2">
                    <i data-lucide="coffee" class="w-4 h-4 text-[#D4954D]"></i>
                    <span>ABS Cafe &bull; Tulungagung</span>
                </div>
                <span>SMKN 1 Boyolangu</span>
            </div>
        </div>

        <!-- Kolom Kanan: Form Login (Di mobile: Kartu krem naik membulat) -->
        <div class="p-6 sm:p-12 lg:p-16 flex flex-col justify-center bg-[#FAFCF3] -mt-6 sm:mt-0 rounded-t-3xl sm:rounded-none relative z-20 shadow-lg sm:shadow-none flex-1">
            <div class="max-w-md w-full mx-auto space-y-6">
                <div>
                    <h3 class="font-serif text-2xl sm:text-3xl font-bold text-cafe-dark">Masuk ke Dashboard</h3>
                    <p class="text-xs sm:text-sm text-cafe-muted mt-1">
                        Gunakan email atau username Anda. Sistem akan otomatis mengenali peran akun.
                    </p>
                </div>

                <!-- Status Messages -->
                @if(session('error'))
                    <div class="p-3.5 rounded-2xl bg-[#B03A2E]/10 border border-[#B03A2E]/20 text-[#B03A2E] text-xs leading-relaxed flex items-start gap-2.5">
                        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 mt-0.5"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('success'))
                    <div class="p-3.5 rounded-2xl bg-[#4F7A4A]/10 border border-[#4F7A4A]/20 text-[#4F7A4A] text-xs leading-relaxed flex items-start gap-2.5">
                        <i data-lucide="check-circle-2" class="w-4 h-4 shrink-0 mt-0.5"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-3.5 rounded-2xl bg-[#B03A2E]/10 border border-[#B03A2E]/20 text-[#B03A2E] text-xs leading-relaxed">
                        @foreach ($errors->all() as $err)
                            <p>&bull; {{ $err }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Email / Username -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-cafe-text">Email atau Username</label>
                        <div class="relative">
                            <i data-lucide="user" class="w-4 h-4 text-cafe-muted absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            <input type="text" name="login" id="login-input" value="{{ old('login') }}" required autofocus placeholder="email atau username" class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs sm:text-sm text-cafe-text placeholder:text-cafe-muted/60 pl-11 pr-4 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-semibold text-cafe-text">Kata Sandi</label>
                            <a href="javascript:void(0)" onclick="showDemoHint()" class="text-xs text-cafe-brown hover:text-cafe-dark hover:underline font-medium">Lupa password?</a>
                        </div>
                        <div class="relative">
                            <i data-lucide="lock" class="w-4 h-4 text-cafe-muted absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            <input type="password" name="password" id="password-input" required placeholder="••••••••" class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs sm:text-sm text-cafe-text placeholder:text-cafe-muted/60 pl-11 pr-11 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all">
                            <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-cafe-muted hover:text-cafe-dark transition-colors p-1">
                                <i id="eye-icon" data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember -->
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-cafe-text select-none">
                        <input type="checkbox" name="remember" value="1" checked class="w-4 h-4 rounded accent-[#290024] focus:ring-cafe-gold">
                        <span>Ingat saya di perangkat ini</span>
                    </label>

                    <!-- Submit -->
                    <button type="submit" class="w-full py-3.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs sm:text-sm shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                        <span>Masuk ke Dashboard</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </form>

                <!-- Demo Accounts -->
                <div class="pt-4 border-t border-cafe-beige/60 space-y-2.5">
                    <p class="text-[11px] font-semibold text-cafe-muted uppercase tracking-wide">Akun Demo</p>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <div class="p-3 rounded-2xl bg-cafe-beige/40 border border-cafe-beige/70">
                            <div class="flex items-center gap-2">
                                <i data-lucide="settings" class="w-4 h-4 text-cafe-bronze shrink-0"></i>
                                <p class="text-[11px] font-bold text-cafe-dark">Administrator</p>
                            </div>
                            <p class="text-[10px] text-cafe-muted mt-1 font-mono">admin@abscafe.com</p>
                            <p class="text-[10px] text-cafe-muted font-mono">admin123</p>
                            <button type="button" onclick="useDemo('admin@abscafe.com', 'admin123')" class="mt-2 w-full text-[11px] font-bold text-cafe-brown hover:text-cafe-dark underline">
                                Gunakan akun ini
                            </button>
                        </div>

                        <div class="p-3 rounded-2xl bg-cafe-beige/40 border border-cafe-beige/70">
                            <div class="flex items-center gap-2">
                                <i data-lucide="crown" class="w-4 h-4 text-cafe-gold shrink-0"></i>
                                <p class="text-[11px] font-bold text-cafe-dark">Owner</p>
                            </div>
                            <p class="text-[10px] text-cafe-muted mt-1 font-mono">owner@abscafe.com</p>
                            <p class="text-[10px] text-cafe-muted font-mono">owner123</p>
                            <button type="button" onclick="useDemo('owner@abscafe.com', 'owner123')" class="mt-2 w-full text-[11px] font-bold text-cafe-brown hover:text-cafe-dark underline">
                                Gunakan akun ini
                            </button>
                        </div>

                        <div class="p-3 rounded-2xl bg-cafe-beige/40 border border-cafe-beige/70">
                            <div class="flex items-center gap-2">
                                <i data-lucide="calculator" class="w-4 h-4 text-cafe-warning shrink-0"></i>
                                <p class="text-[11px] font-bold text-cafe-dark">Kasir</p>
                            </div>
                            <p class="text-[10px] text-cafe-muted mt-1 font-mono">kasir@abscafe.com</p>
                            <p class="text-[10px] text-cafe-muted font-mono">kasir123</p>
                            <button type="button" onclick="useDemo('kasir@abscafe.com', 'kasir123')" class="mt-2 w-full text-[11px] font-bold text-cafe-brown hover:text-cafe-dark underline">
                                Gunakan akun ini
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

        function useDemo(login, password) {
            document.getElementById('login-input').value = login;
            document.getElementById('password-input').value = password;
            document.getElementById('login-input').focus();
        }

        function showDemoHint() {
            alert('Untuk demonstrasi proyek, gunakan salah satu akun demo yang tersedia di bawah form ini:\n\nAdministrator: admin@abscafe.com / admin123\nOwner: owner@abscafe.com / owner123\nKasir: kasir@abscafe.com / kasir123');
        }
    </script>
</body>
</html>
