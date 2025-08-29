@extends('layouts.app')

@section('content')
    <div class="dashboard">

        <h1> Olá, Kalleu!(nome o usuario) 🎬</h1>
        <h2> “Aqui estão alguns filmes que você avaliou:”</h2>

        <section class="dashboard_section">
            <h3 class="dashboard_section_title">Filmes Recentes</h3>
            
            <div class="dashboard_grid">
                @foreach ($recentes as $filme)
                    <x-filmecard :filme="$filme" :campos="['nome','poster']" />
                @endforeach
            </div>

            <h3 class="dashboard_section_title">Filmes Com Maiores Notas</h3>
            
            <div class="dashboard_grid">
                @foreach ($topNotas as $filme)
                    <x-filmecard :filme="$filme" :campos="['nome','poster','nota']"/>
                @endforeach
            </div>
        </section>

        

    </div>



@endsection