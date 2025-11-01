<?php

namespace App\Http\Controllers;

use App\Models\Filme;
use App\Services\TMDBService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FilmeDoDiaController extends Controller
{

    public function index()
    {
        return view('filmes.filme-do-dia');
    }

        public function aleatorios()
{
    try {
        // Busca 10 filmes aleatórios
        $filmes = Filme::inRandomOrder()->take(10)->get(['id', 'nome', 'poster']);

        $results = $filmes->map(function ($filme) {
            return [
                'id' => $filme->id,
                'titulo' => $filme->nome,
                'poster' => $filme->poster ?? asset('images/placeholder-poster.png'),
            ];
        })->values();

        return response()->json(['results' => $results]);

    } catch (\Exception $e) {
        Log::error('Erro ao buscar filmes aleatórios: ' . $e->getMessage());
        return response()->json(['results' => [], 'error' => 'Erro interno no servidor'], 500);
    }
}






    public function sortear(Request $request, TMDBService $tmdbService)
    {
        $fonte = $request->input('fonte', 'biblioteca');
        $ano = $request->input('ano');
        $diretor = $request->input('diretor');
        $genero = $request->input('genero');

        if ($fonte === 'biblioteca') {
            $query = Filme::query();

            if ($ano) $query->where('ano_lancamento', $ano);
            if ($diretor) $query->where('diretor', 'like', "%{$diretor}%");
            if ($genero) $query->where('genero', 'like', "%{$genero}%");

            $filme = $query->inRandomOrder()->first();

            if (!$filme) {
                return response()->json(['error' => 'Nenhum filme encontrado.'], 404);
            }

            return response()->json([
                'fonte' => 'biblioteca',
                'id' => $filme->id,
                'nome' => $filme->nome,
                'poster' => $filme->poster,
            ]);
        } else {
            $movies = $tmdbService->getTrending('pt-BR');
            $filme = collect($movies)->random();

            if (!$filme) {
                return response()->json(['error' => 'Nenhum filme encontrado.'], 404);
            }

            return response()->json([
                'fonte' => 'tmdb',
                'id' => $filme['tmdb_id'] ?? $filme['id'] ?? $filme->id ?? null,
                'nome' => $filme['title'] ?? $filme['nome'] ?? $filme->title ?? 'Sem título',
                'poster' => isset($filme['poster_path']) || isset($filme->poster_path)
                    ? 'https://image.tmdb.org/t/p/w500' . ($filme['poster_path'] ?? $filme->poster_path)
                    : ($filme['poster_url'] ?? $filme->poster_url ?? asset('images/placeholder-poster.png')),
            ]);

        }
    }


    


}
