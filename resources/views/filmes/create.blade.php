@extends('layouts.app')

@section('content')
    <h2>Adicionar Filme</h2>

    <form action="{{ route('filmes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label>Nome:</label>
        <input type="text" name="nome" required><br>

        <label>Descrição:</label>
        <textarea name="descricao"></textarea><br>

        <label>Plataforma:</label>
        <input type="text" name="plataforma"><br>

        <label>Data assistida:</label>
        <input type="date" name="data_assistida"><br>

        <label>Diretor:</label>
        <input type="text" name="diretor"><br>

        <label>Gênero:</label>
        <input type="text" name="genero"><br>

        <label>Nota (1-10):</label>
        <input type="number" step="0.1" min="1" max="10" name="nota"><br>

        <label>Comentários:</label>
        <textarea name="comentarios"></textarea><br>

        <label>Poster:</label>
        <input type="file" name="poster"><br><br>

        <button type="submit">Salvar Filme</button>
    </form>
@endsection
