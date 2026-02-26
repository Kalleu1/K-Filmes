<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FilmeController;
use App\Http\Controllers\FilmeDoDiaController;
use App\Http\Controllers\ShareController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Redirecionamento inicial
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('dashboard');
});

/*
| Rotas protegidas
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    | Biblioteca / Filmes
    */
    Route::get('/biblioteca', [FilmeController::class, 'biblioteca'])
        ->name('filmes.biblioteca');

    Route::get('/biblioteca/buscar', [FilmeController::class, 'buscarBiblioteca'])
        ->name('filmes.buscarBiblioteca');

    Route::get('/filmes/assistidos', [FilmeController::class, 'assistidos'])
        ->name('filmes.assistidos');

    Route::get('/filmes/nao-assistidos', [FilmeController::class, 'naoAssistidos'])
        ->name('filmes.naoAssistidos');

    Route::post('/filmes/{id}/marcar-assistido', [FilmeController::class, 'marcarAssistido'])
        ->name('filmes.marcarAssistido');

    Route::post('/filmes/{id}/favorito', [FilmeController::class, 'toggleFavorito'])
        ->name('filmes.toggleFavorito');

    /*
    | TMDB
    */
    Route::get('/filmes/tmdb/{tmdb_id}', [FilmeController::class, 'showTmdb'])
        ->name('filmes.showTmdb');

    Route::post('/filmes/tmdb/{tmdb_id}/save', [FilmeController::class, 'saveTmdb'])
        ->name('filmes.saveTmdb');

    Route::get('/filmes/busca', [FilmeController::class, 'busca'])
        ->name('filmes.busca');

    Route::get('/filmes/buscar', [FilmeController::class, 'buscarTmdb'])
        ->name('filmes.buscarTmdb');

    /*
    | Compartilhamento (Share)
    */
    Route::get('/filmes/{id}/share', [ShareController::class, 'preview'])
        ->name('filme.share.preview');

    Route::post('/filmes/{id}/share-image', [ShareController::class, 'generate'])
        ->name('filme.share.generate');

    Route::get('/filmes/{id}/share-render', [ShareController::class, 'render'])
        ->name('filme.share.render');

    Route::get('/filmes/{id}/share-image', [ShareController::class, 'gerarShareImage'])
        ->name('filmes.share-image');

    /*
    | Filme do Dia
    */
    Route::get('/filme-do-dia', [FilmeDoDiaController::class, 'index'])
        ->name('filmes.filme-do-dia');

    Route::get('/filme-do-dia/aleatorios', [FilmeDoDiaController::class, 'aleatorios'])
        ->name('filme-do-dia.aleatorios');

    Route::post('/filme-do-dia/sortear', [FilmeDoDiaController::class, 'sortear'])
        ->name('filme-do-dia.sortear');


    Route::resource('filmes', FilmeController::class)->except(['create', 'store']);
});

/*
|--------------------------------------------------------------------------
| Rotas de autenticação (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
