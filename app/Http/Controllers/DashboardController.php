<?php

namespace App\Http\Controllers;

use App\Models\Filme;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        $recentes = Filme::orderBy('created_at','desc')->take(6)->get();

        $topNotas = Filme::orderBy('nota','desc')->take(6)->get();

        $destaques = Filme::inRandomOrder()->take(5)->get();

        return view('pages.dashboard',compact('recentes','topNotas','destaques'));
    }
}
