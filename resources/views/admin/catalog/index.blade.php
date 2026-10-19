@extends('admin.layouts.app')

@section('title', 'Manajemen Menu')
@section('page_title', 'Manajemen Menu')

@section('content')
    @php($tab = request('tab', 'menu'))

    <div class="space-y-6">
        <nav class="flex items-center gap-1.5 text-xs text-cafe-muted mb-3">
            <a href="{{ route('admin.staff.index') }}" class="hover:text-cafe-dark flex items-center gap-1">
                <i data-lucide="home" class="w-3.5 h-3.5"></i>
                <span>Admin</span>
            </a>
            <i data-lucide="chevron-right" class="w-3 h-3 text-cafe-muted/60"></i>
            <span class="font-semibold text-cafe-dark">Manajemen Menu</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-serif text-2xl font-bold text-cafe-dark">Manajemen Menu</h2>
                <p class="text-xs text-cafe-muted mt-0.5">Kelola katalog menu, kategori, stok, dan data supplier dalam satu tempat.</p>
            </div>
        </div>

        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
            <a href="{{ route('admin.catalog.index', ['tab' => 'menu']) }}"
               class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all {{ $tab !== 'stock' ? 'bg-[#290024] text-[#FAFCF3] shadow-sm' : 'bg-[#FAFCF3] text-[#775533] border border-[#E3DEA4] hover:bg-[#E3DEA4]' }}">
                Menu &amp; Kategori
            </a>
            <a href="{{ route('admin.catalog.index', ['tab' => 'stock']) }}"
               class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all {{ $tab === 'stock' ? 'bg-[#290024] text-[#FAFCF3] shadow-sm' : 'bg-[#FAFCF3] text-[#775533] border border-[#E3DEA4] hover:bg-[#E3DEA4]' }}">
                Stok &amp; Supplier
            </a>
        </div>

        @if($tab === 'stock')
            @include('admin.catalog.partials.stock', [
                'menus' => $stockMenus,
                'categories' => $activeCategories,
                'stockLogs' => $stockLogs,
            ])
            @include('admin.catalog.partials.supplier', ['suppliers' => $suppliers])
        @else
            @include('admin.catalog.partials.menu', [
                'categories' => $activeCategories,
                'stocks' => $stocks,
            ])
            @include('admin.catalog.partials.category', ['categories' => $categories])
        @endif
    </div>
@endsection
