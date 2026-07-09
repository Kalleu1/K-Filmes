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

        if ($request->filled('nota')) {
            if ($request->nota === 'none') {
                $query->whereNull('nota');
            } else {
                $query->where('nota', '>=', (float) $request->nota);
            }
        }

        if ($request->filled('plataforma')) {
            $query->where('plataforma', $request->plataforma);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nome', 'LIKE', "%{$q}%")
                    ->orWhere('diretor', 'LIKE', "%{$q}%")
                    ->orWhere('descricao', 'LIKE', "%{$q}%");
            });
        }

        $sort = $request->input('ordenacao', 'recentes');
        switch ($sort) {
            case 'antigos':
                $query->orderBy('created_at', 'asc');
                break;
            case 'nota_max':
                $query->orderByRaw('nota IS NULL, nota DESC');
                break;
            case 'nota_min':
                $query->orderByRaw('nota IS NULL, nota ASC');
                break;
            case 'nome_az':
                $query->orderBy('nome', 'asc');
                break;
            case 'nome_za':
                $query->orderBy('nome', 'desc');
                break;
            case 'ano':
                $query->orderByRaw('ano_lancamento IS NULL, ano_lancamento DESC');
                break;
            case 'data_assistido':
                $query->orderByRaw('data_assistida IS NULL, data_assistida DESC');
                break;
            case 'recentes':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query
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

    public function getDashboardStats(int $userId): array
    {
        return [
            'watched' => $this->getWatchedCount($userId),
            'average_rating' => $this->getAverageRating($userId),
            'favorite_genre' => $this->getFavoriteGenre($userId),
            'last_movie' => $this->getLastWatchedMovie($userId),
        ];
    }

    public function getWatchedCount(int $userId): int
    {
        return Filme::where('user_id', $userId)
            ->where('assistido', true)
            ->count();
    }

    public function getAverageRating(int $userId): float
    {
        $avg = Filme::where('user_id', $userId)
            ->where('assistido', true)
            ->whereNotNull('nota')
            ->avg('nota');

        return $avg ? round((float) $avg, 1) : 0.0;
    }

    public function getFavoriteGenre(int $userId): string
    {
        $allGenres = Filme::where('user_id', $userId)
            ->whereNotNull('genero')
            ->where('genero', '!=', '')
            ->pluck('genero');

        $genreCounts = [];
        foreach ($allGenres as $genreStr) {
            $genres = array_map('trim', explode(',', $genreStr));
            foreach ($genres as $g) {
                if ($g === '') continue;
                $genreCounts[$g] = ($genreCounts[$g] ?? 0) + 1;
            }
        }

        arsort($genreCounts);
        return !empty($genreCounts) ? (string) key($genreCounts) : 'Nenhum';
    }

    public function getLastWatchedMovie(int $userId): array
    {
        $movie = Filme::where('user_id', $userId)
            ->where('assistido', true)
            ->whereNotNull('data_assistida')
            ->orderBy('data_assistida', 'desc')
            ->first();

        return [
            'title' => $movie ? $movie->nome : 'Nenhum',
            'date' => $movie ? date('d/m/Y', strtotime($movie->data_assistida)) : '-',
        ];
    }
}
