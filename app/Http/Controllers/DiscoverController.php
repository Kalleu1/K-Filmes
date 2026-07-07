<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DiscoverController extends Controller
{
    /**
     * Exibe a página principal de descoberta.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Coleção 1: Ficção Científica
        $scifiMovies = [
            (object)[
                'tmdb_id' => 27205,
                'nome' => 'A Origem',
                'poster_url' => 'https://image.tmdb.org/t/p/w342/9uoG6o5861TcvVn5L6W48nUkiH2.jpg',
                'ano' => 2010
            ],
            (object)[
                'tmdb_id' => 157336,
                'nome' => 'Interestelar',
                'poster_url' => 'https://image.tmdb.org/t/p/w342/gEU2QniE6E77NI6lCU20xtJjFGy.jpg',
                'ano' => 2014
            ],
            (object)[
                'tmdb_id' => 271110,
                'nome' => 'Capitão América: Guerra Civil',
                'poster_url' => 'https://image.tmdb.org/t/p/w342/8S6714u2T46197qyw5G6nF4U1fC.jpg',
                'ano' => 2016
            ],
            (object)[
                'tmdb_id' => 19995,
                'nome' => 'Avatar',
                'poster_url' => 'https://image.tmdb.org/t/p/w342/i5R4q5hUfR0vNWhB6zD10tS7m1t.jpg',
                'ano' => 2009
            ],
            (object)[
                'tmdb_id' => 299536,
                'nome' => 'Vingadores: Guerra Infinita',
                'poster_url' => 'https://image.tmdb.org/t/p/w342/2H76J1L2l2l2JbW3b72p2h2H6j.jpg',
                'ano' => 2018
            ]
        ];

        // Coleção 2: Clássicos do Cinema
        $classicsMovies = [
            (object)[
                'tmdb_id' => 278,
                'nome' => 'Um Sonho de Liberdade',
                'poster_url' => 'https://image.tmdb.org/t/p/w342/q6y05II4gU4wtrtr2t7F04o9eQQ.jpg',
                'ano' => 1994
            ],
            (object)[
                'tmdb_id' => 238,
                'nome' => 'O Poderoso Chefão',
                'poster_url' => 'https://image.tmdb.org/t/p/w342/3bhkrj68VMMEbDzmg92GRWCcx64.jpg',
                'ano' => 1972
            ],
            (object)[
                'tmdb_id' => 122,
                'nome' => 'O Senhor dos Anéis: O Retorno do Rei',
                'poster_url' => 'https://image.tmdb.org/t/p/w342/87h2mq27f4pWf5Y3j79H6KzV1xZ.jpg',
                'ano' => 2003
            ],
            (object)[
                'tmdb_id' => 680,
                'nome' => 'Pulp Fiction',
                'poster_url' => 'https://image.tmdb.org/t/p/w342/d5iIlv8jJgZj3rjRpfent66gOci.jpg',
                'ano' => 1994
            ]
        ];

        return view('discover.index', compact('scifiMovies', 'classicsMovies'));
    }

    /**
     * Exibe uma coleção específica da página de descoberta.
     *
     * @param string|int $id
     * @return \Illuminate\View\View
     */
    public function collection($id)
    {
        return view('discover.collection', compact('id'));
    }
}
