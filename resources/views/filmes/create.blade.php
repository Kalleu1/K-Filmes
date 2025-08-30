@extends('layouts.app')

@section('content')
<div class="form_main" >
    <h2>Adicionar Novo Filme</h2>

    <form action="{{ route('filmes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label for="nome">Nome do Filme</label>
        <input type="text" name="nome" id="nome" value="{{ old('nome') }}" required>

        <label for="descricao">Descrição</label>
        <textarea name="descricao" id="descricao">{{ old('descricao') }}</textarea>

        <label for="plataforma">Plataforma</label>
        <input type="text" name="plataforma" id="plataforma" value="{{ old('plataforma') }}">

        <label for="data_assistida">Data Assistida</label>
        <input type="date" name="data_assistida" id="data_assistida" value="{{ old('data_assistida') }}">

        <label for="diretor">Diretor</label>
        <input type="text" name="diretor" id="diretor" value="{{ old('diretor') }}">

        <label for="genero">Gênero</label>
        <input type="text" name="genero" id="genero" value="{{ old('genero') }}">

        <label for="nota">Nota</label>
        <input type="number" step="0.1" min="0" max="10" name="nota" id="nota" value="{{ old('nota') }}">

        <label for="comentarios">Comentários</label>
        <textarea name="comentarios" id="comentarios">{{ old('comentarios') }}</textarea>

        <label for="poster">Poster (2:3)</label>
        <input type="file" name="poster" id="poster">

        <label for="banner_horizontal">Banner (16:9)</label>
        <input type="file" name="banner_horizontal" id="banner_horizontal">

        <button type="submit">Salvar Filme</button>
    </form>
</div>
@endsection
