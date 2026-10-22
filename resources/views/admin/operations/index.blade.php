@extends('admin.layouts.app')

@section('title', 'Meja & Metode Pembayaran')
@section('page_title', 'Meja & Metode Pembayaran')

@section('content')
    @php($tab = request('tab', 'meja'))

    <div class="space-y-0 lg:space-y-6">
        {{-- Breadcrumb + H1 (Desktop only) --}}
        <div class="hidden lg:block space-y-4">
            <nav class="flex items-center gap-1.5 text-xs text-cafe-muted">
                <a href="{{ route('admin.staff.index') }}" class="hover:text-cafe-dark flex items-center gap-1">
                    <i data-lucide="home" class="w-3.5 h-3.5"></i>
                    <span>Admin</span>
                </a>
                <i data-lucide="chevron-right" class="w-3 h-3 text-cafe-muted/60"></i>
                <span class="font-semibold text-cafe-dark">Meja &amp; Metode Pembayaran</span>
            </nav>

            <div>
                <h2 class="font-serif text-2xl font-bold text-cafe-dark">Meja &amp; Metode Pembayaran</h2>
                <p class="text-xs text-cafe-muted mt-0.5">Atur denah meja kafe dan metode pembayaran yang tersedia di kasir.</p>
            </div>
        </div>

        {{-- Tab switcher: Mobile/Tablet sticky segmented control --}}
        <div class="lg:hidden sticky top-16 z-20 bg-[#F2F5E2] pt-2 pb-3 -mx-3.5 px-3.5 sm:-mx-5 sm:px-5">
            <div class="grid grid-cols-2 gap-1 bg-[#E3DEA4]/50 p-1 rounded-2xl">
                <a href="{{ route('admin.operations.index', ['tab' => 'meja']) }}"
                   class="flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ $tab !== 'pembayaran' ? 'bg-[#290024] text-[#FAFCF3] shadow-sm' : 'text-[#775533] hover:bg-[#E3DEA4]/60' }}">
                    <i data-lucide="layout-grid" class="w-3.5 h-3.5"></i>
                    <span>Denah Meja</span>
                </a>
                <a href="{{ route('admin.operations.index', ['tab' => 'pembayaran']) }}"
                   class="flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ $tab === 'pembayaran' ? 'bg-[#290024] text-[#FAFCF3] shadow-sm' : 'text-[#775533] hover:bg-[#E3DEA4]/60' }}">
                    <i data-lucide="credit-card" class="w-3.5 h-3.5"></i>
                    <span>Metode Pembayaran</span>
                </a>
            </div>
        </div>

        {{-- Desktop tabs --}}
        <div class="hidden lg:flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
            <a href="{{ route('admin.operations.index', ['tab' => 'meja']) }}"
               class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all {{ $tab !== 'pembayaran' ? 'bg-[#290024] text-[#FAFCF3] shadow-sm' : 'bg-[#FAFCF3] text-[#775533] border border-[#E3DEA4] hover:bg-[#E3DEA4]' }}">
                Meja
            </a>
            <a href="{{ route('admin.operations.index', ['tab' => 'pembayaran']) }}"
               class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all {{ $tab === 'pembayaran' ? 'bg-[#290024] text-[#FAFCF3] shadow-sm' : 'bg-[#FAFCF3] text-[#775533] border border-[#E3DEA4] hover:bg-[#E3DEA4]' }}">
                Metode Pembayaran
            </a>
        </div>

        @if($tab === 'pembayaran')
            @include('admin.operations.partials.payments')
        @else
            @include('admin.operations.partials.tables')
        @endif
    </div>
@endsection
