<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use Illuminate\Http\Request;

class DiscoverController extends Controller
{
    /**
     * Exibe a página principal de descoberta com dados reais do TMDB.
     *
     * @param TMDBService $tmdb
     * @return \Illuminate\View\View
     */
    public function index(TMDBService $tmdb)
    {
        // Obter e normalizar as coleções principais
        $trendingMovies = array_slice($tmdb->normalizeMovies($tmdb->getTrending()), 0, 10);
        $popularMovies  = array_slice($tmdb->normalizeMovies($tmdb->getPopular()), 0, 10);
        $topRatedMovies = array_slice($tmdb->normalizeMovies($tmdb->getTopRated()), 0, 10);
        $nowPlaying     = array_slice($tmdb->normalizeMovies($tmdb->getNowPlaying()), 0, 10);
        $upcoming       = array_slice($tmdb->normalizeMovies($tmdb->getUpcoming()), 0, 10);

        // Obter os gêneros do TMDB para navegação
        $genres = $tmdb->getGenres();

        return view('discover.index', compact(
            'trendingMovies',
            'popularMovies',
            'topRatedMovies',
            'nowPlaying',
            'upcoming',
            'genres'
        ));
    }

    /**
     * Exibe a visualização completa de uma coleção/gênero específico do TMDB com paginação progressiva.
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

        $collectionKey = $isGenre ? 'genero' : $id;
        $extraId = $isGenre ? (int) $id : null;

        // Buscar filmes usando o método paginado
        $paginatedData = $tmdb->getPaginatedCollection($collectionKey, $page, $extraId);
        $movies = $tmdb->normalizeMovies($paginatedData['results']);
        $totalPages = (int) $paginatedData['total_pages'];

        // Determinar o título do cabeçalho
        $title = 'Coleção';
        if ($isGenre) {
            $genres = collect($tmdb->getGenres());
            $genre = $genres->firstWhere('id', (int) $id);
            $title = $genre['name'] ?? 'Gênero';
        } else {
            $titles = [
                'em-alta' => 'Em Alta',
                'populares' => 'Populares',
                'mais-votados' => 'Mais Votados',
                'em-cartaz' => 'Em Cartaz',
                'proximos-lancamentos' => 'Próximos Lançamentos',
            ];
            $title = $titles[$id] ?? ucfirst(str_replace('-', ' ', $id));
        }

        // Se for uma requisição AJAX, retornar apenas os itens do grid em formato JSON
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
