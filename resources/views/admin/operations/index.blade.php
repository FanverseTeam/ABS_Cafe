@extends('admin.layouts.app')

@section('title', 'Manajemen Meja')
@section('page_title', 'Meja')

@section('content')
    <div class="space-y-4 lg:space-y-6">
        {{-- Breadcrumb (Desktop only) --}}
        <x-breadcrumb
            :items="[
                ['label' => 'Admin', 'url' => route('admin.dashboard'), 'icon' => 'home'],
                ['label' => 'Meja'],
            ]"
            class="mb-3"
        />

        @include('admin.operations.partials.tables')
    </div>
@endsection
