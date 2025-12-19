<?php

namespace App\Http\Controllers;

use App\Models\Filme;
use App\Services\TMDBService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(TMDBService $tmdb){
        //ESTATISTICAS RAPIDAS
        $totalFilmes = Filme::count();
        $totalAssistidos = Filme::where('assistido', true)->count();
        $mediaNotas = number_format(Filme::avg('nota'), 2, '.', '.');
        $totalFavoritos = Filme::where('favorito', true)->count();
        
        //BANNER
        $destaques = Filme::inRandomOrder()->take(6)->get();
        
        // SEÇÕES DASHBOARD
        $recentes = Filme::orderBy('created_at','desc')->take(7)->get();
        $topNotas = Filme::orderBy('nota','desc')->take(7)->get();
        $melhoresAno = Filme::whereYear('data_assistida', now()->year) ->orderBy('nota', 'desc')->take(7) ->get();
        $assistidosRecentemente = Filme::whereNotNull('data_assistida')->orderBy('data_assistida', 'desc')->take(7)->get();
        $pioresAno = Filme::orderBy('nota', 'asc')->take(7)->get();
        $ultimoMes = Filme::where('created_at', '>=', now()->subMonth())->orderBy('created_at', 'desc')->get();
        $filmesGenero = Filme::filtrarPor('genero', 'Ação', 7); 
        $filmesDiretor = Filme::filtrarPor('diretor', 'Christopher Nolan', 7, 'desc', 'nota'); 
        $filmesPlataforma = Filme::filtrarPor('plataforma', 'Netflix', 7); 
        
        //API SEÇÃO DASHBOARD (com cache das listas normalizadas)
        $emCartaz = Cache::remember('dashboard:tmdb:now_playing:pt-BR', 30 * 60, fn() => $tmdb->normalizeMovies($tmdb->getNowPlaying()));
        $trending = Cache::remember('dashboard:tmdb:trending:week:pt-BR', 60 * 60, fn() => $tmdb->normalizeMovies($tmdb->getTrending()));
        $topRated = Cache::remember('dashboard:tmdb:top_rated:pt-BR', 360 * 60, fn() => $tmdb->normalizeMovies($tmdb->getTopRated()));
        $upcoming = Cache::remember('dashboard:tmdb:upcoming:pt-BR', 720 * 60, fn() => $tmdb->normalizeMovies($tmdb->getUpcoming()));

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
            'filmesGenero',
            'filmesDiretor',
            'filmesPlataforma',
            'emCartaz',
            'trending',
            'topRated',
            'upcoming',
            
        ));
    }

    
}
