<?php

namespace App\Services;

use App\Models\Filme;
use Illuminate\Support\Facades\Storage;

class FilmeMediaService
{
    public function deleteMovieImages(Filme $filme): void
    {
        if ($filme->poster) {
            Storage::disk('public')->delete($filme->poster);
        }

        if ($filme->poster_banner) {
            Storage::disk('public')->delete($filme->poster_banner);
        }
    }
}
