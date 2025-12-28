@props([
    'totalFilmes' => 0,
    'totalFavoritos' => 0,
    'totalAssistidos' => 0,
    'mediaNotas' => 0
])

<aside id="dashboard-sidebar" class="dashboard_sidebar" aria-label="Menu lateral de navegação">

            {{-- Header do Sidebar --}}
            <div class="sidebar-header">
                <header class="sidebar-logo">
                    <div class="logo-icon" aria-hidden="true">🎬</div>
                    <div class="logo-text">
                        <h1>K-Filmes</h1>
                        <p>Sua biblioteca de filmes</p>
                    </div>
                </header>
            </div>

                
            {{-- Estatísticas rápidas --}}
            <div class="sidebar-stats" aria-label="Estatísticas rápidas do usuário">
                <div class="stat-card stat-primary" role="region" aria-label="Total de filmes">
                    <div class="stat-content">
                        <div class="stat-info">
                            <span class="stat-label">Total Filmes</span>
                            <span class="stat-value">{{ $totalFilmes}}</span>
                        </div>
                        <div class="stat-icon" aria-hidden="true">🎭</div>
                    </div>
                </div>
                
                <div class="stat-card stat-secondary" role="region" aria-label="Total de favoritos">
                    <div class="stat-content">
                        <div class="stat-info">
                            <span class="stat-label">Favoritos</span>
                            <span class="stat-value">{{ $totalFavoritos}}</span>
                        </div>
                        <div class="stat-icon" aria-hidden="true">❤️</div>
                    </div>
                </div>
                
                <div class="stat-card stat-success" role="region" aria-label="Total de filmes assistidos">
                    <div class="stat-content">
                        <div class="stat-info">
                            <span class="stat-label">Assistidos</span>
                            <span class="stat-value">{{ $totalAssistidos }}</span>
                        </div>
                        <div class="stat-icon" aria-hidden="true">✅</div>
                    </div>
                </div>

                <div class="stat-card stat-warning">
                    <div class="stat-content">
                        <div class="stat-info">
                            <span class="stat-label">Média</span>
                            <span class="stat-value">{{ $mediaNotas }}</span>
                        </div>
                        <div class="stat-icon">⭐</div>
                    </div>
                </div>
                
            </div>    

                
                

            {{-- Menu de Navegação --}}
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <h3 class="nav-section-title">Ações Principais</h3>
                    <ul class="nav-list">

                        <li class="nav-item">
                            <a href="{{ route('filmes.busca') }}" class="nav-link nav-link-search">
                                <div class="nav-icon">🔍</div>
                                <span class="nav-text">Buscar Filmes</span>
                                <div class="nav-glow"></div>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="{{ route('filmes.biblioteca') }}" class="nav-link nav-link-tertiary">
                                <div class="nav-icon">✨</div>
                                <span class="nav-text">Biblioteca</span>
                                <div class="nav-glow"></div>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('filmes.filme-do-dia') }}" class="nav-link nav-link-tertiary">
                                <div class="nav-icon">🎲</div>
                                <span class="nav-text">Sortear Filme</span>
                                <div class="nav-glow"></div>
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- <div class="nav-section">
                    <h3 class="nav-section-title">Descobrir</h3>
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="" class="nav-link nav-link-discover">
                                <div class="nav-icon">📈</div>
                                <span class="nav-text">Em Alta</span>
                                <span class="nav-badge">12</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="" class="nav-link nav-link-discover">
                                <div class="nav-icon">🏆</div>
                                <span class="nav-text">Premiados</span>
                                <span class="nav-badge">8</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="" class="nav-link nav-link-discover">
                                <div class="nav-icon">⭐</div>
                                <span class="nav-text">Top Rated</span>
                                <span class="nav-badge">25</span>
                            </a>
                        </li>
                    </ul>
                </div> --}}
            </nav>
        </aside>