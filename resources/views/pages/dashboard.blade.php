@extends('layouts.base')
@include('components.header')

@section('content')
    <div class="dashboard">

        <div class="sidebar-overlay" data-sidebar-overlay></div>
            {{-- Barra lateral --}}
            <x-sidebar 
                :totalFilmes="$totalFilmes"
                :totalFavoritos="$totalFavoritos"
                :totalAssistidos="$totalAssistidos"
                :mediaNotas="$mediaNotas"
            />
             

        <div class ="dashboard_main">
        
            {{-- Banner Hero com Imagens Responsivas --}}       
            <section class="dashboard-hero">
                @foreach($destaques as $index => $filme)
                    <div class="hero-slide {{ $index === 0 ? 'active' : '' }}">
                        <a href="{{ 
            isset($filme->id) 
                ? route('filmes.show', $filme->id) 
                : (isset($filme->tmdb_id) ? route('filmes.showTmdb', $filme->tmdb_id) : '#') 
        }}">
                            {{-- Picture responsiva: poster mobile, backdrop desktop --}}
                            @if(isset($filme->responsiveBackdrop))
                                <picture>
                                    {{-- Desktop: backdrop landscape (w1280) --}}
                                    <source 
                                        media="(min-width: 768px)" 
                                        srcset="{{ $filme->responsiveBackdrop->desktop_url }}"
                                        alt="Banner de {{ $filme->nome }}">
                                    
                                    {{-- Mobile: poster vertical (w500) --}}
                                    <img 
                                        src="{{ $filme->responsiveBackdrop->mobile_url }}"
                                        alt="Banner de {{ $filme->nome }}"
                                        class="hero-poster">
                                </picture>
                            @else
                                {{-- Fallback para filmes sem imagem responsiva --}}
                                <img 
                                    src="{{ $filme->poster_banner_url ?? asset('imgs/no-poster.jpg') }}"
                                    alt="Banner de {{ $filme->nome }}" 
                                    class="hero-poster">
                            @endif
                        </a>

                        <div class="hero-info">
                            <h2 class="hero-title">{{ $filme->nome }}</h2>
                            <p class="hero-rating">⭐  {{ $filme->nota }}/10</p>
                            <p class="hero-meta">{{ $filme->genero }} | {{ $filme->diretor }}</p>
                        </div>
                    </div>
                @endforeach
            </section>

            {{-- Filmes Adicionado Recentemente --}}

            {{-- Filmes Recentes --}}
            <section class="dashboard_section">
                
                <div class="section-header">
                    <h3 class="section-title">👁️ Filmes Vistos Recentemente</h3>
                </div>
                
                <div class="dashboard_grid">
                    @foreach ($assistidosRecentemente as $filme)
                        <x-filmecard :filme="$filme" :campos="['poster']" />
                    @endforeach
                </div>
            </section>


            {{-- Filmes Maiores Notas --}}
            <section class="dashboard_section">
                
                <div class="section-header">
                    <h3 class="section-title">🏆 Maiores Notas do Ano</h3>
                </div>
                
                <div class="dashboard_grid">
                    @foreach ($melhoresAno as $filme)
                        <x-filmecard :filme="$filme" :campos="['poster']" />
                    @endforeach
                </div>
            </section>

    
            

            <section class="dashboard_section">
                
                <div class="section-header">
                    <h3 class="section-title">Em Alta na Semana</h3>
                </div>
                
                <div class="dashboard_grid">
                    @foreach ($topRated as $filme)
                        <x-filmecard :filme="$filme" :campos="['poster']" />
                    @endforeach
                </div>
            </section>

            <section class="dashboard_section">
                
                <div class="section-header">
                    <h3 class="section-title">Próximos Lançamentos</h3>
                </div>

                    <div class="dashboard_grid">
                        @foreach ($upcoming as $filme)
                            <x-filmecard :filme="$filme" :campos="['poster']" />
                        @endforeach
                    </div>
            </section>
        </div>

    </div>

@endsection

{{-- Script JS do dashboard removido — agora está em resources/js/pages/dashboard.js --}}