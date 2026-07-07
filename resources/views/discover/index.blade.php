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

        <div class="discover-content-wrapper">
            {{-- Coleção 1: Ficção Científica --}}
            <x-discover.collection-header 
                title="Ficção Científica" 
                subtitle="Viagens espaciais, realidades alternativas e tecnologia futurista"
                viewAllUrl="{{ route('discover.collection', 'ficcao-cientifica') }}"
                icon="🚀"
            />
            <x-discover.collection-carousel :movies="$scifiMovies" />

            {{-- Coleção 2: Clássicos do Cinema --}}
            <x-discover.collection-header 
                title="Clássicos do Cinema" 
                subtitle="Obras-primas imperdíveis que moldaram a história do cinema"
                viewAllUrl="{{ route('discover.collection', 'classicos') }}"
                icon="🎬"
            />
            <x-discover.collection-carousel :movies="$classicsMovies" />
        </div>
    </div>
</div>
@endsection
