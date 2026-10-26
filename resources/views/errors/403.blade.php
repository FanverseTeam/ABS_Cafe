@php
    $backUrl = route('login');
    $backLabel = 'Kembali ke Halaman Masuk';
    if (auth()->check()) {
        $role = auth()->user()->primaryRole();
        if ($role === 'owner') {
            $backUrl = route('owner.dashboard');
            $backLabel = 'Kembali ke Dashboard Owner';
        } elseif ($role === 'admin') {
            $backUrl = config('features.legacy_admin')
                ? route('admin.dashboard')
                : route('admin.staff.index');
            $backLabel = 'Kembali ke Portal Admin';
        }
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak - ABS Cafe</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-badge.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-badge.png') }}">
    @include('partials.font-preloads')
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: { cafe: {
                    dark: '#290024', brown: '#775533', gold: '#D4954D', cream: '#F2F5E2',
                    beige: '#E3DEA4', card: '#F2F5E2', text: '#290024', muted: '#775533',
                    success: '#775533', warning: '#D4954D', danger: '#775533', info: '#D4954D',
                } },
                fontFamily: { serif: ['"Noto Serif"', 'Georgia', 'serif'], sans: ['"Poppins"', 'sans-serif'] },
            } }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="antialiased min-h-screen bg-cafe-cream text-cafe-text font-sans flex items-center justify-center p-6">
    <div class="max-w-lg w-full bg-cafe-card rounded-3xl shadow-lg border border-cafe-beige/60 p-8 sm:p-10 text-center">
        <x-logo size="lg" loading="eager" class="mx-auto" />
        <p class="font-serif text-5xl font-bold text-cafe-dark mt-6">403</p>
        <h1 class="font-serif text-xl font-bold text-cafe-dark mt-2">Akses Ditolak</h1>
        <p class="text-sm text-cafe-muted mt-2 leading-relaxed">
            Maaf, Anda tidak memiliki hak akses untuk membuka halaman ini. Silakan kembali ke dashboard Anda.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 mt-7">
            <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream text-xs font-semibold transition-colors w-full sm:w-auto justify-center">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> {{ $backLabel }}
            </a>
        </div>
        <p class="text-[11px] text-cafe-muted mt-6">ABS Cafe &bull; Accessories & Cafe</p>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
