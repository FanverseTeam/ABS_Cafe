@extends('admin.layouts.app')

@section('title', 'Manajemen Menu')
@section('page_title', 'Manajemen Menu')

@section('content')
    <div class="space-y-0 lg:space-y-6">

        {{-- Breadcrumb + H1: only on desktop (lg+) --}}
        <div class="hidden lg:block space-y-4">
            <nav class="flex items-center gap-1.5 text-xs text-cafe-muted">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-cafe-dark flex items-center gap-1">
                    <i data-lucide="home" class="w-3.5 h-3.5"></i>
                    <span>Admin</span>
                </a>
                <i data-lucide="chevron-right" class="w-3 h-3 text-cafe-muted/60"></i>
                <span class="font-semibold text-cafe-dark">Manajemen Menu</span>
            </nav>

            <div>
                <h2 class="font-serif text-2xl font-bold text-cafe-dark">Manajemen Menu</h2>
                <p class="text-xs text-cafe-muted mt-0.5">Kelola katalog menu dan kategori produk kafe.</p>
            </div>
        </div>

        @include('admin.catalog.partials.menu', [
            'categories' => $activeCategories,
            'stocks' => $stocks,
        ])
        <div class="lg:block" id="kategori">
            @include('admin.catalog.partials.category', ['categories' => $categories])
        </div>
    </div>
@endsection
