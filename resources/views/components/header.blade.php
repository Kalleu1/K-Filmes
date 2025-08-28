<header class="header">
    <div class="header-left">
        <a href="{{ route('filmes.index') }}"> <img src="{{asset('/imgs/logo_K-Notas.png')}}" alt="Logo nao encontrada"> </a>
    </div>

    <div class="header-center">
        
    </div>

    <div class="header-right">

        <div class="header-config">
            {{ svg('iconpark-config', ['id' => 'user-config']) }}
        </div>

        <div class="header-picture">
            <button class="user-picture"> K</button>
        </div>
    </div>
</header>