@extends('admin.layouts.app')

@section('title', 'Manajemen Meja')
@section('page_title', 'Meja')

@section('content')
    <div class="space-y-4 lg:space-y-6">
        {{-- Breadcrumb (Desktop only) --}}
        <nav class="hidden lg:flex items-center gap-1.5 text-xs text-cafe-muted mb-3">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-cafe-dark flex items-center gap-1">
                <i data-lucide="home" class="w-3.5 h-3.5"></i>
                <span>Admin</span>
            </a>
            <i data-lucide="chevron-right" class="w-3 h-3 text-cafe-muted/60"></i>
            <span class="font-semibold text-cafe-dark">Meja</span>
        </nav>

        @include('admin.operations.partials.tables')
    </div>
@endsection
