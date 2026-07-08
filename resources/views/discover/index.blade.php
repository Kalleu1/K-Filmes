@extends('layouts.base')
@include('components.header')

@push('styles')
    @vite('resources/css/pages/discover.css')
@endpush

@push('scripts')
    @vite('resources/js/discover.js')
@endpush

@section('content')
<div class="discover-page">
    <div class="discover-container">
        <div class="discover-header">
            <x-back-button context="icon" href="{{ route('dashboard') }}" />
            <h1 class="discover-title">✨ Descobrir</h1>
            <p class="discover-subtitle">Explore novas coleções e recomendações cinematográficas</p>
        </div>

        {{-- Seção de Gêneros --}}
        @if(!empty($genres))
            <section class="discover-genres">
                <h2 class="genres-title">📂 Navegar por Gênero</h2>
                <div class="genres-list">
                    @foreach($genres as $genre)
                        <a href="{{ route('discover.collection', $genre['id']) }}" class="genre-pill">
                            {{ $genre['name'] }}
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <div class="discover-content-wrapper">
            @foreach($collections as $collection)
                <div class="discover-section">
                    <x-discover.collection-header 
                        title="{{ $collection['title'] }}" 
                        subtitle="{{ $collection['subtitle'] ?? '' }}"
                        viewAllUrl="{{ route('discover.collection', $collection['slug']) }}"
                        icon="{{ $collection['icon'] ?? '🎬' }}"
                    />
                    <x-discover.collection-carousel :movies="$collection['movies']" />
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
