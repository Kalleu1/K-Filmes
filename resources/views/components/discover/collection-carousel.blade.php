@props([
    'movies' => []
])

<div class="collection-carousel-wrapper">
    <button class="carousel-control prev" aria-label="Anterior" type="button">
        <i class="fa-solid fa-chevron-left"></i>
    </button>
    
    <div class="collection-carousel">
        <div class="carousel-track">
            @forelse($movies as $movie)
                <div class="carousel-item">
                    <x-filmecard :filme="$movie" :campos="['poster']" />
                </div>
            @empty
                <div class="carousel-empty">
                    <p>Nenhum filme disponível nesta coleção</p>
                </div>
            @endforelse
        </div>
    </div>
    
    <button class="carousel-control next" aria-label="Próximo" type="button">
        <i class="fa-solid fa-chevron-right"></i>
    </button>
</div>
