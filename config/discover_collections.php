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

        // --- 2. Gêneros do Cinema ---
        'acao' => [
            'slug' => 'acao',
            'title' => 'Ação',
            'subtitle' => 'Adrenalina pura, perseguições e combates eletrizantes',
            'type' => 'genre',
            'icon' => '⚔️',
            'params' => [
                'genre_id' => 28,
            ],
        ],

        'comedia' => [
            'slug' => 'comedia',
            'title' => 'Comédia',
            'subtitle' => 'Dose diária de bom humor, risadas e diversão',
            'type' => 'genre',
            'icon' => '😂',
            'params' => [
                'genre_id' => 35,
            ],
        ],

        'terror' => [
            'slug' => 'terror',
            'title' => 'Terror',
            'subtitle' => 'Sustos, tensão psicológica e mistérios sombrios',
            'type' => 'genre',
            'icon' => '👻',
            'params' => [
                'genre_id' => 27,
            ],
        ],

        'ficcao-cientifica' => [
            'slug' => 'ficcao-cientifica',
            'title' => 'Ficção Científica',
            'subtitle' => 'Viagens espaciais, futuros distópicos e tecnologia avançada',
            'type' => 'genre',
            'icon' => '🚀',
            'params' => [
                'genre_id' => 878,
            ],
        ],

        'romance' => [
            'slug' => 'romance',
            'title' => 'Romance',
            'subtitle' => 'Histórias de amor, encontros marcantes e emoção',
            'type' => 'genre',
            'icon' => '❤️',
            'params' => [
                'genre_id' => 10749,
            ],
        ],

        'drama' => [
            'slug' => 'drama',
            'title' => 'Drama',
            'subtitle' => 'Narrativas profundas sobre a complexidade humana',
            'type' => 'genre',
            'icon' => '🎭',
            'params' => [
                'genre_id' => 18,
            ],
        ],

        'misterio' => [
            'slug' => 'misterio',
            'title' => 'Mistério',
            'subtitle' => 'Casos intrigantes e segredos que exigem decifração',
            'type' => 'genre',
            'icon' => '🕵️',
            'params' => [
                'genre_id' => 9648,
            ],
        ],

        'animacao' => [
            'slug' => 'animacao',
            'title' => 'Animação',
            'subtitle' => 'Arte visual marcante e fantasia para todas as idades',
            'type' => 'genre',
            'icon' => '🎨',
            'params' => [
                'genre_id' => 16,
            ],
        ],

        // --- 3. Filtros Especiais e Descobertas ---
        'classicos' => [
            'slug' => 'classicos',
            'title' => 'Clássicos do Cinema',
            'subtitle' => 'Obras-primas influentes lançadas até 1980',
            'type' => 'discover',
            'icon' => '💎',
            'params' => [
                'primary_release_date.lte' => '1980-01-01',
                'vote_count.gte' => 500,
                'sort_by' => 'vote_average.desc',
            ],
        ],

        'anos-90' => [
            'slug' => 'anos-90',
            'title' => 'Clássicos dos Anos 90',
            'subtitle' => 'A era de ouro das locadoras de VHS e Blockbuster',
            'type' => 'discover',
            'icon' => '📼',
            'params' => [
                'primary_release_date.gte' => '1990-01-01',
                'primary_release_date.lte' => '1999-12-31',
                'sort_by' => 'popularity.desc',
            ],
        ],

        'cinema-internacional' => [
            'slug' => 'cinema-internacional',
            'title' => 'Cinema Internacional',
            'subtitle' => 'A riqueza cultural de grandes produções fora do eixo de Hollywood',
            'type' => 'discover',
            'icon' => '🌎',
            'params' => [
                'without_original_language' => 'en',
                'sort_by' => 'popularity.desc',
                'vote_count.gte' => 200,
            ],
        ],

        'curtidos-pela-critica' => [
            'slug' => 'curtidos-pela-critica',
            'title' => 'Curtidos pela Crítica',
            'subtitle' => 'Filmes aclamados com média de avaliação superior a 8.0',
            'type' => 'discover',
            'icon' => '🏆',
            'params' => [
                'vote_average.gte' => 8.0,
                'vote_count.gte' => 1000,
                'sort_by' => 'vote_average.desc',
            ],
        ],

        'blockbusters' => [
            'slug' => 'blockbusters',
            'title' => 'Blockbusters',
            'subtitle' => 'Os maiores recordistas de bilheteria e arrecadação do cinema',
            'type' => 'discover',
            'icon' => '💥',
            'params' => [
                'sort_by' => 'revenue.desc',
            ],
        ],

        'joias-escondidas' => [
            'slug' => 'joias-escondidas',
            'title' => 'Joias Escondidas',
            'subtitle' => 'Filmes altamente aclamados mas pouco conhecidos do público geral',
            'type' => 'discover',
            'icon' => '💎',
            'params' => [
                'vote_average.gte' => 7.6,
                'vote_count.gte' => 100,
                'vote_count.lte' => 1200,
                'sort_by' => 'vote_average.desc',
            ],
        ],
    ],
];
