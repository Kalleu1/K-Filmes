@extends('layouts.base')
@include('components.header')

@section('content')
<div class="filme-dia-page"
     id="filme-dia-page"
     data-sortear-url="{{ route('filme-do-dia.sortear') }}"
     data-aleatorios-url="{{ route('filme-do-dia.aleatorios') }}">

    {{-- Poster inicial (placeholder) --}}
    <div class="poster-container" id="posterContainer">
        <img id="filmePoster" src="{{ asset('images/placeholder-poster.png') }}" 
             alt="Poster do Filme" class="poster-img">
        
        {{-- Container da roleta sobreposta --}}
        <div class="roulette-container" id="rouletteContainer">
            <div class="roulette-track" id="rouletteTrack"></div>
        </div>
    </div>

    {{-- Botão principal --}}
    <form id="filmeDoDiaForm" class="filme-dia-form">
        @csrf
        <button type="submit" class="btn-sortear">🎲 Sortear Filme</button>

        <div class="filters">
            <input type="number" name="ano" placeholder="Ano" min="1900" max="{{ date('Y') }}">
            <input type="text" name="diretor" placeholder="Diretor">
            <input type="text" name="genero" placeholder="Gênero">
        </div>

        <div class="fonte">
            <label><input type="radio" name="fonte" value="biblioteca" checked> Minha Biblioteca</label>
            {{-- <label><input type="radio" name="fonte" value="tmdb"> TMDB</label> --}}
        </div>
    </form>
</div>


@endsection
