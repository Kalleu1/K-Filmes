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
        if (empty($this->poster_banner)) {
            return null;
        }

        $val = trim($this->poster_banner);

        if (Str::startsWith($val, ['http://', 'https://', '//'])) {
            return $val;
        }

        if (Str::startsWith($val, '/storage/')) {
            return asset(ltrim($val, '/'));
        }

        if (Str::startsWith($val, 'storage/')) {
            return asset($val);
        }

        if (Str::startsWith($val, 'posters_banners/')) {
            return asset('storage/' . $val);
        }

        if (Str::startsWith($val, '/')) {
            return 'https://image.tmdb.org/t/p/w1280' . $val;
        }

        return asset('storage/posters_banners/' . ltrim($val, '/'));
    }

    // Buscar Poster
    public function getPosterUrlAttribute()
    {
        if (empty($this->poster)) {
            return null;
        }

        $val = trim($this->poster);

        if (Str::startsWith($val, ['http://', 'https://', '//'])) {
            return $val;
        }

        if (Str::startsWith($val, '/storage/')) {
            return asset(ltrim($val, '/'));
        }

        if (Str::startsWith($val, 'storage/')) {
            return asset($val);
        }

        if (Str::startsWith($val, 'posters/')) {
            return asset('storage/' . $val);
        }

        if (Str::startsWith($val, '/')) {
            return 'https://image.tmdb.org/t/p/w500' . $val;
        }

        return asset('storage/posters/' . ltrim($val, '/'));
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



