<?php

namespace App\Services;

use App\Models\Filme;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class FilmeBibliotecaService
{
    public function paginateBiblioteca(Request $request, int $userId): LengthAwarePaginator
    {
        $query = Filme::where('user_id', $userId);

        if ($request->has('assistido') && $request->assistido !== '') {
            $query->where('assistido', $request->boolean('assistido'));
        }

        if ($request->has('favorito') && $request->favorito !== '') {
            $query->where('favorito', $request->boolean('favorito'));
        }

        if ($request->filled('genero')) {
            $query->where('genero', 'LIKE', '%' . $request->genero . '%');
        }

        if ($request->filled('diretor')) {
            $query->where('diretor', 'LIKE', '%' . $request->diretor . '%');
        }

        if ($request->filled('ano_lancamento')) {
            $query->where('ano_lancamento', $request->ano_lancamento);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nome', 'LIKE', "%{$q}%")
                    ->orWhere('diretor', 'LIKE', "%{$q}%")
                    ->orWhere('descricao', 'LIKE', "%{$q}%");
            });
        }

        return $query
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();
    }

    public function searchBiblioteca(?string $search, int $userId): LengthAwarePaginator
    {
        return Filme::where('user_id', $userId)
            ->when($search, function ($qBuilder) use ($search) {
                $qBuilder->where(function ($sub) use ($search) {
                    $sub->where('nome', 'like', "%{$search}%")
                        ->orWhere('diretor', 'like', "%{$search}%")
                        ->orWhere('genero', 'like', "%{$search}%")
                        ->orWhere('ano_lancamento', 'like', "%{$search}%");
                });
            })
            ->orderBy('nome')
            ->paginate(20)
            ->withQueryString();
    }
}
