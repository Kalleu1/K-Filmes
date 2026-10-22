@extends('layouts.base')
@include('components.header')

@section('content')
<main class="library-page">
    <div class="library-container">
        <div class="library-header">
            <h1 class="library-title">Editar coleção</h1>
        </div>
        <form method="POST" action="{{ route('colecoes.update', $colecao) }}" class="library-search-bar">
            @csrf
            @method('PUT')
            <label for="nome">Nome</label>
            <input id="nome" name="nome" type="text" value="{{ old('nome', $colecao->nome) }}" required maxlength="255">
            @error('nome') <p role="alert">{{ $message }}</p> @enderror

            <label for="descricao">Descrição (opcional)</label>
            <textarea id="descricao" name="descricao" maxlength="5000">{{ old('descricao', $colecao->descricao) }}</textarea>
            @error('descricao') <p role="alert">{{ $message }}</p> @enderror

            <button type="submit" class="btn-primary">Salvar alterações</button>
            <a href="{{ route('colecoes.show', $colecao) }}" class="btn-secondary">Cancelar</a>
        </form>
    </div>
</main>
@endsection
