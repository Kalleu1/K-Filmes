@extends('layouts.base')
@include('components.header')

@section('content')
<main class="collections-page collections-show-page">
    <section class="collection-hero {{ $heroFilme ? 'collection-hero--image' : '' }}"
        @if($heroFilme)
            style="--collection-backdrop: url('{{ $heroFilme->poster_banner_url ?: $heroFilme->poster_url }}')"
        @endif>
        <div class="collections-container collection-hero__content">
            <x-back-button context="hero" href="{{ route('colecoes.index') }}" />
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
                <div class="collection-section-heading__tools">
                    <span>{{ $colecao->filmes->count() }}</span>
                    <div class="collection-view-switcher" aria-label="Modo de visualização">
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}" class="{{ $viewMode === 'grid' ? 'is-active' : '' }}" title="Visualização em grade">
                            <i class="fa-solid fa-grip" aria-hidden="true"></i>
                            <span class="sr-only">Grade</span>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}" class="{{ $viewMode === 'list' ? 'is-active' : '' }}" title="Visualização em lista">
                            <i class="fa-solid fa-list" aria-hidden="true"></i>
                            <span class="sr-only">Lista</span>
                        </a>
                    </div>
                </div>
            </div>

            @if($colecao->filmes->isEmpty())
                <div class="collections-empty collections-empty--compact">
                    <h2>Esta coleção está vazia</h2>
                    <p>Adicione filmes da sua biblioteca usando o painel ao lado.</p>
                </div>
            @else
                <div class="collection-film-list {{ $viewMode === 'grid' ? 'collection-film-list--grid' : '' }}">
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
                                <button type="button" class="collection-film__remove" data-action="open-remove-film-modal" data-filme-name="{{ $filme->nome }}" aria-label="Remover {{ $filme->nome }}">Remover</button>
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
                    <a href="{{ route('colecoes.edit', $colecao) }}" class="collections-action collections-action--secondary collection-edit-action">Editar coleção</a>
                    <form method="POST" action="{{ route('colecoes.destroy', $colecao) }}" onsubmit="return confirm('Excluir esta coleção?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="collection-text-button">Excluir coleção</button>
                    </form>
                </div>
            </div>

            <div class="collection-panel collection-panel--add">
                <p class="collections-eyebrow">Adicionar título</p>
                <label for="collection-film-search">Busque na sua biblioteca</label>
                <div class="collection-add-search">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <input id="collection-film-search" type="search" placeholder="Digite o nome do filme..." autocomplete="off" {{ $filmesDisponiveis->isEmpty() ? 'disabled' : '' }}>
                </div>
                <p class="collection-panel__search-status" data-collection-search-status>
                    {{ $filmesDisponiveis->count() }} {{ $filmesDisponiveis->count() === 1 ? 'filme disponível' : 'filmes disponíveis' }}
                </p>
                <div class="collection-available-films" data-collection-available-films>
                    @foreach($filmesDisponiveis as $filme)
                        <form method="POST" action="{{ route('colecoes.filmes.store', $colecao) }}" class="collection-available-film" data-collection-film data-film-name="{{ \Illuminate\Support\Str::lower($filme->nome) }}">
                            @csrf
                            <input type="hidden" name="filme_id" value="{{ $filme->id }}">
                            <span>{{ $filme->nome }}</span>
                            <button type="submit" class="collection-add-film" aria-label="Adicionar {{ $filme->nome }}">
                                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                                <span>Adicionar</span>
                            </button>
                        </form>
                    @endforeach
                </div>
                @if($filmesDisponiveis->isEmpty())
                    <p class="collection-panel__hint">Todos os filmes da biblioteca já estão nesta coleção.</p>
                @endif
                <p class="collection-panel__hint collection-search-empty" data-collection-search-empty hidden>Nenhum filme encontrado com esse nome.</p>
            </div>
        </aside>
    </div>
</main>

<div id="removeCollectionFilmModal" class="modal-overlay" aria-hidden="true">
    <div class="modal-content delete-modal" role="dialog" aria-modal="true" aria-labelledby="removeCollectionFilmTitle">
        <div class="modal-header">
            <h2 id="removeCollectionFilmTitle" class="modal-title">Remover da coleção</h2>
            <button type="button" class="modal-close" data-action="close-modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
            <div class="delete-warning">
                <h3>Tem certeza?</h3>
                <p>O filme <strong data-remove-film-name></strong> será removido desta coleção. Ele continuará na sua biblioteca.</p>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" data-action="close-modal">Cancelar</button>
                <button type="button" class="btn-delete-confirm" data-action="confirm-remove-film">Sim, remover</button>
            </div>
        </div>
    </div>
</div>
@endsection
