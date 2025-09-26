<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FilmeController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Rotas personalizadas de filmes
Route::get('/filmes/assistidos', [FilmeController::class, 'assistidos'])->name('filmes.assistidos');
Route::get('/filmes/nao-assistidos', [FilmeController::class, 'naoAssistidos'])->name('filmes.naoAssistidos');

// Marcar como assistido
Route::post('/filmes/{id}/marcar-assistido', [FilmeController::class, 'marcarAssistido'])->name('filmes.marcarAssistido');

// Integração com TMDB
Route::get('/filmes/tmdb/{tmdb_id}', [FilmeController::class, 'showTmdb'])->name('filmes.showTmdb');
Route::post('/filmes/tmdb/{tmdb_id}/save', [FilmeController::class, 'saveTmdb'])->name('filmes.saveTmdb');

// Página de busca
Route::get('/filmes/busca', [FilmeController::class, 'busca'])->name('filmes.busca');
Route::get('/filmes/buscar', [FilmeController::class, 'buscarTmdb'])->name('filmes.buscarTmdb');

//Pagina Biblioteca
Route::get('/biblioteca', [FilmeController::class, 'biblioteca'])->name('filmes.biblioteca');
Route::get('/biblioteca/buscar', [FilmeController::class, 'buscarBiblioteca'])->name('filmes.buscarBiblioteca');

Route::post('/filmes/{id}/favorito', [FilmeController::class, 'toggleFavorito'])
    ->name('filmes.toggleFavorito');



// Rotas RESTful de filmes (tem que vir por último)
Route::resource('filmes', FilmeController::class);
           