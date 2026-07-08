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
        // --- 1. Destaques Gerais ---
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

        'os-melhores-filmes' => [
            'slug' => 'os-melhores-filmes',
            'title' => 'Os Melhores Filmes',
            'subtitle' => 'As maiores obras do cinema, ordenadas pela avaliação da comunidade',
            'type' => 'discover',
            'icon' => '🥇',
            'params' => [
                'sort_by' => 'vote_average.desc',
                'vote_count.gte' => 5000,
            ],
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

        'adrenalina-maxima' => [
            'slug' => 'adrenalina-maxima',
            'title' => 'Adrenalina Máxima',
            'subtitle' => 'Explosões, perseguições e cenas de ação do começo ao fim',
            'type' => 'discover',
            'icon' => '💥',
            'params' => [
                'with_genres' => '28,12',
                'sort_by' => 'popularity.desc',
                'vote_count.gte' => 500,
    ],
        ],



        

        'prepare-os-lencos' => [
            'slug' => 'prepare-os-lencos',
            'title' => 'Prepare os Lenços',
            'subtitle' => 'Emoção, superação e histórias que tocam o coração',
            'type' => 'discover',
            'icon' => '😭',
            'params' => [
                'with_genres' => '18,10749',
                'vote_average.gte' => 7.0,
                'vote_count.gte' => 500,
            ],
        ],

        'para-assistir-a-noite' => [
            'slug' => 'para-assistir-a-noite',
            'title' => 'Para Assistir à Noite',
            'subtitle' => 'Suspense, mistério e terror para uma sessão inesquecível',
            'type' => 'discover',
            'icon' => '🌙',
            'params' => [
                'with_genres' => '27,53,9648',
                'sort_by' => 'popularity.desc',
            ],
        ],

        'humor-garantido' => [
            'slug' => 'humor-garantido',
            'title' => 'Humor Garantido',
            'subtitle' => 'Comédias para relaxar e dar boas risadas',
            'type' => 'discover',
            'icon' => '😂',
            'params' => [
                'with_genres' => '35',
                'sort_by' => 'popularity.desc',
                'vote_count.gte' => 500,
            ],
        ],

        'para-ver-em-familia' => [
            'slug' => 'para-ver-em-familia',
            'title' => 'Para Ver em Família',
            'subtitle' => 'Filmes para reunir todos no sofá',
            'type' => 'discover',
            'icon' => '👨‍👩‍👧',
            'params' => [
                'with_genres' => '10751,16',
                'sort_by' => 'popularity.desc',
            ],
        ],

        'joias-escondidas' => [
            'slug' => 'joias-escondidas',
            'title' => 'Joias Escondidas',
            'subtitle' => 'Filmes excelentes que merecem muito mais reconhecimento',
            'type' => 'discover',
            'icon' => '💎',
            'params' => [
                'vote_average.gte' => 7.6,
                'vote_count.gte' => 100,
                'vote_count.lte' => 1200,
                'sort_by' => 'vote_average.desc',
            ],
        ],

        'curtidos-pela-critica' => [
            'slug' => 'curtidos-pela-critica',
            'title' => 'Curtidos pela Crítica',
            'subtitle' => 'Produções aclamadas por críticos e pelo público',
            'type' => 'discover',
            'icon' => '🏆',
            'params' => [
                'vote_average.gte' => 8,
                'vote_count.gte' => 3000,
                'sort_by' => 'vote_average.desc',
            ],
        ],

        'classicos-atemporais' => [
            'slug' => 'classicos-atemporais',
            'title' => 'Clássicos Atemporais',
            'subtitle' => 'Obras que marcaram gerações e continuam inesquecíveis',
            'type' => 'discover',
            'icon' => '🎞️',
            'params' => [
                'primary_release_date.lte' => '1985-12-31',
                'vote_average.gte' => 7,
                'vote_count.gte' => 1000,
                'sort_by' => 'vote_average.desc',
            ],
        ],

        'nostalgia-anos-90' => [
            'slug' => 'nostalgia-anos-90',
            'title' => 'Nostalgia Anos 90',
            'subtitle' => 'Os sucessos que marcaram uma geração',
            'type' => 'discover',
            'icon' => '📼',
            'params' => [
                'primary_release_date.gte' => '1990-01-01',
                'primary_release_date.lte' => '1999-12-31',
                'vote_count.gte' => 500,
                'sort_by' => 'popularity.desc',
            ],
        ],

        'cinema-pelo-mundo' => [
            'slug' => 'cinema-pelo-mundo',
            'title' => 'Cinema pelo Mundo',
            'subtitle' => 'Descubra grandes histórias produzidas fora de Hollywood',
            'type' => 'discover',
            'icon' => '🌍',
            'params' => [
                'without_original_language' => 'en',
                'vote_count.gte' => 300,
                'sort_by' => 'popularity.desc',
            ],
        ],
        
    ],
];
