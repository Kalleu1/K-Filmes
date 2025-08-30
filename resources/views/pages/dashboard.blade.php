@extends('layouts.app')

@section('content')
    <div class="dashboard">

        {{-- Barra lateral --}}
        <aside class="dashboard_sidebar">
            <h2>Ações</h2>
            <ul>
                <li><a href="">➕ Adicionar Filme</a></li>
                <li><a href="">❤️ Ver Favoritos</a></li>
                <li><a href="">✨ Explorar Recomendações</a></li>
            </ul>
        </aside>

    

        <div class ="dashboard_main">
            {{-- <h1> Olá, Kalleu!(nome o usuario) 🎬</h1>
            <h2> “Aqui estão alguns filmes que você avaliou:”</h2> --}}

                {{-- Banner --}}
            <section class="dashboard-hero">
                @foreach($destaques as $index => $filme)
                    <div class="hero-slide {{ $index === 0 ? 'active' : '' }}">
                        <img src="{{ asset('storage/posters/' . $filme->poster) }}" 
                            alt="{{ $filme->nome }}" 
                            class="hero-poster">
                        <div class="hero-info">
                            <h2>{{ $filme->nome }}</h2>
                            <p>⭐ Nota: {{ $filme->nota }}/10</p>
                            <p>{{ $filme->genero }} | {{ $filme->diretor }}</p>
                        </div>
                    </div>
                @endforeach
            </section>

            <section class="dashboard_section">
                <h3 class="dashboard_section_title">Filmes Recentes</h3>
                
                <div class="dashboard_grid">
                    @foreach ($recentes as $filme)
                        <x-filmecard :filme="$filme" :campos="['nome','poster']" />
                    @endforeach
                </div>

                <h3 class="dashboard_section_title">Filmes Com Maiores Notas</h3>
                
                <div class="dashboard_grid">
                    @foreach ($topNotas as $filme)
                        <x-filmecard :filme="$filme" :campos="['nome','poster','nota']"/>
                    @endforeach
                </div>
            </section>
        </div>

    </div>



@endsection