@extends('layouts.app')

@section('content')
    <div class="dashboard" style="background-image: url('{{ asset($destaques) }}');">

            {{-- Barra lateral --}}
            <x-sidebar 
                :totalFilmes="$totalFilmes"
                :totalFavoritos="$totalFavoritos"
                :totalAssistidos="$totalAssistidos"
                :mediaNotas="$mediaNotas"
            />
             

        <div class ="dashboard_main">
        
            {{-- Banner --}}       
            <section class="dashboard-hero">
                @foreach($destaques as $index => $filme)
                    <div class="hero-slide {{ $index === 0 ? 'active' : '' }}">
                        <img src="{{ $filme->poster_banner_url }}"
                        alt="Banner de {{ $filme->nome }}" 
                        class="hero-poster">

                        <div class="hero-info">
                            <h2>{{ $filme->nome }}</h2>
                            <p>⭐ Nota: {{ $filme->nota }}/10</p>
                            <p>{{ $filme->genero }} | {{ $filme->diretor }}</p>
                        </div>
                    </div>
                @endforeach
            </section>

            {{-- Filmes Adicionado Recentemente --}}

            {{-- Filmes Recentes --}}
            <section class="dashboard_section">
                <h3 class="dashboard_section_title">👁️ Filmes Vistos Recentemente</h3>
                
                <div class="dashboard_grid">
                    @foreach ($assistidosRecentemente as $filme)
                        <x-filmecard :filme="$filme" :campos="['poster']" />
                    @endforeach
                </div>
            </section>


            {{-- Filmes Maiores Notas --}}
            <section class="dashboard_section">
                <h3 class="dashboard_section_title">🏆 Maiores Notas do Ano</h3>
                
                <div class="dashboard_grid">
                    @foreach ($melhoresAno as $filme)
                        <x-filmecard :filme="$filme" :campos="['poster']" />
                    @endforeach
                </div>
            </section>

    
            

            <section class="dashboard_section">
                <h3 class="dashboard_section_title">Em Alta na Semana</h3>
                
                <div class="dashboard_grid">
                    @foreach ($topRated as $filme)
                        <x-filmecard :filme="$filme" :campos="['poster']" />
                    @endforeach
                </div>
            </section>

            <section class="dashboard_section">
                <h3 class="dashboard_section_title">Próximos Lançamentos</h3>

                    <div class="dashboard_grid">
                        @foreach ($upcoming as $filme)
                            <x-filmecard :filme="$filme" :campos="['poster']" />
                        @endforeach
                    </div>
            </section>
        </div>

    </div>

@endsection

<script>
document.addEventListener("DOMContentLoaded", function () {
    const slides = document.querySelectorAll(".hero-slide");
    let currentIndex = 0;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.toggle("active", i === index);
        });
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % slides.length;
        showSlide(currentIndex);
    }

    // Troca a cada 30 segundos (30000ms)
    setInterval(nextSlide, 30000);
});
</script>