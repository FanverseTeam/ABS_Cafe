<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kasir Dashboard') - ABS Cafe (Accessories & Cafe)</title>
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
                extend: {
                    colors: {
                        cafe: {
                            dark: '#290024',
                            brown: '#775533',
                            gold: '#D4954D',
                            cream: '#F2F5E2',
                            beige: '#E3DEA4',
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
                        serif: ['"Noto Serif"', 'Georgia', 'serif'],
                        sans: ['"Poppins"', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                    },
                    borderRadius: {
                        '2xl': '1rem',
                        '3xl': '1.5rem',
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(41, 0, 36, 0.06), 0 2px 6px -1px rgba(41, 0, 36, 0.04)',
                        'soft-lg': '0 10px 25px -3px rgba(41, 0, 36, 0.08), 0 4px 12px -2px rgba(41, 0, 36, 0.04)',
                    }
                }
            }
        }
    </script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin-custom.css') }}">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom JS -->
    <script src="{{ asset('js/admin-app.js') }}"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F2F5E2;
            color: #290024;
        }
        h1, h2, h3, h4, .font-serif {
            font-family: 'Noto Serif', Georgia, serif;
        }
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(227, 222, 164, 0.4);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(119, 85, 51, 0.3);
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(41, 0, 36, 0.5);
        }
        @media print {
            body * { visibility: hidden; }
            #printable-receipt, #printable-receipt * { visibility: visible; }
            #printable-receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 80mm;
                margin: 0 auto;
                padding: 10px;
                background: white;
                color: black;
            }
        }
    </style>
</head>
<body class="antialiased min-h-screen bg-cafe-cream text-cafe-text">

    @include('kasir.layouts.sidebar')

    <div class="flex-1 flex flex-col min-w-0 lg:pl-72 transition-all min-h-screen">
        @include('kasir.layouts.topbar')

        <main class="flex-1 p-3 sm:p-6 lg:p-8 landscape:p-3 sm:landscape:p-5 pb-[calc(5.5rem+env(safe-area-inset-bottom))] landscape:pb-[calc(4rem+env(safe-area-inset-bottom))] lg:pb-8 lg:landscape:pb-8 relative min-w-0">
            <div class="fixed bottom-0 right-0 w-96 h-96 pointer-events-none opacity-5 -z-10">
                <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M40 160 C70 120, 120 120, 160 40 C140 100, 100 140, 40 160 Z" fill="#290024"/>
                    <path d="M20 180 C80 160, 140 100, 180 20" stroke="#290024" stroke-width="2"/>
                </svg>
            </div>

            <div class="relative max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Mobile Bottom Navigation Bar -->
    <x-bottom-nav role="kasir" />

    <!-- Toast Notifications (elevated above bottom nav on mobile) -->
    <div id="toast-container" class="fixed bottom-[calc(4.75rem+env(safe-area-inset-bottom))] landscape:bottom-[calc(3.75rem+env(safe-area-inset-bottom))] lg:bottom-5 right-3 sm:right-5 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none">
        @if(session('success'))
            <div class="toast-item pointer-events-auto flex items-start gap-3 p-4 rounded-2xl shadow-soft-lg border bg-[#FAFCF3]/95 border-[#4F7A4A]/30 text-cafe-text backdrop-blur-md animate-in slide-in-from-bottom-5 transition-all">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-cafe-success shrink-0 mt-0.5"></i>
                <div class="flex-1 min-w-0">
                    <h5 class="font-semibold text-xs text-cafe-dark">Berhasil</h5>
                    <p class="text-xs text-cafe-muted mt-0.5 leading-snug">{{ session('success') }}</p>
                </div>
                <button onclick="this.closest('.toast-item').remove()" class="text-cafe-muted hover:text-cafe-dark"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
        @endif

        @if(session('error'))
            <div class="toast-item pointer-events-auto flex items-start gap-3 p-4 rounded-2xl shadow-soft-lg border bg-[#FAFCF3]/95 border-[#B03A2E]/30 text-cafe-text backdrop-blur-md animate-in slide-in-from-bottom-5 transition-all">
                <i data-lucide="alert-circle" class="w-5 h-5 text-cafe-danger shrink-0 mt-0.5"></i>
                <div class="flex-1 min-w-0">
                    <h5 class="font-semibold text-xs text-cafe-dark">Peringatan</h5>
                    <p class="text-xs text-cafe-muted mt-0.5 leading-snug">{{ session('error') }}</p>
                </div>
                <button onclick="this.closest('.toast-item').remove()" class="text-cafe-muted hover:text-cafe-dark"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
        @endif
    </div>

    <script>
        lucide.createIcons();

        function showToast(title, message, type = 'success') {
            const container = document.getElementById('toast-container');
            const item = document.createElement('div');
            const borderColor = type === 'success' ? 'border-[#4F7A4A]/30' : (type === 'error' ? 'border-[#B03A2E]/30' : 'border-cafe-gold/30');
            const iconColor = type === 'success' ? 'text-cafe-success' : (type === 'error' ? 'text-cafe-danger' : 'text-cafe-gold');
            const iconName = type === 'success' ? 'check-circle-2' : (type === 'error' ? 'alert-circle' : 'info');

            item.className = `toast-item pointer-events-auto flex items-start gap-3 p-4 rounded-2xl shadow-soft-lg border bg-[#FAFCF3]/95 ${borderColor} text-cafe-text backdrop-blur-md transition-all duration-300`;
            item.innerHTML = `
                <i data-lucide="${iconName}" class="w-5 h-5 ${iconColor} shrink-0 mt-0.5"></i>
                <div class="flex-1 min-w-0">
                    <h5 class="font-semibold text-xs text-cafe-dark">${title}</h5>
                    <p class="text-xs text-cafe-muted mt-0.5 leading-snug">${message || ''}</p>
                </div>
                <button onclick="this.closest('.toast-item').remove()" class="text-cafe-muted hover:text-cafe-dark"><i data-lucide="x" class="w-4 h-4"></i></button>
            `;
            container.appendChild(item);
            lucide.createIcons();
            setTimeout(() => { if (item.parentNode) item.remove(); }, 4500);
        }

        // Auto dismiss existing toasts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.toast-item').forEach(el => el.remove());
        }, 5000);
    </script>

    @include('partials.confirm-modal')
    @include('components.mobile.confirm-sheet')

    @stack('scripts')
</body>
</html>