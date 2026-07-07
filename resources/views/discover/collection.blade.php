@extends('layouts.base')
@include('components.header')

@push('styles')
    @vite('resources/css/pages/discover.css')
@endpush

@push('scripts')
    @vite('resources/js/discover.js')
@endpush

@section('content')
<div class="discover-page discover-collection-page">
    <div class="discover-container">
        <div class="discover-header">
            <x-back-button context="icon" href="{{ route('discover.index') }}" />
            <h1 class="discover-title">🎬 {{ $title }}</h1>
            <p class="discover-subtitle">Explore a seleção completa de filmes desta categoria</p>
        </div>

        <div class="discover-grid-wrapper">
            <div class="discover-movies-grid">
                @forelse($movies as $movie)
                    <div class="discover-movie-item">
                        <x-filmecard :filme="$movie" :campos="['poster']" />
                    </div>
                @empty
                    <div class="discover-empty">
                        <p>Nenhum filme disponível nesta coleção no momento.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
