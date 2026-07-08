<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use Illuminate\Http\Request;

class DiscoverController extends Controller
{
    /**
     * Exibe a página principal de descoberta com base nas coleções configuradas.
     *
     * @param TMDBService $tmdb
     * @return \Illuminate\View\View
     */
    public function index(TMDBService $tmdb)
    {
        // Ler coleções do arquivo de configuração
        $collectionsConfig = config('discover_collections.collections', []);
        $collections = [];

        foreach ($collectionsConfig as $config) {
            // Obter a primeira página da coleção (20 filmes) e pegar apenas 10
            $paginatedData = $tmdb->getPaginatedCollection($config, 1);
            $movies = array_slice($tmdb->normalizeMovies($paginatedData['results']), 0, 10);

            $collections[] = array_merge($config, [
                'movies' => $movies
            ]);
        }

        // Obter os gêneros do TMDB para a barra de tags rápidas
        $genres = $tmdb->getGenres();

        return view('discover.index', compact('collections', 'genres'));
    }

    /**
     * Exibe a visualização de grid completo de uma coleção/gênero.
     *
     * @param string|int $id
     * @param Request $request
     * @param TMDBService $tmdb
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function collection($id, Request $request, TMDBService $tmdb)
    {
        $page = (int) $request->input('page', 1);
        $isGenre = is_numeric($id);

        // Buscar coleção na configuração
        $config = config("discover_collections.collections.{$id}");

        // Compatibilidade retrógrada: Se não estiver cadastrado mas for ID numérico de gênero
        if (!$config && $isGenre) {
            $genres = collect($tmdb->getGenres());
            $genre = $genres->firstWhere('id', (int) $id);
            $title = $genre['name'] ?? 'Gênero';

            $config = [
                'slug' => $id,
                'title' => $title,
                'type' => 'genre',
                'params' => [
                    'genre_id' => (int) $id,
                ]
            ];
        }

        if (!$config) {
            abort(404, 'Coleção não encontrada.');
        }

        $title = $config['title'];

        // Buscar filmes paginados
        $paginatedData = $tmdb->getPaginatedCollection($config, $page);
        $movies = $tmdb->normalizeMovies($paginatedData['results']);
        $totalPages = (int) $paginatedData['total_pages'];

        // Responder à paginação progressiva AJAX
        if ($request->ajax()) {
            $html = view('discover.partials.movie-grid-items', compact('movies'))->render();
            return response()->json([
                'html' => $html,
                'page' => $page,
                'total_pages' => $totalPages,
            ]);
        }

        return view('discover.collection', compact('id', 'title', 'movies', 'page', 'totalPages'));
    }
}
