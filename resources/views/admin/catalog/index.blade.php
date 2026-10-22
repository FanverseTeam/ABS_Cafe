@extends('admin.layouts.app')

@section('title', 'Manajemen Menu')
@section('page_title', 'Manajemen Menu')

@section('content')
    @php($tab = request('tab', 'menu'))

    <div class="space-y-0 lg:space-y-6">

        {{-- Breadcrumb + H1: only on desktop (lg+) --}}
        <div class="hidden lg:block space-y-4">
            <nav class="flex items-center gap-1.5 text-xs text-cafe-muted">
                <a href="{{ route('admin.staff.index') }}" class="hover:text-cafe-dark flex items-center gap-1">
                    <i data-lucide="home" class="w-3.5 h-3.5"></i>
                    <span>Admin</span>
                </a>
                <i data-lucide="chevron-right" class="w-3 h-3 text-cafe-muted/60"></i>
                <span class="font-semibold text-cafe-dark">Manajemen Menu</span>
            </nav>

            <div>
                <h2 class="font-serif text-2xl font-bold text-cafe-dark">Manajemen Menu</h2>
                <p class="text-xs text-cafe-muted mt-0.5">Kelola katalog menu, kategori, stok, dan data supplier dalam satu tempat.</p>
            </div>
        </div>

        {{-- Tab switcher --}}
        {{-- Mobile/Tablet: sticky below app bar, full-width segmented control --}}
        <div class="lg:hidden sticky top-[calc(4rem+env(safe-area-inset-top,0px))] z-20 bg-[#F2F5E2] pt-2 pb-1 -mx-3.5 px-3.5 sm:-mx-5 sm:px-5">
            <div class="grid grid-cols-2 gap-1 bg-[#E3DEA4]/50 p-1 rounded-2xl">
                <a href="{{ route('admin.catalog.index', ['tab' => 'menu']) }}"
                   class="flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ $tab !== 'stock' ? 'bg-[#290024] text-[#FAFCF3] shadow-sm' : 'text-[#775533] hover:bg-[#E3DEA4]/60' }}">
                    <i data-lucide="utensils" class="w-3.5 h-3.5"></i>
                    <span>Menu & Kategori</span>
                </a>
                <a href="{{ route('admin.catalog.index', ['tab' => 'stock']) }}"
                   class="flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ $tab === 'stock' ? 'bg-[#290024] text-[#FAFCF3] shadow-sm' : 'text-[#775533] hover:bg-[#E3DEA4]/60' }}">
                    <i data-lucide="package" class="w-3.5 h-3.5"></i>
                    <span>Stok & Supplier</span>
                </a>
            </div>
        </div>

        {{-- Desktop: pill-style tab row --}}
        <div class="hidden lg:flex items-center gap-1.5 overflow-x-auto no-scrollbar py-1">
            <a href="{{ route('admin.catalog.index', ['tab' => 'menu']) }}"
               class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all {{ $tab !== 'stock' ? 'bg-[#290024] text-[#FAFCF3] shadow-sm' : 'bg-[#FAFCF3] text-[#775533] border border-[#E3DEA4] hover:bg-[#E3DEA4]' }}">
                Menu & Kategori
            </a>
            <a href="{{ route('admin.catalog.index', ['tab' => 'stock']) }}"
               class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all {{ $tab === 'stock' ? 'bg-[#290024] text-[#FAFCF3] shadow-sm' : 'bg-[#FAFCF3] text-[#775533] border border-[#E3DEA4] hover:bg-[#E3DEA4]' }}">
                Stok & Supplier
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
            <div class="lg:block" id="kategori">
                @include('admin.catalog.partials.category', ['categories' => $categories])
            </div>
        @endif
    </div>
@endsection
