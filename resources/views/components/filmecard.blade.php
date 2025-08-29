<div class="filme-card">
    {{-- Poster --}}
    @if(in_array('poster', $campos))
       <img src="{{ asset('storage/posters/' . $filme->poster) }}" alt="{{ $filme->nome }}" class="filme-poster">
    @endif

    {{-- Nome --}}
    @if(in_array('nome', $campos))
        <h3>{{ $filme->nome }}</h3>
    @endif

    {{-- Nota --}}
    @if(in_array('nota', $campos))
        <p>⭐ Nota: {{ $filme->nota }}/10</p>
    @endif

    {{-- Diretor --}}
    @if(in_array('diretor', $campos))
        <p>🎬 Diretor: {{ $filme->diretor }}</p>
    @endif

    {{-- Gênero --}}
    @if(in_array('genero', $campos))
        <p>📌 Gênero: {{ $filme->genero }}</p>
    @endif

    {{-- Plataforma --}}
    @if(in_array('plataforma', $campos))
        <p>📺 Assistido em: {{ $filme->plataforma }}</p>
    @endif

    {{-- Comentários --}}
    @if(in_array('comentarios', $campos))
        <p class="comentarios">💬 "{{ $filme->comentarios }}"</p>
    @endif

    {{-- Data assistida --}}
    @if(in_array('data_assistida', $campos))
        <small>📅 Assistido em {{ \Carbon\Carbon::parse($filme->data_assistida)->format('d/m/Y') }}</small>
    @endif
</div>
