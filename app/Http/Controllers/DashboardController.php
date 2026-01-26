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
            ->get();
        
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
            'emCartaz',
            'trending',
            'topRated',
            'upcoming',
            
        ));
    }

    
}
