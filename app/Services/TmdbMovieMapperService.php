<?php

namespace App\Services;

class TmdbMovieMapperService
{
    public function __construct(protected TMDBService $tmdb)
    {
    }

    public function mapDetails(array $tmdbData): array
    {
        $director = $this->extractDirector($tmdbData);
        $genres = $this->extractGenres($tmdbData);

        return [
            'director' => $director,
            'genres' => $genres,
            'posterUrl' => $this->tmdb->getImageUrl($tmdbData['poster_path'] ?? null, 'w500'),
            'backdropUrl' => $this->tmdb->getImageUrl($tmdbData['backdrop_path'] ?? null, 'w1280'),
            'tmdbRating' => isset($tmdbData['vote_average']) ? round($tmdbData['vote_average'], 1) : null,
        ];
    }

    public function mapSearchResult(array $movie): array
    {
        return [
            'tmdb_id' => $movie['id'],
            'nome' => $movie['title'] ?? ($movie['name'] ?? 'Sem título'),
            'release_date' => $movie['release_date'] ?? null,
            'descricao' => $movie['overview'] ?? null,
            'poster_url' => $this->tmdb->getImageUrl($movie['poster_path'] ?? null, 'w500'),
        ];
    }

    public function mapLocalMovieAttributesFromTmdb(
        array $tmdbData,
        array $validated,
        int $assistido,
        ?string $descricaoFallback = null
    ): array {
        $details = $this->mapDetails($tmdbData);

        return [
            'nome' => $tmdbData['title'] ?? ($tmdbData['name'] ?? 'Sem título'),
            'descricao' => $tmdbData['overview'] ?? $descricaoFallback,
            'diretor' => $details['director'],
            'genero' => $details['genres'],
            'poster' => $details['posterUrl'],
            'poster_banner' => $details['backdropUrl'],
            'plataforma' => $validated['plataforma'] ?? null,
            'data_assistida' => $validated['data_assistida'] ?? null,
            'nota' => $validated['nota'] ?? null,
            'comentarios' => $validated['comentarios'] ?? null,
            'ano_lancamento' => !empty($tmdbData['release_date'])
                ? date('Y', strtotime($tmdbData['release_date']))
                : null,
            'assistido' => $assistido,
        ];
    }

    private function extractDirector(?array $tmdbData): ?string
    {
        if (empty($tmdbData['credits']['crew']) || !is_array($tmdbData['credits']['crew'])) {
            return null;
        }

        foreach ($tmdbData['credits']['crew'] as $crew) {
            if (($crew['job'] ?? null) && strtolower($crew['job']) === 'director') {
                return $crew['name'] ?? null;
            }
        }

        return null;
    }

    private function extractGenres(?array $tmdbData): ?string
    {
        if (empty($tmdbData['genres']) || !is_array($tmdbData['genres'])) {
            return null;
        }

        return implode(', ', array_column($tmdbData['genres'], 'name'));
    }
}
