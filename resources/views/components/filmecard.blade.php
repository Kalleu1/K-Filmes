<div class="filme-card"> {{-- Poster --}}

    @if(in_array('poster', $campos))
        <a href="{{ 
            isset($filme->id) 
                ? route('filmes.show', $filme->id) 
                : (isset($filme->tmdb_id) ? route('filmes.showTmdb', $filme->tmdb_id) : '#') 
        }}">
            <img 
                src="{{ $filme->poster_url ?? asset('img/poster-placeholder.png') }}" 
                alt="{{ $filme->nome ?? 'Sem título' }}" 
                class="filme-poster"
            >
        </a>

    @endif


    {{-- Nome --}}
    @if(in_array('nome', $campos))
        <h3 class="filme-titulo">{{ $filme->nome ?? 'Sem título' }}</h3>
    @endif

    {{-- Ano --}}
    @if(in_array('ano', $campos))
        <p class="filme-titulo">{{ $filme->ano ?? '-' }}</p>
    @endif

    {{-- Nota --}}
    @if(in_array('nota', $campos) && isset($filme->nota))
        <p>⭐ Nota: {{ $filme->nota }}/10</p>
    @endif

    {{-- Diretor --}}
    @if(in_array('diretor', $campos) && isset($filme->diretor))
        <p>🎬 Diretor: {{ $filme->diretor }}</p>
    @endif

    {{-- Gênero --}}
    @if(in_array('genero', $campos) && isset($filme->genero))
        <p>📌 Gênero: {{ $filme->genero }}</p>
    @endif

    {{-- Plataforma --}}
    @if(in_array('plataforma', $campos) && isset($filme->plataforma))
        <p>📺 Assistido em: {{ $filme->plataforma }}</p>
    @endif

    {{-- Comentários --}}
    @if(in_array('comentarios', $campos) && isset($filme->comentarios))
        <p class="comentarios">💬 "{{ $filme->comentarios }}"</p>
    @endif

    {{-- Data assistida --}}
    @if(in_array('data_assistida', $campos) && isset($filme->data_assistida))
        <small>📅 Assistido em {{ \Carbon\Carbon::parse($filme->data_assistida)->format('d/m/Y') }}</small>
    @endif
</div>