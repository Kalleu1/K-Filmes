@extends('layouts.base')
@include('components.header')

@section('content')
<main class="library-page">
    <div class="library-container">
        <div class="library-header">
            <h1 class="library-title">Criar coleção</h1>
        </div>
        <form method="POST" action="{{ route('colecoes.store') }}" class="library-search-bar">
            @csrf
            <label for="nome">Nome</label>
            <input id="nome" name="nome" type="text" value="{{ old('nome') }}" required maxlength="255">
            @error('nome') <p role="alert">{{ $message }}</p> @enderror

            <label for="descricao">Descrição (opcional)</label>
            <textarea id="descricao" name="descricao" maxlength="5000">{{ old('descricao') }}</textarea>
            @error('descricao') <p role="alert">{{ $message }}</p> @enderror

            <button type="submit" class="btn-primary">Salvar coleção</button>
            <a href="{{ route('colecoes.index') }}" class="btn-secondary">Cancelar</a>
        </form>
    </div>
</main>
@endsection
