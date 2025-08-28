<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FilmeController;
use Illuminate\Support\Facades\Route;


Route::resource('filmes', FilmeController::class);

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');