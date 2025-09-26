<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TMDBService
{
    protected $base;
    protected $key;
    protected $imageBase;
    protected $imageSizes = [];

    public function __construct()
    {
        $this->base = config('services.tmdb.base_url', 'https://api.themoviedb.org/3/');
        $this->key  = config('services.tmdb.key');
        $this->initConfig();
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
            $this->imageSizes = $config['images']['poster_sizes'] ?? ['w500'];
        } else {
            $this->imageBase  = config('services.tmdb.image_url', 'https://image.tmdb.org/t/p/');
            $this->imageSizes = ['w500'];
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
        return $this->get('search/movie', [
            'query' => $query,
            'page' => $page,
            'include_adult' => false,
            'language' => 'pt-BR',
        ]);
    }

    public function getMovie(int $id, string $language = 'pt-BR')
    {
        $movie = $this->get("movie/{$id}", [
            'append_to_response' => 'credits,images',
            'language' => $language,
        ]);

        if (!$movie) {
            return null;
        }

        // Pegar o diretor do array de crew
        $director = '';
        if (!empty($movie['credits']['crew'])) {
            $dir = collect($movie['credits']['crew'])->firstWhere('job', 'Director');
            $director = $dir['name'] ?? '';
        }
        $movie['director'] = $director;

        return $movie;
    }

    public function getImageUrl(?string $path, string $size = 'w500')
    {
        if (!$path) return null;
        $sizeToUse = in_array($size, $this->imageSizes) ? $size : $this->imageSizes[0];
        return rtrim($this->imageBase, '/') . '/' . $sizeToUse . '/' . ltrim($path, '/');
    }

    //

    //TRENDS

    public function getNowPlaying(string $language = 'pt-BR')
{
    return $this->get('movie/now_playing', [
        'language' => $language,
        'region' => 'BR', // opcional: traz em cartaz no Brasil
    ])['results'] ?? [];
}

public function getTrending(string $timeWindow = 'week', string $language = 'pt-BR')
{
    // timeWindow pode ser 'day' ou 'week'
    return $this->get("trending/movie/{$timeWindow}", [
        'language' => $language,
    ])['results'] ?? [];
}

public function getTopRated(string $language = 'pt-BR')
{
    return $this->get('movie/top_rated', [
        'language' => $language,
    ])['results'] ?? [];
}

public function getUpcoming(string $language = 'pt-BR')
{
    return $this->get('movie/upcoming', [
        'language' => $language,
        'region' => 'BR', // opcional
    ])['results'] ?? [];
}

public function getSimilarMovies(int $tmdbId, string $language = 'pt-BR', int $limit = 7): array
{
    $response = $this->get("movie/{$tmdbId}/similar", [
        'language' => $language,
    ]);

    $results = $response['results'] ?? [];

    // Pega os primeiros $limit sem embaralhar
    return $this->normalizeMovies(array_slice($results, 0, $limit));
}




    
}
