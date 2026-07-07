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
     * Exibe a visualização completa de uma coleção/gênero específico do TMDB.
     *
     * @param string|int $id
     * @param TMDBService $tmdb
     * @return \Illuminate\View\View
     */
    public function collection($id, TMDBService $tmdb)
    {
        $title = 'Coleção';
        $rawMovies = [];

        switch ($id) {
            case 'em-alta':
                $title = 'Em Alta';
                $rawMovies = $tmdb->getTrending();
                break;
            case 'populares':
                $title = 'Populares';
                $rawMovies = $tmdb->getPopular();
                break;
            case 'mais-votados':
                $title = 'Mais Votados';
                $rawMovies = $tmdb->getTopRated();
                break;
            case 'em-cartaz':
                $title = 'Em Cartaz';
                $rawMovies = $tmdb->getNowPlaying();
                break;
            case 'proximos-lancamentos':
                $title = 'Próximos Lançamentos';
                $rawMovies = $tmdb->getUpcoming();
                break;
            default:
                // Se for um ID numérico de gênero
                if (is_numeric($id)) {
                    $genres = collect($tmdb->getGenres());
                    $genre = $genres->firstWhere('id', (int) $id);
                    $title = $genre['name'] ?? 'Gênero';
                    $rawMovies = $tmdb->getMoviesByGenre((int) $id);
                } else {
                    $title = ucfirst(str_replace('-', ' ', $id));
                    $rawMovies = [];
                }
                break;
        }

        $movies = $tmdb->normalizeMovies($rawMovies);

        return view('discover.collection', compact('id', 'title', 'movies'));
    }
}
