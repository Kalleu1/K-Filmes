@extends('layouts.app')

@section('content')
<div class="form_main">
    <h2 class="create_title">Adicionar Novo Filme</h2>

    <form action="{{ route('filmes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Nome do Filme --}}
        <div class="form_group">
            <label for="nome">Nome do Filme</label>
            <input type="text" name="nome" id="nome" value="{{ old('nome', $tmdbData['title'] ?? '') }}" required>
        </div>

        {{-- Diretor --}}
        <div class="form_group">
            <label for="diretor">Diretor</label>
            <input type="text" name="diretor" id="diretor" value="{{ old('diretor', $tmdbData['director'] ?? '') }}">
        </div>

        {{-- Gênero --}}
        <div class="form_group">
            <label for="genero">Gênero</label>
            <select name="genero" id="genero" class="form-control" required>
                @php $selectedGenre = old('genero', $tmdbData['genres'][0]['name'] ?? ''); @endphp
                <option value="">Selecione...</option>
                @foreach(['Ação','Aventura','Animação','Biográfico','Comédia','Documentário','Drama','Fantasia','Ficção Científica','Mistério','Musical','Romance','Suspense','Terror','Thriller'] as $g)
                    <option value="{{ $g }}" {{ $selectedGenre == $g ? 'selected' : '' }}>{{ $g }}</option>
                @endforeach
            </select>
        </div>

        {{-- Plataforma --}}
        <div class="form_group">
            <label for="plataforma">Plataforma</label>
            <select name="plataforma" id="plataforma">
                @php $selectedPlatform = old('plataforma'); @endphp
                @foreach(['Netflix','Amazon Prime','Disney+','HBO Max','Apple TV+','Cinema','Stremio','Unitv','Torrent','Outros'] as $p)
                    <option value="{{ $p }}" {{ $selectedPlatform == $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>
        </div>

        {{-- Data assistida --}}
        <div class="form_group">
            <label for="data_assistida">Data Assistida</label>
            <input type="date" name="data_assistida" id="data_assistida" value="{{ old('data_assistida') }}">
        </div>

        {{-- Nota --}}
        <div class="form_group">
            <label for="nota">Nota (0-10)</label>
            <input type="number" step="0.1" min="0" max="10" name="nota" id="nota" value="{{ old('nota') }}">
        </div>

        {{-- Descrição --}}
        <div class="form_group form_full">
            <label for="descricao">Descrição</label>
            <textarea name="descricao" id="descricao">{{ old('descricao', $tmdbData['overview'] ?? '') }}</textarea>
        </div>

        {{-- Comentários --}}
        <div class="form_group form_full">
            <label for="comentarios">Comentários</label>
            <textarea name="comentarios" id="comentarios">{{ old('comentarios') }}</textarea>
        </div>

        {{-- Poster --}}
        <div class="form_group">
            <label for="poster">Poster (2:3)</label>
            @if(isset($tmdbData['poster_path']))
                <div class="poster_preview">
                    <img src="{{ $tmdbData['poster_path'] }}" alt="Poster do filme">
                </div>
            @endif
            <input type="file" name="poster" id="poster">
        </div>

        {{-- Banner --}}
        <div class="form_group">
            <label for="poster_banner">Banner (16:9)</label>
            @if(isset($tmdbData['backdrop_path']))
                <div class="banner_preview">
                    <img src="{{ $tmdbData['backdrop_path'] }}" alt="Banner do filme">
                </div>
            @endif
            <input type="file" name="poster_banner" id="poster_banner">
        </div>

        {{-- Botão --}}
        <div class="form_actions form_full">
            <button type="submit">Salvar Filme</button>
        </div>
    </form>
</div>
@endsection
