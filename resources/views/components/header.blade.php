<header class="header">
    <div class="header-left">
        <a href="{{ route('dashboard') }}"> <img src="{{asset('/imgs/logo_K-Notas.png')}}" alt="Logo nao encontrada"> </a>
    </div>

    <div class="header-center">
        
    </div>

    <div class="header-right">
        @if (request()->routeIs('dashboard*'))
            <button
            class="sidebar-toggle"
            aria-label="Abrir menu"
            aria-controls="dashboard-sidebar"
        >
            ☰
        </button>
        
        @endif
        
    </div>
</header>