<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Filme extends Model
{
    protected $table = 'filmes';
    
    protected $fillable = [
        'tmdb_id',
        'nome',
        'descricao',
        'plataforma',
        'data_assistida',
        'diretor',
        'genero',
        'nota',
        'comentarios',
        'poster',
        'poster_banner',
        'assistido',
        'favorito',
        'generos',
        'ano_lancamento',
    ];

    protected $casts = [
    'assistido' => 'boolean',
    'favorito' => 'boolean',
    'ano_lancamento' => 'integer',
    ];


    // Buscar Banner 
    public function getPosterBannerUrlAttribute()
    {
    if (!$this->poster_banner) return null;

    // Se for URL completa (começa com http), retorna diretamente
    if (Str::startsWith($this->poster_banner, 'http')) {
        return $this->poster_banner;
    }

    // Senão assume que é arquivo local
    return asset('storage/posters_banners/' . $this->poster_banner);
    }


    // Buscar Poster
    public function getPosterUrlAttribute()
    {
    if (!$this->poster) return null;

    // Se for URL completa (começa com http), retorna diretamente
    if (Str::startsWith($this->poster, 'http')) {
        return $this->poster;
    }

    // Senão assume que é arquivo local
    return asset('storage/posters/' . $this->poster);
    }

    // Filtro Dashboard Seção
    public static function filtrarPor($campo, $valor, $limit = 5, $ordem = 'desc', $colunaOrdem = 'created_at')
    {
        return self::where($campo, 'like', "%{$valor}%")
                    ->orderBy($colunaOrdem, $ordem)
                    ->take($limit)
                    ->get();
    }

}



