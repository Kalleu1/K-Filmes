@foreach($movies as $movie)
    <div class="discover-movie-item" style="opacity: 0; transform: translateY(20px); animation: fadeInUp 0.6s cubic-bezier(0.25, 1, 0.5, 1) forwards;">
        <x-filmecard :filme="$movie" :campos="['poster']" />
    </div>
@endforeach
