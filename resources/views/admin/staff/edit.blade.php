@extends('admin.layouts.app')

@section('title', 'Edit Staf - ' . $user->name)
@section('page_title', 'Edit Staf')

@section('content')
<div class="max-w-3xl mx-auto space-y-5 sm:space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-xs text-cafe-muted">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-cafe-dark flex items-center gap-1">
            <i data-lucide="home" class="w-3.5 h-3.5"></i>
            <span>Admin</span>
        </a>
        <i data-lucide="chevron-right" class="w-3 h-3 text-cafe-muted/60"></i>
        <a href="{{ route('admin.staff.index') }}" class="hover:text-cafe-dark">
            Pengguna (Staf/Kasir)
        </a>
        <i data-lucide="chevron-right" class="w-3 h-3 text-cafe-muted/60"></i>
        <span class="font-semibold text-cafe-dark">Edit: {{ $user->name }}</span>
    </nav>

    <!-- Header with Back Button -->
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('admin.staff.index') }}"
               class="flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-2xl bg-cafe-beige/60 text-cafe-dark hover:bg-cafe-beige transition-all active:scale-95 shrink-0"
               title="Kembali ke Daftar Staf">
                <i data-lucide="arrow-left" class="w-4 h-4 sm:w-5 sm:h-5"></i>
            </a>
            <div class="min-w-0">
                <h2 class="font-serif text-lg sm:text-2xl font-bold text-cafe-dark truncate">Edit Staf: {{ $user->name }}</h2>
                <p class="text-[11px] sm:text-xs text-cafe-muted truncate">Perbarui profil, peran, atau status akses pengguna</p>
            </div>
        </div>
    </div>

    <!-- Error Summary if any -->
    @if($errors->any())
        <div class="p-4 rounded-2xl bg-[#B03A2E]/10 border border-[#B03A2E]/30 text-cafe-danger text-xs space-y-1 animate-in fade-in">
            <div class="flex items-center gap-2 font-bold">
                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                <span>Terdapat kesalahan pada formulir:</span>
            </div>
            <ul class="list-disc list-inside pl-1 space-y-0.5 text-[11px]">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-cafe-card rounded-2xl sm:rounded-3xl border border-cafe-beige/60 shadow-soft overflow-hidden">
        <form method="POST" action="{{ route('admin.staff.update', $user->id) }}" class="p-5 sm:p-8 space-y-5 text-xs">
            @csrf

            <!-- Section 1: Profil Pengguna -->
            <div>
                <h4 class="font-serif font-bold text-xs sm:text-sm text-cafe-dark flex items-center gap-2 pb-3 border-b border-cafe-beige/40">
                    <span class="w-6 h-6 rounded-lg bg-cafe-gold/20 text-cafe-gold flex items-center justify-center">
                        <i data-lucide="user" class="w-3.5 h-3.5"></i>
                    </span>
                    Informasi Profil
                </h4>

                <div class="mt-4 space-y-4">
                    <!-- Nama Lengkap -->
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark block text-xs">
                            Nama Lengkap <span class="text-cafe-danger">*</span>
                        </label>
                        <div class="relative">
                            <i data-lucide="user" class="w-4 h-4 text-cafe-muted/60 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            <input type="text"
                                   name="name"
                                   value="{{ old('name', $user->name) }}"
                                   required
                                   placeholder="misal: Siti Rahma"
                                   class="w-full bg-[#FAFCF3] focus:bg-white text-xs sm:text-sm text-cafe-dark pl-10 pr-3.5 py-2.5 sm:py-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:ring-1 focus:ring-cafe-gold/40 focus:outline-none transition-all shadow-sm">
                        </div>
                    </div>

                    <!-- Grid: Username & No. Telepon -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Username -->
                        <div class="space-y-1">
                            <label class="font-semibold text-cafe-dark block text-xs">
                                Username <span class="text-cafe-danger">*</span>
                            </label>
                            <div class="relative">
                                <i data-lucide="at-sign" class="w-4 h-4 text-cafe-muted/60 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                                <input type="text"
                                       name="username"
                                       value="{{ old('username', $user->username) }}"
                                       required
                                       placeholder="siti_kasir"
                                       autocomplete="off"
                                       class="w-full bg-[#FAFCF3] focus:bg-white text-xs sm:text-sm text-cafe-dark font-mono pl-10 pr-3.5 py-2.5 sm:py-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:ring-1 focus:ring-cafe-gold/40 focus:outline-none transition-all shadow-sm">
                            </div>
                            <p class="text-[10px] text-cafe-muted">Hanya huruf kecil, angka, titik, dan garis bawah</p>
                        </div>

                        <!-- No. Telepon -->
                        <div class="space-y-1">
                            <label class="font-semibold text-cafe-dark block text-xs">
                                No. Telepon / WhatsApp
                            </label>
                            <div class="relative">
                                <i data-lucide="phone" class="w-4 h-4 text-cafe-muted/60 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                                <input type="text"
                                       name="phone"
                                       value="{{ old('phone', $user->phone) }}"
                                       placeholder="08123456789"
                                       class="w-full bg-[#FAFCF3] focus:bg-white text-xs sm:text-sm text-cafe-dark pl-10 pr-3.5 py-2.5 sm:py-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:ring-1 focus:ring-cafe-gold/40 focus:outline-none transition-all shadow-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Akses & Keamanan -->
            <div class="pt-2">
                <h4 class="font-serif font-bold text-xs sm:text-sm text-cafe-dark flex items-center gap-2 pb-3 border-b border-cafe-beige/40">
                    <span class="w-6 h-6 rounded-lg bg-cafe-gold/20 text-cafe-gold flex items-center justify-center">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                    </span>
                    Akses &amp; Keamanan Akun
                </h4>

                <div class="mt-4 space-y-4">
                    <!-- Grid: Peran & Status -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Peran / Jabatan -->
                        <div class="space-y-1">
                            <label class="font-semibold text-cafe-dark block text-xs">
                                Peran / Jabatan <span class="text-cafe-danger">*</span>
                            </label>
                            <div class="relative">
                                <i data-lucide="shield" class="w-4 h-4 text-cafe-muted/60 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                                <select name="role" required class="w-full bg-[#FAFCF3] focus:bg-white text-xs sm:text-sm text-cafe-dark pl-10 pr-8 py-2.5 sm:py-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:ring-1 focus:ring-cafe-gold/40 focus:outline-none transition-all shadow-sm cursor-pointer appearance-none">
                                    <option value="kasir" {{ old('role', $user->role) === 'kasir' ? 'selected' : '' }}>Kasir (Kasir &amp; POS)</option>
                                    <option value="staf" {{ old('role', $user->role) === 'staf' ? 'selected' : '' }}>Staf (Barista &amp; Dapur)</option>
                                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrator (Akses Penuh)</option>
                                </select>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-cafe-muted/60 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            </div>
                        </div>

                        <!-- Status Akun -->
                        <div class="space-y-1">
                            <label class="font-semibold text-cafe-dark block text-xs">
                                Status Akun <span class="text-cafe-danger">*</span>
                            </label>
                            <div class="relative">
                                <i data-lucide="activity" class="w-4 h-4 text-cafe-muted/60 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                                <select name="status" required class="w-full bg-[#FAFCF3] focus:bg-white text-xs sm:text-sm text-cafe-dark pl-10 pr-8 py-2.5 sm:py-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:ring-1 focus:ring-cafe-gold/40 focus:outline-none transition-all shadow-sm cursor-pointer appearance-none">
                                    <option value="aktif" {{ old('status', $user->status) === 'aktif' ? 'selected' : '' }}>Aktif (Dapat Login)</option>
                                    <option value="nonaktif" {{ old('status', $user->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif (Akses Dikunci)</option>
                                </select>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-cafe-muted/60 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Ganti Password (Opsional) -->
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark block text-xs">
                            Ganti Password (Opsional)
                        </label>
                        <div class="relative">
                            <i data-lucide="lock" class="w-4 h-4 text-cafe-muted/60 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            <input type="password"
                                   name="password"
                                   placeholder="Kosongkan jika tidak ingin mengubah password"
                                   autocomplete="new-password"
                                   class="w-full bg-[#FAFCF3] focus:bg-white text-xs sm:text-sm text-cafe-dark pl-10 pr-3.5 py-2.5 sm:py-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:ring-1 focus:ring-cafe-gold/40 focus:outline-none transition-all shadow-sm">
                        </div>
                        <p class="text-[10px] text-cafe-muted">Biarkan kosong jika tidak ada perubahan password untuk pengguna ini</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Footer -->
            <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-3 pt-6 border-t border-cafe-beige/60">
                <a href="{{ route('admin.staff.index') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl border border-cafe-beige bg-cafe-card hover:bg-cafe-beige/60 text-xs font-semibold text-cafe-dark transition-all active:scale-95 text-center">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Kembali</span>
                </a>

                <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3 rounded-xl bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all active:scale-95 text-center">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
