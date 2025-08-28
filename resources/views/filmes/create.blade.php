@extends('layouts.app')

@section('content')
    <h2>Adicionar Filme</h2>

    <form action="{{ route('filmes.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="text" name="nome" placeholder="Nome do filme">
    <input type="file" name="poster">
    <button type="submit">Salvar</button>
</form>

@endsection
