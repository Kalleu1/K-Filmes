<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Filme extends Model
{
    protected $table = 'filmes';
    
    protected $fillable = [
        'user_id',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }


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

    

    public function scopeDoUsuario($query)
{
    return $query->where('user_id', Auth::id());
}

    public function scopeNaoAssistidos($query)
    {
        return $query->where('assistido', false);
    }


}



