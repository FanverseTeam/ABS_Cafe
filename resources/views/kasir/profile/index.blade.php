@extends('kasir.layouts.app')

@section('title', 'Profil Saya - Kasir')
@section('page_title', 'Profil Saya')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="font-serif text-2xl sm:text-3xl font-bold text-cafe-dark">Profil Saya</h2>
        <p class="text-xs text-cafe-muted mt-1">Kelola informasi akun dan keamanan kata sandi Anda.</p>
    </div>

    @if($errors->any())
        <div class="p-3.5 rounded-2xl bg-cafe-danger/10 border border-cafe-danger/20 text-cafe-danger text-xs leading-relaxed">
            @foreach($errors->all() as $err)
                <p>&bull; {{ $err }}</p>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Identity card -->
        <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-6 text-center">
            <div class="w-20 h-20 rounded-full bg-cafe-dark text-cafe-gold font-serif font-bold text-2xl flex items-center justify-center mx-auto shadow">
                {{ $user->initials() ?? strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h3 class="font-serif text-lg font-bold text-cafe-dark mt-4">{{ $user->name }}</h3>
            <p class="text-xs text-cafe-muted">@ {{ $user->username }}</p>
            <span class="inline-flex items-center gap-1 mt-3 px-3 py-1 rounded-full bg-cafe-gold/20 text-cafe-brown text-[10px] font-semibold uppercase tracking-wide">
                <i data-lucide="calculator" class="w-3 h-3"></i> Kasir
            </span>
            <div class="mt-5 pt-4 border-t border-cafe-beige/50 text-left text-xs space-y-2">
                <div class="flex items-center gap-2 text-cafe-muted"><i data-lucide="mail" class="w-3.5 h-3.5"></i> {{ $user->email }}</div>
                <div class="flex items-center gap-2 text-cafe-muted"><i data-lucide="phone" class="w-3.5 h-3.5"></i> {{ $user->phone ?: '-' }}</div>
                <div class="flex items-center gap-2 text-cafe-muted"><i data-lucide="clock" class="w-3.5 h-3.5"></i> Login terakhir: {{ $user->last_login_at?->translatedFormat('d M Y, H:i') ?? '-' }}</div>
            </div>

            <div class="mt-5 grid grid-cols-2 gap-3">
                <div class="p-3 rounded-2xl bg-cafe-cream border border-cafe-beige/60">
                    <p class="font-serif font-bold text-cafe-dark">{{ $stats['orders_today'] }}</p>
                    <p class="text-[9px] text-cafe-muted">Pesanan Hari Ini</p>
                </div>
                <div class="p-3 rounded-2xl bg-cafe-cream border border-cafe-beige/60">
                    <p class="text-xs font-serif font-bold text-cafe-gold">Rp {{ number_format($stats['sales_today'], 0, ',', '.') }}</p>
                    <p class="text-[9px] text-cafe-muted">Penjualan Hari Ini</p>
                </div>
            </div>
        </div>

        <!-- Forms -->
        <div class="lg:col-span-2 space-y-4">
            <form action="{{ route('kasir.profile.update') }}" method="POST" class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5 space-y-4">
                @csrf
                <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2"><i data-lucide="user-round" class="w-4 h-4 text-cafe-gold"></i> Informasi Akun</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-cafe-text">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-cafe-cream/60 focus:bg-white text-sm px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-cafe-text">Username</label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" required class="w-full bg-cafe-cream/60 focus:bg-white text-sm px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-cafe-text">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full bg-cafe-cream/60 focus:bg-white text-sm px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-cafe-text">Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full bg-cafe-cream/60 focus:bg-white text-sm px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-5 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream text-xs font-semibold transition-colors">Simpan Profil</button>
                </div>
            </form>

            <form action="{{ route('kasir.profile.password') }}" method="POST" class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5 space-y-4">
                @csrf
                <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2"><i data-lucide="key-round" class="w-4 h-4 text-cafe-gold"></i> Ganti Kata Sandi</h3>
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-cafe-text">Kata Sandi Saat Ini</label>
                    <input type="password" name="current_password" required class="w-full bg-cafe-cream/60 focus:bg-white text-sm px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-cafe-text">Kata Sandi Baru</label>
                        <input type="password" name="password" required class="w-full bg-cafe-cream/60 focus:bg-white text-sm px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-cafe-text">Konfirmasi Kata Sandi</label>
                        <input type="password" name="password_confirmation" required class="w-full bg-cafe-cream/60 focus:bg-white text-sm px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-5 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream text-xs font-semibold transition-colors">Perbarui Kata Sandi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection