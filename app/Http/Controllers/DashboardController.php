<?php

namespace App\Http\Controllers;

use App\Models\Filme;
use App\Services\TMDBService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(TMDBService $tmdb, \App\Services\FilmeBibliotecaService $bibliotecaService){

    

        //ESTATISTICAS RAPIDAS
        $totalFilmes = Filme::doUsuario()->count();

        $totalAssistidos = Filme::doUsuario()
            ->where('assistido', true)
            ->count();

        $mediaNotas = number_format(
            Filme::doUsuario()->avg('nota'),
            2,
            '.',
            '.'
        );

        $totalFavoritos = Filme::doUsuario()
            ->where('favorito', true)
            ->count();

        
        //BANNER
        $destaques = Filme::doUsuario()
            ->inRandomOrder()
            ->take(6)
            ->get()
            ->map(function($filme) use ($tmdb) {
                // Se o filme tem tmdb_id, busca as imagens responsivas
                if ($filme->tmdb_id) {
                    $tmdbMovie = $tmdb->getMovie((int) $filme->tmdb_id);
                    if ($tmdbMovie) {
                        $filme->responsiveBackdrop = $tmdb->getResponsiveBackdrop($tmdbMovie);
                    }
                }
                return $filme;
            });
        
        // SEÇÕES DASHBOARD
        $recentes = Filme::doUsuario()
            ->orderBy('created_at','desc')
            ->take(10)
            ->get();

        $topNotas = Filme::doUsuario()
            ->orderBy('nota','desc')
            ->take(10)
            ->get();

        $melhoresAno = Filme::doUsuario()
            ->whereYear('data_assistida', now()->year)
            ->orderBy('nota', 'desc')
            ->take(10)
            ->get();

        $assistidosRecentemente = Filme::doUsuario()
            ->whereNotNull('data_assistida')
            ->orderBy('data_assistida', 'desc')
            ->take(10)
            ->get();

        $pioresAno = Filme::doUsuario()
            ->orderBy('nota', 'asc')
            ->take(10)
            ->get();

        $ultimoMes = Filme::doUsuario()
            ->where('created_at', '>=', now()->subMonth())
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = $bibliotecaService->getDashboardStats(auth()->id());

        return view('pages.dashboard',compact(
            'recentes',
            'topNotas',
            'melhoresAno',
            'assistidosRecentemente',
            'destaques',
            'pioresAno',
            'ultimoMes',
            'totalFilmes',
            'totalAssistidos',
            'mediaNotas',
            'totalFavoritos',
            'stats'
        ));
    }

    
}
