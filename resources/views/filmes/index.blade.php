@extends('layouts.app')

@section('content')
    <h2>Lista de Filmes</h2>

    <div class ="filmes-grid">
        @foreach($filmes as $filme)
          <div class="filme-card">
            @if($filme->poster)
                <img src="{{ asset('storage/posters/' . $filme->poster) }}" alt="{{ $filme->nome }}" class="filme-poster">
            @else
                <div class="filme-sem-poster">Sem poster</div>
            @endif

            <h3 class="filme-titulo"> {{ $filme->nome }}</h3>
            <p class="filme-titulo"> {{ $filme->genero }}</p>
            <p class="filme-titulo"> {{ $filme->nota }}</p>

            <a href="{{ route('filmes.show', $filme->id) }}" class="filme-link">Ver detalhes</a>
          </div>  
        @endforeach
    </div>
@endsection
