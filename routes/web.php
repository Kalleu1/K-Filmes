<?php

use App\Http\Controllers\FilmeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('filmes', FilmeController::class);
