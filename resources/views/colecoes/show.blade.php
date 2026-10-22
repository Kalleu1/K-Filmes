@extends('layouts.base')
@include('components.header')

@section('content')
<main class="collections-page collections-show-page">
    <section class="collection-hero {{ $heroFilme ? 'collection-hero--image' : '' }}"
        @if($heroFilme)
            style="--collection-backdrop: url('{{ $heroFilme->poster_banner_url ?: $heroFilme->poster_url }}')"
        @endif>
        <div class="collections-container collection-hero__content">
            <a href="{{ route('colecoes.index') }}" class="collection-back-link">← Minhas coleções</a>
            <div class="collection-hero__copy">
                <p class="collections-eyebrow">Coleção pessoal</p>
                <h1>{{ $colecao->nome }}</h1>
                <p>{{ $colecao->descricao ?: 'Uma seleção especial da sua biblioteca.' }}</p>
                <span>{{ $colecao->filmes->count() }} {{ $colecao->filmes->count() === 1 ? 'filme' : 'filmes' }}</span>
            </div>
        </div>
    </section>

    <div class="collections-container collection-layout">
        <section class="collection-films">
            <div class="collection-section-heading">
                <div>
                    <p class="collections-eyebrow">Em ordem de adição</p>
                    <h2>Filmes da coleção</h2>
                </div>
                <span>{{ $colecao->filmes->count() }}</span>
            </div>

            @if($colecao->filmes->isEmpty())
                <div class="collections-empty collections-empty--compact">
                    <h2>Esta coleção está vazia</h2>
                    <p>Adicione filmes da sua biblioteca usando o painel ao lado.</p>
                </div>
            @else
                <div class="collection-film-list">
                    @foreach($colecao->filmes as $filme)
                        <article class="collection-film">
                            <a href="{{ route('filmes.show', $filme) }}" class="collection-film__poster">
                                <img src="{{ $filme->poster_url ?: asset('img/poster-placeholder.png') }}" alt="Poster de {{ $filme->nome }}">
                            </a>
                            <div class="collection-film__info">
                                <span class="collection-film__number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <h3><a href="{{ route('filmes.show', $filme) }}">{{ $filme->nome }}</a></h3>
                                <p>{{ $filme->ano_lancamento ?: 'Filme da biblioteca' }}</p>
                            </div>
                            <form method="POST" action="{{ route('colecoes.filmes.destroy', [$colecao, $filme]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="collection-film__remove" aria-label="Remover {{ $filme->nome }}">Remover</button>
                            </form>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <aside class="collection-sidebar">
            <div class="collection-panel">
                <div class="collection-panel__heading">
                    <p class="collections-eyebrow">Sobre a coleção</p>
                    <h2>{{ $colecao->nome }}</h2>
                </div>
                <p class="collection-panel__description">{{ $colecao->descricao ?: 'Sem descrição adicionada.' }}</p>
                <dl class="collection-facts">
                    <div><dt>Filmes</dt><dd>{{ $colecao->filmes->count() }}</dd></div>
                    <div><dt>Criada em</dt><dd>{{ $colecao->created_at->format('d/m/Y') }}</dd></div>
                </dl>
                <div class="collection-panel__actions">
                    <a href="{{ route('colecoes.edit', $colecao) }}" class="collections-action collections-action--secondary">Editar coleção</a>
                    <form method="POST" action="{{ route('colecoes.destroy', $colecao) }}" onsubmit="return confirm('Excluir esta coleção?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="collection-text-button">Excluir coleção</button>
                    </form>
                </div>
            </div>

            <div class="collection-panel collection-panel--add">
                <p class="collections-eyebrow">Adicionar título</p>
                <form method="POST" action="{{ route('colecoes.filmes.store', $colecao) }}">
                    @csrf
                    <label for="filme_id">Escolha um filme da biblioteca</label>
                    <select id="filme_id" name="filme_id" required>
                        <option value="">Selecione um filme</option>
                        @foreach($filmesDisponiveis as $filme)
                            <option value="{{ $filme->id }}">{{ $filme->nome }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="collections-action" {{ $filmesDisponiveis->isEmpty() ? 'disabled' : '' }}>Adicionar filme</button>
                </form>
                @if($filmesDisponiveis->isEmpty())
                    <p class="collection-panel__hint">Todos os filmes da biblioteca já estão nesta coleção.</p>
                @endif
            </div>
        </aside>
    </div>
</main>
@endsection
