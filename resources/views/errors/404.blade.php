@php
    $backUrl = route('login');
    $backLabel = 'Kembali ke Halaman Masuk';
    if (auth()->check()) {
        $role = auth()->user()->primaryRole();
        if ($role === 'owner') {
            $backUrl = route('owner.dashboard');
            $backLabel = 'Kembali ke Dashboard Owner';
        } elseif ($role === 'admin') {
            $backUrl = route('admin.dashboard');
            $backLabel = 'Kembali ke Dashboard Admin';
        }
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan - ABS Cafe</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: { cafe: {
                    dark: '#290024', brown: '#775533', gold: '#D4954D', cream: '#F2F5E2',
                    beige: '#E3DEA4', card: '#FAFCF3', text: '#290024', muted: '#775533',
                    success: '#4F7A4A', warning: '#C98A2B', danger: '#B03A2E', info: '#5B7C99',
                } },
                fontFamily: { serif: ['"Playfair Display"', 'Georgia', 'serif'], sans: ['"Poppins"', 'sans-serif'] },
            } }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="antialiased min-h-screen bg-cafe-cream text-cafe-text font-sans flex items-center justify-center p-6">
    <div class="max-w-lg w-full bg-cafe-card rounded-3xl shadow-lg border border-cafe-beige/60 p-8 sm:p-10 text-center">
        <div class="w-16 h-16 rounded-2xl bg-cafe-gold/15 text-cafe-gold flex items-center justify-center mx-auto">
            <i data-lucide="coffee" class="w-8 h-8"></i>
        </div>
        <p class="font-serif text-5xl font-bold text-cafe-dark mt-6">404</p>
        <h1 class="font-serif text-xl font-bold text-cafe-dark mt-2">Halaman Tidak Ditemukan</h1>
        <p class="text-sm text-cafe-muted mt-2 leading-relaxed">
            Sepertinya halaman yang Anda cari sudah dipindahkan atau tidak pernah ada. Mari kembali menikmati kopi.
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
