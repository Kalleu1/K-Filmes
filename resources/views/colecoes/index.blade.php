@extends('layouts.base')
@include('components.header')

@section('content')
<main class="collections-page collections-index-page">
    <div class="collections-container">
        <header class="collections-list-header">
            <div>
                <p class="collections-eyebrow">Sua curadoria</p>
                <h1>Minhas coleções</h1>
                <p>Reúna filmes para cada momento, tema ou história.</p>
            </div>
            <a href="{{ route('colecoes.create') }}" class="collections-action">Criar coleção</a>
        </header>

        @if(session('success'))
            <div class="collections-feedback">{{ session('success') }}</div>
        @endif

        @if($colecoes->isEmpty())
            <div class="collections-empty">
                <span class="collections-empty__icon">✦</span>
                <h2>Você ainda não tem coleções</h2>
                <p>Crie uma coleção para organizar os filmes da sua biblioteca.</p>
                <a href="{{ route('colecoes.create') }}" class="collections-action">Criar minha primeira coleção</a>
            </div>
        @else
            <div class="collections-gallery">
                @foreach($colecoes as $colecao)
                    @php
                        $capa = $colecao->filmes->first(function ($filme) {
                            return $filme->poster_banner || $filme->poster;
                        });
                    @endphp
                    <a href="{{ route('colecoes.show', $colecao) }}" class="collection-tile">
                        <div class="collection-tile__cover">
                            @if($capa)
                                <img src="{{ $capa->poster_banner_url ?: $capa->poster_url }}" alt="">
                            @else
                                <span class="collection-tile__placeholder">✦</span>
                            @endif
                            <span class="collection-tile__count">{{ $colecao->filmes_count }} {{ $colecao->filmes_count === 1 ? 'filme' : 'filmes' }}</span>
                        </div>
                        <div class="collection-tile__details">
                            <h2>{{ $colecao->nome }}</h2>
                            <p>{{ $colecao->descricao ?: 'Coleção pessoal' }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</main>
@endsection
