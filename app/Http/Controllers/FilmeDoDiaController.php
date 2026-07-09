<?php

namespace App\Http\Controllers;

use App\Models\Filme;
use App\Services\TMDBService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Support\Toast\ToastMessages;

class FilmeDoDiaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(TMDBService $tmdbService)
    {
        // Pôster aleatório da biblioteca do próprio usuário logado para servir de placeholder
        $randomFilme = Filme::doUsuario()
            ->whereNotNull('poster')
            ->where('poster', '!=', '')
            ->inRandomOrder()
            ->first();

        $placeholderPoster = $randomFilme ? $randomFilme->poster : asset('images/placeholder-poster.png');

        // Gêneros da biblioteca do próprio usuário
        $allGenres = Filme::doUsuario()
            ->whereNotNull('genero')
            ->where('genero', '!=', '')
            ->pluck('genero');

        $bibliotecaGeneros = [];
        foreach ($allGenres as $genreStr) {
            $genres = array_map('trim', explode(',', $genreStr));
            foreach ($genres as $g) {
                if ($g !== '' && !in_array($g, $bibliotecaGeneros)) {
                    $bibliotecaGeneros[] = $g;
                }
            }
        }
        sort($bibliotecaGeneros);

        // Gêneros oficiais da TMDB
        $tmdbGeneros = $tmdbService->getGenres('pt-BR') ?? [];

        return view('filmes.filme-do-dia', compact('placeholderPoster', 'bibliotecaGeneros', 'tmdbGeneros'));
    }

    public function aleatorios(Request $request, TMDBService $tmdbService)
    {
        try {
            $fonte = $request->input('fonte', 'biblioteca');
            $ano = $request->input('ano');
            $diretor = $request->input('diretor');
            $genero = $request->input('genero');

            if ($fonte === 'biblioteca') {
                $query = Filme::doUsuario()->naoAssistidos();

                if ($ano) $query->where('ano_lancamento', $ano);
                if ($diretor) $query->where('diretor', 'like', "%{$diretor}%");
                if ($genero) $query->where('genero', 'like', "%{$genero}%");

                $filmes = $query
                    ->inRandomOrder()
                    ->take(10)
                    ->get(['id', 'nome', 'poster']);

                if ($filmes->isEmpty()) {
                    $filmes = Filme::doUsuario()
                        ->inRandomOrder()
                        ->take(10)
                        ->get(['id', 'nome', 'poster']);
                }

                $results = $filmes->map(function ($filme) {
                    return [
                        'id' => $filme->id,
                        'titulo' => $filme->nome,
                        'poster' => $filme->poster ?? asset('images/placeholder-poster.png'),
                    ];
                })->values();
            } else {
                // TMDB - Buscar filmes do gênero selecionado com filtros extras
                if ($diretor) {
                    $directorMovies = $tmdbService->getMoviesByDirector($diretor, 40, 'pt-BR');
                    $filtered = collect($directorMovies);
                    
                    if ($ano) {
                        $filtered = $filtered->filter(fn($m) => $m->ano == $ano);
                    }
                    if ($genero) {
                        $tmdbGenresList = $tmdbService->getGenres('pt-BR') ?? [];
                        $selectedGenreName = null;
                        foreach ($tmdbGenresList as $g) {
                            if ($g['id'] == $genero) {
                                $selectedGenreName = $g['name'];
                                break;
                            }
                        }
                        if ($selectedGenreName) {
                            $filtered = $filtered->filter(fn($m) => $m->genero && strtolower($m->genero) === strtolower($selectedGenreName));
                        }
                    }
                    
                    if ($filtered->isEmpty()) {
                        $filmes = collect();
                    } else {
                        $filmes = $filtered->shuffle()->take(10);
                    }
                } else {
                    $params = [
                        'language' => 'pt-BR',
                        'include_adult' => false,
                        'page' => rand(1, 10),
                    ];
                    if ($genero) {
                        $params['with_genres'] = $genero;
                    }
                    if ($ano) {
                        $params['primary_release_year'] = $ano;
                    }
                    
                    $response = $tmdbService->get('discover/movie', $params);
                    $resultsList = $response['results'] ?? [];
                    if (empty($resultsList)) {
                        $params['page'] = 1;
                        $response = $tmdbService->get('discover/movie', $params);
                        $resultsList = $response['results'] ?? [];
                    }

                    $filmes = collect($resultsList)->shuffle()->take(10);
                }

                $results = $filmes->map(function ($filme) use ($tmdbService) {
                    $isObj = is_object($filme);
                    return [
                        'id' => $isObj ? $filme->tmdb_id : $filme['id'],
                        'titulo' => $isObj ? $filme->nome : ($filme['title'] ?? 'Sem título'),
                        'poster' => $isObj 
                            ? ($filme->poster_url ?? asset('images/placeholder-poster.png'))
                            : (isset($filme['poster_path']) ? $tmdbService->getImageUrl($filme['poster_path'], 'w500') : asset('images/placeholder-poster.png')),
                    ];
                })->values();
            }

            return response()->json(['results' => $results]);

        } catch (\Exception $e) {
            return response()->json([
                'results' => [],
                'error' => 'Erro interno no servidor',
                'toast' => ToastMessages::tmdbUnavailable()
            ], 500);
        }
    }

    public function sortear(Request $request, TMDBService $tmdbService)
    {
        $fonte = $request->input('fonte', 'biblioteca');
        $ano = $request->input('ano');
        $diretor = $request->input('diretor');
        $genero = $request->input('genero');

        if ($fonte === 'biblioteca') {
            $query = Filme::doUsuario()->naoAssistidos();

            if ($ano) $query->where('ano_lancamento', $ano);
            if ($diretor) $query->where('diretor', 'like', "%{$diretor}%");
            if ($genero) $query->where('genero', 'like', "%{$genero}%");

            $filme = $query->inRandomOrder()->first();

            if (!$filme) {
                return response()->json([
                    'error' => 'Nenhum filme encontrado.',
                    'toast' => ToastMessages::custom('warning', 'Nenhum filme encontrado.')
                ], 404);
            }

            return response()->json([
                'fonte' => 'biblioteca',
                'id' => $filme->id,
                'nome' => $filme->nome,
                'poster' => $filme->poster ?? asset('images/placeholder-poster.png'),
            ]);
        } else {
            // TMDB
            $params = [
                'language' => 'pt-BR',
                'include_adult' => false,
            ];
            if ($genero) {
                $params['with_genres'] = $genero;
            }
            if ($ano) {
                $params['primary_release_year'] = $ano;
            }

            if ($diretor) {
                $directorMovies = $tmdbService->getMoviesByDirector($diretor, 40, 'pt-BR');
                $filtered = collect($directorMovies);
                
                if ($ano) {
                    $filtered = $filtered->filter(fn($m) => $m->ano == $ano);
                }
                if ($genero) {
                    $tmdbGenresList = $tmdbService->getGenres('pt-BR') ?? [];
                    $selectedGenreName = null;
                    foreach ($tmdbGenresList as $g) {
                        if ($g['id'] == $genero) {
                            $selectedGenreName = $g['name'];
                            break;
                        }
                    }
                    if ($selectedGenreName) {
                        $filtered = $filtered->filter(fn($m) => $m->genero && strtolower($m->genero) === strtolower($selectedGenreName));
                    }
                }
                
                if ($filtered->isEmpty()) {
                    return response()->json([
                        'error' => 'Nenhum filme encontrado na TMDB com estes filtros.',
                        'toast' => ToastMessages::custom('warning', 'Nenhum filme encontrado na TMDB com estes filtros.')
                    ], 404);
                }
                
                $filme = $filtered->random();
                
                return response()->json([
                    'fonte' => 'tmdb',
                    'id' => $filme->tmdb_id,
                    'nome' => $filme->nome,
                    'poster' => $filme->poster_url ?? asset('images/placeholder-poster.png'),
                ]);
            } else {
                $params['page'] = rand(1, 15);
                $response = $tmdbService->get('discover/movie', $params);
                $results = $response['results'] ?? [];
                if (empty($results)) {
                    $params['page'] = 1;
                    $response = $tmdbService->get('discover/movie', $params);
                    $results = $response['results'] ?? [];
                }

                if (empty($results)) {
                    return response()->json([
                        'error' => 'Nenhum filme encontrado na TMDB.',
                        'toast' => ToastMessages::custom('warning', 'Nenhum filme encontrado na TMDB.')
                    ], 404);
                }

                $filme = collect($results)->random();

                return response()->json([
                    'fonte' => 'tmdb',
                    'id' => $filme['id'],
                    'nome' => $filme['title'] ?? 'Sem título',
                    'poster' => isset($filme['poster_path'])
                        ? $tmdbService->getImageUrl($filme['poster_path'], 'w500')
                        : asset('images/placeholder-poster.png'),
                ]);
            }
        }
    }
}

    



