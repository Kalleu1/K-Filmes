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
            {{-- 1. Em Alta --}}
            <x-discover.collection-header 
                title="Em Alta" 
                subtitle="Os títulos mais procurados e discutidos da semana"
                viewAllUrl="{{ route('discover.collection', 'em-alta') }}"
                icon="🔥"
            />
            <x-discover.collection-carousel :movies="$trendingMovies" />

            {{-- 2. Populares --}}
            <x-discover.collection-header 
                title="Populares" 
                subtitle="Os filmes mais assistidos pela comunidade"
                viewAllUrl="{{ route('discover.collection', 'populares') }}"
                icon="⭐"
            />
            <x-discover.collection-carousel :movies="$popularMovies" />

            {{-- 3. Mais Votados --}}
            <x-discover.collection-header 
                title="Mais Votados" 
                subtitle="Grandes sucessos de bilheteria e aclamados pela crítica"
                viewAllUrl="{{ route('discover.collection', 'mais-votados') }}"
                icon="🏆"
            />
            <x-discover.collection-carousel :movies="$topRatedMovies" />

            {{-- 4. Em Cartaz --}}
            <x-discover.collection-header 
                title="Em Cartaz" 
                subtitle="Filmes exibidos atualmente nos cinemas"
                viewAllUrl="{{ route('discover.collection', 'em-cartaz') }}"
                icon="🎬"
            />
            <x-discover.collection-carousel :movies="$nowPlaying" />

            {{-- 5. Próximos Lançamentos --}}
            <x-discover.collection-header 
                title="Próximos Lançamentos" 
                subtitle="Estreias aguardadas que chegarão em breve"
                viewAllUrl="{{ route('discover.collection', 'proximos-lancamentos') }}"
                icon="📅"
            />
            <x-discover.collection-carousel :movies="$upcoming" />
        </div>
    </div>
</div>
@endsection
