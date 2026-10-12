<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Owner Dashboard') - ABS Cafe (Accessories & Cafe)</title>

    <!-- Google Fonts: Playfair Display (Serif) & Poppins (Sans-serif) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,600&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (via CDN with ABS Cafe Owner Palette) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cafe: {
                            dark: '#290024',      // Midnight plum (sidebar, judul, tombol utama)
                            plum: '#3D1236',      // Gradasi plum lebih terang
                            brown: '#775533',     // Olive/coklat tua (aksen, hover)
                            olive: '#775533',
                            gold: '#D4954D',      // Apricot (highlight, badge, tombol sekunder)
                            apricot: '#D4954D',
                            bronze: '#B87C3C',    // Apricot lebih pekat
                            cream: '#F2F5E2',     // Beige lembut (background halaman)
                            beige: '#E3DEA4',     // Custard (kartu, panel, input)
                            custard: '#E3DEA4',
                            card: '#FAFCF3',      // Putih kehijauan (kartu utama)
                            text: '#290024',      // Teks utama
                            muted: '#775533',     // Teks sekunder
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
                        'soft': '0 4px 20px -2px rgba(41, 0, 36, 0.06), 0 2px 6px -1px rgba(41, 0, 36, 0.04)',
                        'soft-lg': '0 10px 25px -3px rgba(41, 0, 36, 0.10), 0 4px 12px -2px rgba(41, 0, 36, 0.05)',
                    }
                }
            }
        }
    </script>

    <!-- Custom CSS (ABS Cafe) -->
    <link rel="stylesheet" href="{{ asset('css/admin-custom.css') }}">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom JS helpers -->
    <script src="{{ asset('js/admin-app.js') }}"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F2F5E2;
            color: #290024;
        }
        h1, h2, h3, h4, .font-serif {
            font-family: 'Playfair Display', Georgia, serif;
        }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: rgba(227, 222, 164, 0.4); }
        ::-webkit-scrollbar-thumb { background: rgba(119, 85, 51, 0.3); border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(41, 0, 36, 0.5); }
        @media print {
            body * { visibility: hidden; }
            #printable-report, #printable-report * { visibility: visible; }
            #printable-report {
                position: absolute; left: 0; top: 0; width: 100%;
                margin: 0; padding: 0; background: white; color: black;
            }
        }
    </style>
</head>
<body class="antialiased min-h-screen bg-cafe-cream text-cafe-text">

    <!-- Mobile Drawer Backdrop -->
    <div id="sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-[#290024]/60 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity"></div>

    <!-- Sidebar Layout -->
    @include('owner.layouts.sidebar')

    <!-- Main Content Container -->
    <div class="flex-1 flex flex-col min-w-0 lg:pl-72 transition-all min-h-screen">
        <!-- Topbar -->
        @include('owner.layouts.topbar')

        <!-- Main Body -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 relative">
            <!-- Background Line Art Decoration -->
            <div class="fixed bottom-0 right-0 w-96 h-96 pointer-events-none opacity-5 -z-0">
                <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M40 160 C70 120, 120 120, 160 40 C140 100, 100 140, 40 160 Z" fill="#290024"/>
                    <path d="M20 180 C80 160, 140 100, 180 20" stroke="#290024" stroke-width="2"/>
                </svg>
            </div>

            <!-- Page Content -->
            <div class="relative z-10 max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none">
        @if(session('success'))
            <div class="toast-item pointer-events-auto flex items-start gap-3 p-4 rounded-2xl shadow-soft-lg border bg-[#FAFCF3]/95 border-[#4F7A4A]/30 text-cafe-text backdrop-blur-md transition-all">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-cafe-success shrink-0 mt-0.5"></i>
                <div class="flex-1 min-w-0">
                    <h5 class="font-semibold text-xs text-cafe-dark">Berhasil</h5>
                    <p class="text-xs text-cafe-muted mt-0.5 leading-snug">{{ session('success') }}</p>
                </div>
                <button onclick="this.closest('.toast-item').remove()" class="text-cafe-muted hover:text-cafe-dark">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="toast-item pointer-events-auto flex items-start gap-3 p-4 rounded-2xl shadow-soft-lg border bg-[#FAFCF3]/95 border-[#B03A2E]/30 text-cafe-text backdrop-blur-md transition-all">
                <i data-lucide="alert-circle" class="w-5 h-5 text-cafe-danger shrink-0 mt-0.5"></i>
                <div class="flex-1 min-w-0">
                    <h5 class="font-semibold text-xs text-cafe-dark">Peringatan</h5>
                    <p class="text-xs text-cafe-muted mt-0.5 leading-snug">{{ session('error') }}</p>
                </div>
                <button onclick="this.closest('.toast-item').remove()" class="text-cafe-muted hover:text-cafe-dark">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif
    </div>

    <script>
        lucide.createIcons();

        // Mobile Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('owner-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
                backdrop.classList.add('hidden');
            }
        }

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
                <button onclick="this.closest('.toast-item').remove()" class="text-cafe-muted hover:text-cafe-dark">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            `;
            container.appendChild(item);
            lucide.createIcons();
            setTimeout(() => { if (item.parentNode) item.remove(); }, 4500);
        }

        setTimeout(() => {
            document.querySelectorAll('.toast-item').forEach(el => el.remove());
        }, 5000);
    </script>

    @stack('scripts')
</body>
</html>
