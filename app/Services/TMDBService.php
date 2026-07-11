<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;



class TMDBService
{

    protected $base;
    protected $key;
    protected $imageBase;
    protected $imageSizes = [
    'w92', 
    'w154', 
    'w185', 
    'w342', 
    'w500', 
    'w1280', 
    'original'];

    public function __construct()
    {
        $this->base = config('services.tmdb.base_url', 'https://api.themoviedb.org/3/');
        $this->key  = config('services.tmdb.key');
        $this->initConfig();
    }

    protected function remember(string $key, int $minutes, \Closure $callback)
    {
        return Cache::remember($key, now()->addMinutes($minutes), $callback);
    }


    protected function initConfig()
    {
        $config = Cache::remember('tmdb_configuration', 60 * 24, function () {
            $resp = Http::get($this->base . 'configuration', [
                'api_key' => $this->key
            ]);
            return $resp->successful() ? $resp->json() : null;
        });

        if ($config && isset($config['images'])) {
            $this->imageBase  = $config['images']['secure_base_url'] ?? $config['images']['base_url'];
            
            // CORREÇÃO:
            // Pegamos os tamanhos de poster E de backdrop e juntamos tudo num array só
            $posters = $config['images']['poster_sizes'] ?? [];
            $backdrops = $config['images']['backdrop_sizes'] ?? [];
            
            // array_merge junta as duas listas e array_unique remove duplicados (como w780 que tem nos dois)
            $this->imageSizes = array_unique(array_merge($posters, $backdrops));
        } else {
            $this->imageBase  = config('services.tmdb.image_url', 'https://image.tmdb.org/t/p/');
            $this->imageSizes = ['w1280'];
        }
    }

    

    protected function get($endpoint, $params = [])
    {
        $params['api_key'] = $this->key;
        $resp = Http::get(rtrim($this->base, '/') . '/' . ltrim($endpoint, '/'), $params);
        return $resp->successful() ? $resp->json() : null;
    }

    //PADRONIZAÇÃO

    public function normalizeMovie(array|object $movie): object
    {
        if (is_object($movie)) {
            $movie = (array) $movie;
        }

        return (object) [
            'tmdb_id'    => $movie['id'] ?? null,
            'nome'       => $movie['title'] ?? $movie['name'] ?? 'Sem título',
            'ano'        => isset($movie['release_date']) ? substr($movie['release_date'], 0, 4) : null,
            'poster_url' => $this->getImageUrl($movie['poster_path'] ?? null, 'w500'),
            'nota'       => $movie['vote_average'] ?? null,
            'diretor'    => $movie['director'] ?? null,
            'genero'     => isset($movie['genres'][0]['name']) ? $movie['genres'][0]['name'] : null,
        ];
    }


    public function normalizeMovies(array $movies): array
    {
        return collect($movies)->map(fn($m) => $this->normalizeMovie($m))->toArray();
    }


    public function searchMovies(string $query, int $page = 1)
    {
        $key = 'tmdb:search:' . Str::slug($query) . ":{$page}";

        return $this->remember($key, 15, function () use ($query, $page) {
            return $this->get('search/movie', [
                'query' => $query,
                'page' => $page,
                'include_adult' => false,
                'language' => 'pt-BR',
            ]);
        });
    }

public function getMovie(int $id, string $language = 'pt-BR')
{
    $cacheKey = "tmdb:movie:full:{$id}:{$language}";

    return $this->remember($cacheKey, 720, function () use ($id, $language) {

        $movie = $this->get("movie/{$id}", [
            'append_to_response' => 'credits,images',
            'language' => $language,
        ]);

        if (!$movie || empty($movie['id'])) {
            $movie = $this->get("movie/{$id}", [
                'append_to_response' => 'credits,images',
                'language' => 'en-US',
            ]);
        }

        if (!$movie) return null;

        
        $director = null;
        if (!empty($movie['credits']['crew'])) {
            $dir = collect($movie['credits']['crew'])
                ->firstWhere('job', 'Director');
            $director = $dir['name'] ?? null;
        }

        return [
            'id'            => $movie['id'],
            'title'         => $movie['title'] ?? null,
            'overview'      => $movie['overview'] ?? null,
            'genres'        => $movie['genres'] ?? [],
            'director'      => $director,
            'vote_average'  => $movie['vote_average'] ?? null,
            'poster_path'   => $movie['poster_path'] ?? null,
            'backdrop_path' => $movie['backdrop_path'] ?? null,
            'credits'       => $movie['credits'] ?? [],
            // Adicionado: duração e data de lançamento para uso nas views
            'runtime'       => $movie['runtime'] ?? null,
            'release_date'  => $movie['release_date'] ?? null,
        ];
    });
}

public function getMoviePosters(int $tmdbId)
{
    $key = "tmdb:movie:{$tmdbId}:posters";
    return $this->remember($key, 10080, function () use ($tmdbId) {
        $response = $this->get("movie/{$tmdbId}/images");
        if (!$response || empty($response['posters'])) {
            return [];
        }

        return collect($response['posters'])
            ->filter(function ($item) {
                return ($item['iso_639_1'] === 'pt' || $item['iso_639_1'] === 'br');
            })
            ->map(function ($item) {
                return [
                    'poster_path' => $item['file_path'],
                    'preview_url' => $this->getImageUrl($item['file_path'], 'w500'),
                    'language'    => $item['iso_639_1'] ?? null,
                ];
            })
            ->values()
            ->toArray();
    });
}







    public function getImageUrl(?string $path, string $size = 'w500')
    {
         
        if (!$path) return null;
        $sizeToUse = in_array($size, $this->imageSizes) ? $size : $this->imageSizes[0];
        return rtrim($this->imageBase, '/') . '/' . $sizeToUse . '/' . ltrim($path, '/');
    }

    public function ensureImageSaved(?string $path, string $size, int $tmdbId, string $folder = 'posters_banners'): ?string
{
    if (!$path) {
        return null;
    }

    // Monta a URL oficial (reusa sua função existente)
    $imageUrl = $this->getImageUrl($path, $size);

    if (!$imageUrl) {
        return null;
    }

    $filename = "{$tmdbId}.jpg";
    $storagePath = "{$folder}/{$filename}";

    // Se já existe, retorna o path local
    if (Storage::disk('public')->exists($storagePath)) {
        return Storage::disk('public')->path($storagePath);
    }

    try {
        $contents = file_get_contents($imageUrl);

        if ($contents === false) {
            return null;
        }

        Storage::disk('public')->put($storagePath, $contents);

        return Storage::disk('public')->path($storagePath);
    } catch (\Throwable $e) {
        return null;
    }
}

    //

    //TRENDS

    public function getNowPlaying(string $language = 'pt-BR')
{
    $key = "tmdb:now_playing:{$language}";
    return $this->remember($key, 30, function () use ($language) {
        return $this->get('movie/now_playing', [
            'language' => $language,
            'region' => 'BR',
        ])['results'] ?? [];
    });
}

public function getTrending(string $timeWindow = 'week', string $language = 'pt-BR')
{
    $key = "tmdb:trending:{$timeWindow}:{$language}";
    return $this->remember($key, 60, function () use ($timeWindow, $language) {
        return $this->get("trending/movie/{$timeWindow}", [
            'language' => $language,
        ])['results'] ?? [];
    });
}

public function getTopRated(string $language = 'pt-BR')
{
    $key = "tmdb:top_rated:{$language}";
    return $this->remember($key, 360, function () use ($language) {
        return $this->get('movie/top_rated', [
            'language' => $language,
        ])['results'] ?? [];
    });
}

public function getUpcoming(string $language = 'pt-BR')
{
    $key = "tmdb:upcoming:{$language}";
    return $this->remember($key, 720, function () use ($language) {
        return $this->get('movie/upcoming', [
            'language' => $language,
            'region' => 'BR',
        ])['results'] ?? [];
    });
}

public function getSimilarMovies(int $tmdbId, string $language = 'pt-BR', int $limit = 7): array
{
    $key = "tmdb:similar:{$tmdbId}:{$language}:{$limit}";

    return $this->remember($key, 360, function () use ($tmdbId, $language, $limit) {

        $response = $this->get("movie/{$tmdbId}/similar", [
            'language' => $language,
        ]);

        $results = $response['results'] ?? [];

        return $this->normalizeMovies(
            array_slice($results, 0, $limit)
        );
    });
}


public function getMoviesByDirector(string $directorName, int $limit = 7, string $language = 'pt-BR'): array
{
    $key = 'tmdb:director:' . Str::slug($directorName) . ":{$language}:{$limit}";

    return $this->remember($key, 720, function () use ($directorName, $limit, $language) {

        $search = $this->get('search/person', [
            'query' => $directorName,
            'language' => $language,
        ]);

        if (empty($search['results'])) return [];

        $person = $search['results'][0] ?? null;
        if (!$person || empty($person['id'])) return [];

        $credits = $this->get("person/{$person['id']}/movie_credits", [
            'language' => $language,
        ]);

        if (empty($credits['crew'])) return [];

        $directed = array_filter($credits['crew'], fn($c) =>
            isset($c['job']) && strtolower($c['job']) === 'director'
        );

        usort($directed, fn($a, $b) =>
            ($b['popularity'] ?? 0) <=> ($a['popularity'] ?? 0)
        );

        return $this->normalizeMovies(
            array_slice($directed, 0, $limit)
        );
    });
}

/**
 * Retorna URLs responsivas de poster (mobile) e backdrop (desktop)
 * 
 * Útil para <picture> tags que servem diferentes imagens por viewport
 *
 * Objeto com mobile_url, desktop_url, mobile_path, desktop_path
 *                     ou null se nenhuma imagem disponível
 *
 * Exemplo:
 * ```php
 * $responsive = $tmdb->getResponsiveBackdrop($movie);
 * // $responsive->mobile_url   // poster para mobile (w500)
 * // $responsive->desktop_url  // backdrop para desktop (w1280)
 * // $responsive->mobile_path  // path do poster
 * // $responsive->desktop_path // path do backdrop
 * ```
 */
public function getResponsiveBackdrop($movie): ?object
{
    if (is_object($movie)) {
        $movie = (array) $movie;
    }

    $poster_path = $movie['poster_path'] ?? null;
    $backdrop_path = $movie['backdrop_path'] ?? null;

    // Fallback: se não tem backdrop, usa poster em ambas as versões
    if (!$backdrop_path && !$poster_path) {
        return null;
    }

    // Lógica: mobile sempre usa poster se disponível, desktop usa backdrop se disponível
    $mobile_path = $poster_path ?? $backdrop_path;
    $desktop_path = $backdrop_path ?? $poster_path;

    return (object) [
        'mobile_url'  => $this->getImageUrl($mobile_path, 'w500'),
        'desktop_url' => $this->getImageUrl($desktop_path, 'w1280'),
        'mobile_path' => $mobile_path,
        'desktop_path' => $desktop_path,
    ];
}

public function getPopular(string $language = 'pt-BR')
{
    $key = "tmdb:popular:{$language}";
    return $this->remember($key, 360, function () use ($language) {
        return $this->get('movie/popular', [
            'language' => $language,
        ])['results'] ?? [];
    });
}

public function getGenres(string $language = 'pt-BR')
{
    $key = "tmdb:genres:{$language}";
    return $this->remember($key, 1440, function () use ($language) {
        return $this->get('genre/movie/list', [
            'language' => $language,
        ])['genres'] ?? [];
    });
}

public function getMoviesByGenre(int $genreId, string $language = 'pt-BR')
{
    $key = "tmdb:genre:movies:{$genreId}:{$language}";
    return $this->remember($key, 720, function () use ($genreId, $language) {
        return $this->get('discover/movie', [
            'with_genres' => $genreId,
            'language' => $language,
            'sort_by' => 'popularity.desc',
        ])['results'] ?? [];
    });
}

public function getPaginatedCollection(array $config, int $page = 1, string $language = 'pt-BR')
{
    $type = $config['type'] ?? '';
    $slug = $config['slug'] ?? 'default';
    $params = $config['params'] ?? [];
    
    $key = "tmdb:collection:{$slug}:{$page}:{$language}";
    
    return $this->remember($key, 60, function () use ($type, $params, $page, $language) {
        $endpoint = '';
        $query = [
            'language' => $language,
            'page' => $page,
        ];
        
        switch ($type) {
            case 'trending':
                $timeWindow = $params['time_window'] ?? 'week';
                $endpoint = "trending/movie/{$timeWindow}";
                break;
            case 'popular':
                $endpoint = 'movie/popular';
                break;
            case 'top_rated':
                $endpoint = 'movie/top_rated';
                break;
            case 'now_playing':
                $endpoint = 'movie/now_playing';
                $query['region'] = 'BR';
                break;
            case 'upcoming':
                $endpoint = 'movie/upcoming';
                $query['region'] = 'BR';
                break;
            case 'genre':
                $endpoint = 'discover/movie';
                $query['with_genres'] = $params['genre_id'] ?? null;
                $query['sort_by'] = 'popularity.desc';
                break;
            case 'discover':
                $endpoint = 'discover/movie';
                $query = array_merge($query, $params);
                break;
            default:
                return [
                    'results' => [],
                    'total_pages' => 1,
                    'page' => $page
                ];
        }
        
        $response = $this->get($endpoint, $query);
        return [
            'results' => $response['results'] ?? [],
            'total_pages' => $response['total_pages'] ?? 1,
            'page' => $response['page'] ?? 1,
        ];
    });
}
}
