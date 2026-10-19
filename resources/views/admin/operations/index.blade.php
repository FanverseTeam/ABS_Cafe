@extends('admin.layouts.app')

@section('title', 'Meja & Metode Pembayaran')
@section('page_title', 'Meja & Metode Pembayaran')

@section('content')
    @php($tab = request('tab', 'meja'))

    <div class="space-y-6">
        <nav class="flex items-center gap-1.5 text-xs text-cafe-muted mb-3">
            <a href="{{ route('admin.staff.index') }}" class="hover:text-cafe-dark flex items-center gap-1">
                <i data-lucide="home" class="w-3.5 h-3.5"></i>
                <span>Admin</span>
            </a>
            <i data-lucide="chevron-right" class="w-3 h-3 text-cafe-muted/60"></i>
            <span class="font-semibold text-cafe-dark">Meja &amp; Metode Pembayaran</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-serif text-2xl font-bold text-cafe-dark">Meja &amp; Metode Pembayaran</h2>
                <p class="text-xs text-cafe-muted mt-0.5">Atur denah meja kafe dan metode pembayaran yang tersedia di kasir.</p>
            </div>
        </div>

        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
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
