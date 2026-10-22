<?php

namespace App\Http\Controllers;

use App\Models\Colecao;
use App\Models\Filme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ColecaoController extends Controller
{
    public function index(): View
    {
        $colecoes = Colecao::where('user_id', Auth::id())
            ->with('filmes')
            ->withCount('filmes')
            ->latest()
            ->get();

        return view('colecoes.index', compact('colecoes'));
    }

    public function create(): View
    {
        return view('colecoes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string', 'max:5000'],
        ]);

        $colecao = Colecao::create([
            ...$data,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('colecoes.show', $colecao)
            ->with('success', 'Coleção criada com sucesso.');
    }

    public function show(int $id): View
    {
        $colecao = $this->ownedCollection($id);
        $colecao->load('filmes');
        $heroFilme = $colecao->filmes->first(function (Filme $filme) {
            return $filme->poster_banner || $filme->poster;
        });
        $viewMode = in_array(request('view'), ['list', 'grid'], true)
            ? request('view')
            : 'list';

        $filmesDisponiveis = Filme::where('user_id', Auth::id())
            ->whereNotIn('id', $colecao->filmes->modelKeys())
            ->orderBy('nome')
            ->get();

        return view('colecoes.show', compact('colecao', 'filmesDisponiveis', 'heroFilme', 'viewMode'));
    }

    public function edit(int $id): View
    {
        $colecao = $this->ownedCollection($id);

        return view('colecoes.edit', compact('colecao'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $colecao = $this->ownedCollection($id);
        $colecao->update($request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string', 'max:5000'],
        ]));

        return redirect()->route('colecoes.show', $colecao)
            ->with('success', 'Coleção atualizada com sucesso.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->ownedCollection($id)->delete();

        return redirect()->route('colecoes.index')
            ->with('success', 'Coleção excluída com sucesso.');
    }

    public function addMovie(Request $request, int $id): RedirectResponse
    {
        $colecao = $this->ownedCollection($id);
        $filme = Filme::where('user_id', Auth::id())->findOrFail(
            $request->validate(['filme_id' => ['required', 'integer']])['filme_id']
        );

        if ($colecao->filmes()->whereKey($filme->id)->exists()) {
            return back()->with('error', 'Este filme já está nesta coleção.');
        }

        $nextPosition = (int) ($colecao->filmes()->max('position') ?? -1) + 1;
        $colecao->filmes()->attach($filme->id, ['position' => $nextPosition]);

        return back()->with('success', 'Filme adicionado à coleção.');
    }

    public function removeMovie(int $id, int $filmeId): RedirectResponse
    {
        $colecao = $this->ownedCollection($id);
        $colecao->filmes()->detach(
            Filme::where('user_id', Auth::id())->findOrFail($filmeId)->id
        );

        return back()->with('success', 'Filme removido da coleção.');
    }

    private function ownedCollection(int $id): Colecao
    {
        return Colecao::where('user_id', Auth::id())->findOrFail($id);
    }
}
