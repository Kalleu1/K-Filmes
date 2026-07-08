<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Coleções da Página Descobrir
    |--------------------------------------------------------------------------
    |
    | Define todas as coleções de filmes exibidas no painel de descoberta.
    | Adicionar novas coleções (editoriais, franquias, filtros) requer apenas
    | cadastrar um novo item neste array associativo.
    |
    | Tipos suportados ('type'):
    | - 'trending': Filmes em alta na semana ou dia (params: 'time_window').
    | - 'popular': Filmes mais populares no momento.
    | - 'top_rated': Filmes mais bem avaliados globalmente.
    | - 'now_playing': Filmes em exibição atualmente (BR).
    | - 'upcoming': Próximos lançamentos programados (BR).
    | - 'genre': Filmes filtrados por ID de gênero (params: 'genre_id').
    | - 'discover': Filmes descobertos com parâmetros flexíveis da API do TMDB.
    |
    */

    'collections' => [
        'em-alta' => [
            'slug' => 'em-alta',
            'title' => 'Em Alta',
            'subtitle' => 'Os títulos mais procurados e discutidos da semana',
            'type' => 'trending',
            'icon' => '🔥',
            'params' => [
                'time_window' => 'week',
            ],
        ],

        'populares' => [
            'slug' => 'populares',
            'title' => 'Populares',
            'subtitle' => 'Os filmes mais assistidos pela comunidade',
            'type' => 'popular',
            'icon' => '⭐',
            'params' => [],
        ],

        'mais-votados' => [
            'slug' => 'mais-votados',
            'title' => 'Mais Votados',
            'subtitle' => 'Grandes sucessos de bilheteria e aclamados pela crítica',
            'type' => 'top_rated',
            'icon' => '🏆',
            'params' => [],
        ],

        'em-cartaz' => [
            'slug' => 'em-cartaz',
            'title' => 'Em Cartaz',
            'subtitle' => 'Filmes exibidos atualmente nos cinemas',
            'type' => 'now_playing',
            'icon' => '🎬',
            'params' => [],
        ],

        'proximos-lancamentos' => [
            'slug' => 'proximos-lancamentos',
            'title' => 'Próximos Lançamentos',
            'subtitle' => 'Estreias aguardadas que chegarão em breve',
            'type' => 'upcoming',
            'icon' => '📅',
            'params' => [],
        ],
    ],
];
