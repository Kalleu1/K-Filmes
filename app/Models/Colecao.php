<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Colecao extends Model
{
    use HasFactory;

    protected $table = 'colecoes';

    protected $fillable = [
        'user_id',
        'nome',
        'descricao',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function filmes(): BelongsToMany
    {
        return $this->belongsToMany(Filme::class, 'colecao_filme')
            ->withPivot('position')
            ->withTimestamps()
            ->orderByPivot('position');
    }
}
