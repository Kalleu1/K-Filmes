@extends('layouts.base')

@section('content')
    <h1>Editar Filme</h1>

    <form action="{{ route('filmes.update', $filme->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label for="titulo">Título:</label>
        <input type="text" name="titulo" value="{{ $filme->titulo }}" required>

        <label for="descricao">Descrição:</label>
        <textarea name="descricao">{{ $filme->descricao }}</textarea>

        <label for="poster">Poster:</label>
        <input type="file" name="poster">

        <button type="submit">Salvar Alterações</button>
    </form>
@endsection
