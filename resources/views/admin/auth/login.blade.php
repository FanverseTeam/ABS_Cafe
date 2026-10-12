<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Admin - ABS Cafe (Accessories & Cafe)</title>

    <!-- Google Fonts: Playfair Display & Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,600&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (via CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cafe: {
                            dark: '#4A1F14',
                            brown: '#7A4A2B',
                            gold: '#D4A76A',
                            cream: '#F6EDE0',
                            beige: '#EFE0CC',
                            card: '#FBF6EE',
                            text: '#3B1E14',
                            muted: '#8A6A55',
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
                    borderRadius: {
                        '2xl': '1rem',
                        '3xl': '1.5rem',
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(74, 31, 20, 0.06), 0 2px 6px -1px rgba(74, 31, 20, 0.04)',
                        'soft-lg': '0 10px 25px -3px rgba(74, 31, 20, 0.08), 0 4px 12px -2px rgba(74, 31, 20, 0.04)',
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="antialiased min-h-screen bg-cafe-cream flex items-center justify-center p-4 sm:p-6 lg:p-10 font-sans text-cafe-text">

    <div class="w-full max-w-5xl bg-cafe-card rounded-3xl shadow-soft-lg border border-cafe-beige/60 overflow-hidden grid grid-cols-1 lg:grid-cols-2">
        <!-- Kolom Kiri: Ilustrasi Suasana Kafe & Tagline -->
        <div class="relative bg-[#4A1F14] text-[#F6EDE0] p-8 sm:p-12 flex flex-col justify-between overflow-hidden">
            <!-- Background Image Overlay -->
            <div class="absolute inset-0 opacity-30 bg-cover bg-center mix-blend-overlay" style="background-image: url('https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=1000&auto=format&fit=crop&q=80')"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#3B1E14] via-[#4A1F14]/90 to-transparent"></div>

            <!-- Top Logo -->
            <div class="relative z-10 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#D4A76A] via-[#C59253] to-[#7A4A2B] p-0.5 shadow-sm shrink-0">
                    <div class="w-full h-full rounded-full bg-[#4A1F14] flex items-center justify-center border border-[#D4A76A]/40">
                        <span class="font-serif font-black tracking-tighter text-xs text-[#D4A76A]">ABS</span>
                    </div>
                </div>
                <div class="flex flex-col">
                    <span class="font-serif font-bold text-lg tracking-tight leading-none text-[#F6EDE0]">ABS Cafe</span>
                    <span class="text-[10px] tracking-widest uppercase font-medium text-[#D4A76A] mt-0.5">Accessories & Cafe</span>
                </div>
            </div>

            <!-- Middle Tagline & Info -->
            <div class="relative z-10 my-10 lg:my-16 space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-md text-[#D4A76A] text-xs font-medium border border-[#D4A76A]/20">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                    <span>Back-Office Management</span>
                </div>

                <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-[#FBF6EE] leading-tight">
                    Good Coffee, <br>
                    <span class="text-[#D4A76A] italic font-serif">Good Mood.</span>
                </h2>

                <p class="text-xs sm:text-sm text-[#F6EDE0]/80 max-w-md leading-relaxed">
                    Portal administrasi resmi ABS Cafe Boyolangu, Tulungagung. Kelola menu, pesanan pelanggan, status meja, dan stok harian dengan nuansa hangat dan elegan.
                </p>
            </div>

            <!-- Footer Badge -->
            <div class="relative z-10 pt-6 border-t border-white/10 flex items-center justify-between text-xs text-[#F6EDE0]/60">
                <div class="flex items-center gap-2">
                    <i data-lucide="coffee" class="w-4 h-4 text-[#D4A76A]"></i>
                    <span>ABS Cafe &bull; Tulungagung</span>
                </div>
                <span>SMKN 1 Boyolangu</span>
            </div>
        </div>

        <!-- Kolom Kanan: Form Login di Kartu Krem -->
        <div class="p-8 sm:p-12 lg:p-16 flex flex-col justify-center bg-[#FBF6EE]">
            <div class="max-w-md w-full mx-auto space-y-6">
                <div>
                    <h3 class="font-serif text-2xl sm:text-3xl font-bold text-cafe-dark">
                        Masuk Administrator
                    </h3>
                    <p class="text-xs sm:text-sm text-cafe-muted mt-1">
                        Silakan masukkan email atau username administrator Anda.
                    </p>
                </div>

                <!-- Error Messages -->
                @if(session('error'))
                    <div class="p-3.5 rounded-2xl bg-[#B03A2E]/10 border border-[#B03A2E]/20 text-[#B03A2E] text-xs leading-relaxed flex items-start gap-2.5">
                        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 mt-0.5"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-3.5 rounded-2xl bg-[#B03A2E]/10 border border-[#B03A2E]/20 text-[#B03A2E] text-xs leading-relaxed">
                        @foreach ($errors->all() as $err)
                            <p>&bull; {{ $err }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Email / Username Input -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-cafe-text">Email atau Username</label>
                        <div class="relative">
                            <i data-lucide="mail" class="w-4 h-4 text-cafe-muted absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            <input type="text" name="login" id="login-input" value="{{ old('login', 'admin@abscafe.com') }}" required placeholder="admin@abscafe.com" class="w-full bg-[#EFE0CC]/50 focus:bg-white text-xs sm:text-sm text-cafe-text placeholder:text-cafe-muted/60 pl-11 pr-4 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all">
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-semibold text-cafe-text">Kata Sandi</label>
                            <a href="javascript:void(0)" onclick="alert('Untuk demonstrasi proyek, gunakan akun demo: admin@abscafe.com / admin123')" class="text-xs text-cafe-brown hover:text-cafe-dark hover:underline font-medium">Lupa password?</a>
                        </div>
                        <div class="relative">
                            <i data-lucide="lock" class="w-4 h-4 text-cafe-muted absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            <input type="password" name="password" id="password-input" value="admin123" required placeholder="••••••••" class="w-full bg-[#EFE0CC]/50 focus:bg-white text-xs sm:text-sm text-cafe-text placeholder:text-cafe-muted/60 pl-11 pr-11 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all">
                            <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-cafe-muted hover:text-cafe-dark transition-colors p-1">
                                <i id="eye-icon" data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-xs text-cafe-text select-none">
                            <input type="checkbox" name="remember" value="1" checked class="w-4 h-4 rounded text-cafe-dark accent-[#4A1F14] focus:ring-cafe-gold">
                            <span>Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-3.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs sm:text-sm shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                        <span>Masuk ke Dashboard</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </form>

                <!-- Demo Account Helper Box -->
                <div class="pt-4 border-t border-cafe-beige/60">
                    <div class="p-3.5 rounded-2xl bg-cafe-beige/40 border border-cafe-beige/70 flex items-start gap-3">
                        <i data-lucide="shield-check" class="w-5 h-5 text-cafe-gold shrink-0 mt-0.5"></i>
                        <div class="text-xs">
                            <p class="font-semibold text-cafe-dark">Akun Demo Administrator:</p>
                            <p class="text-cafe-muted mt-0.5 font-mono text-[11px]">
                                Email: <strong>admin@abscafe.com</strong> <br>
                                Password: <strong>admin123</strong>
                            </p>
                            <button type="button" onclick="fillDemoCredentials()" class="mt-2 text-[11px] font-bold text-cafe-brown hover:text-cafe-dark underline">
                                Gunakan Akun Demo Otomatis
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

        function fillDemoCredentials() {
            document.getElementById('login-input').value = 'admin@abscafe.com';
            document.getElementById('password-input').value = 'admin123';
        }
    </script>
</body>
</html>
