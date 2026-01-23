<?php

namespace App\Http\Controllers;

use App\Models\Filme;
use App\Services\ColorThemeService;
use App\Services\TMDBService;
use App\Support\Toast\ToastMessages;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class FilmeController extends Controller
{

    protected $tmdb;

    public function __construct(TMDBService $tmdb)
    {
        $this->tmdb = $tmdb;
    }
    /**
     * Display a listing of the resource.
     */
        public function index()
        {
            

            $filmes = Filme::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();

            return view('filmes.index', compact('filmes'));
        }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, TMDBService $tmdb)
    {
        $tmdbData = null;

        $tmdbId = $request->query('tmdb_id'); 
        

        if ($request->has('tmdb_id')) {
            $tmdbData = $tmdb->getMovie($request->tmdb_id);
        }

        return view('filmes.create', compact('tmdbData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $data = $request->validate([
        'nome' => 'required|string|max:255',
        'descricao' => 'nullable|string',
        'plataforma' => 'nullable|string',
        'data_assistida' => 'nullable|date',
        'diretor' => 'nullable|string|max:255',
        'genero' => 'nullable|string|max:255',
        'nota' => 'nullable|numeric|min:0|max:10',
        'comentarios' => 'nullable|string',
        'poster' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'poster_banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

        if($request->hasFile('poster')){
            $file = $request->file('poster');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->storeAs('posters', $filename, 'public'); 
            $data['poster'] = $filename;
        }

        if($request->hasFile('poster_banner')){
            $file = $request->file('poster_banner');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->storeAs('posters_banners', $filename, 'public'); 
            $data['poster_banner'] = $filename;
        }

        $data['user_id'] = Auth::id();

        Filme::create($data);

        return redirect()->route('dashboard')->with(ToastMessages::movieAdded());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, ColorThemeService $colorTheme)
{
    
    $filme = Filme::where('id', $id)
    ->where('user_id', Auth::id())
    ->firstOrFail();


    $tmdbData = null;
    $director = null;
    $genres = null;
    $posterUrl = null;
    $backdropUrl = null;
    $similarMovies = [];
    $directorMovies = [];

    // Se o filme tem tmdb_id, busca os dados do TMDB
    if ($filme->tmdb_id) {
        $tmdbData = $this->tmdb->getMovie((int) $filme->tmdb_id);

        if ($tmdbData) {
            
            //rating
            

            // Diretor
            if (!empty($tmdbData['credits']['crew'])) {
                foreach ($tmdbData['credits']['crew'] as $crew) {
                    if (isset($crew['job']) && strtolower($crew['job']) === 'director') {
                        $director = $crew['name'];
                        break;
                    }
                }
            }
            // Gêneros
            $genres = !empty($tmdbData['genres']) ? implode(', ', array_column($tmdbData['genres'], 'name')) : null;
            // Poster e Banner
            $posterUrl   = $this->tmdb->getImageUrl($tmdbData['poster_path'] ?? null, 'w500');
            $backdropUrl = $this->tmdb->getImageUrl($tmdbData['backdrop_path']?? null, 'w1280');

            $localBackdropPath = null;

            if (!empty($tmdbData['backdrop_path']) && $filme->tmdb_id) {
                $localBackdropPath = $this->tmdb->ensureImageSaved(
                    $tmdbData['backdrop_path'],
                    'w1280',
                    (int) $filme->tmdb_id
                );
            }

            // Filmes similares
            $similarMovies = $this->tmdb->getSimilarMovies((int) $filme->tmdb_id);
            // Filmes do mesmo diretor
            if ($director) {
                $directorMovies = $this->tmdb->getMoviesByDirector($director, 7);
            }

            $tmdbRating = isset($tmdbData['vote_average']) ? round($tmdbData['vote_average'], 1) : null;
            
        }
    }

            $colorThemeData = null;

            if ($filme->assistido && !empty($localBackdropPath)) {
                $colorThemeData = $colorTheme->extract($localBackdropPath);

                if (file_exists($localBackdropPath)) {
                    @unlink($localBackdropPath);
                }
            }






    // Escolhe a view com base no status do filme
    if ($filme->assistido) {
        return view('filmes.show', compact(
            'filme', 'tmdbData', 'director', 'genres', 'posterUrl', 'backdropUrl', 'similarMovies', 'directorMovies','tmdbRating','colorThemeData'
        ));
    } else {
        return view('filmes.show_tmdb', compact(
            'filme', 'tmdbData', 'director', 'genres', 'posterUrl', 'backdropUrl', 'similarMovies', 'directorMovies','tmdbRating'
        ));
    }
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Filme $filme)
    {
        $this->authorize('update', $filme);
        return view('filmes.edit', compact('filme'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Filme $filme)
    {
        $this->authorize('update', $filme);
    
        $data = $request->validate([
        
        'plataforma' => 'nullable|string',
        'data_assistida' => 'nullable|date',
        'nota' => 'nullable|numeric|min:0|max:10',
        'comentarios' => 'nullable|string',
        
    ]);

    $filme->update($data);

    return back()->with(ToastMessages::movieUpdated());
    }

    
    public function destroy(Filme $filme)
    {
        $this->authorize('delete', $filme);

    if($filme->poster) {
        Storage::disk('public')->delete($filme->poster);
    }
    if($filme->poster_banner) {
        Storage::disk('public')->delete($filme->poster_banner);
    }
        $filme->delete();
        return redirect()->route('filmes.biblioteca')
            ->with(ToastMessages::movieDeleted());
    }

    public function biblioteca(Request $request)
    {
        $query = Filme::where('user_id', Auth::id());

        if ($request->has('assistido') && $request->assistido !== '') {
            $query->where('assistido', $request->boolean('assistido'));
        }

        if ($request->has('favorito') && $request->favorito !== '') {
            $query->where('favorito', $request->boolean('favorito'));
        }

        if ($request->filled('genero')) {
            $query->where('genero', 'LIKE', '%' . $request->genero . '%');
        }

        // Adicionado: filtro por diretor
        if ($request->filled('diretor')) {
            $query->where('diretor', 'LIKE', '%' . $request->diretor . '%');
        }

        if ($request->filled('ano_lancamento')) {
            $query->where('ano_lancamento', $request->ano_lancamento);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('nome', 'LIKE', "%{$q}%")
                    ->orWhere('diretor', 'LIKE', "%{$q}%")
                    ->orWhere('descricao', 'LIKE', "%{$q}%");
            });
        }

        $filmes = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('filmes.biblioteca', compact('filmes'));
    }

    public function buscarBiblioteca(Request $request)
    {
        $query = $request->input('q');

        $filmes = Filme::where('user_id', Auth::id())
            ->when($query, function ($qBuilder) use ($query) {
                $qBuilder->where(function ($sub) use ($query) {
                    $sub->where('nome', 'like', "%{$query}%")
                        ->orWhere('diretor', 'like', "%{$query}%")
                        ->orWhere('genero', 'like', "%{$query}%")
                        ->orWhere('ano_lancamento', 'like', "%{$query}%");
                });
            })

            ->orderBy('nome')
            ->paginate(20)
            ->withQueryString();


        return view('filmes.biblioteca', compact('filmes', 'query'));
    }

        public function toggleFavorito(Request $request, $id)
        {
           $filme = Filme::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();


            // Atualiza o campo favorito com base no que veio no request
            $filme->favorito = $request->input('favorito') ? 1 : 0;
            $filme->save();

            return response()->json([
                'success' => true,
                'favorito' => $filme->favorito
            ]);
        }








    public function search(Request $request, TMDBService $tmdb)
{
    $q = $request->input('q');
    if (!$q) {
        return response()->json(['results' => []]);
    }

    $res = $tmdb->searchMovies($q, 1);
    $results = [];

    if ($res && isset($res['results'])) {
        foreach ($res['results'] as $r) {
            $results[] = [
                'tmdb_id'      => $r['id'],
                'nome'         => $r['title'] ?? ($r['name'] ?? 'Sem título'),
                'release_date' => $r['release_date'] ?? null,
                'descricao'    => $r['overview'] ?? null,
                'poster_url'   => $tmdb->getImageUrl($r['poster_path'] ?? null, 'w500'),
            ];
        }
    }

    return response()->json(['results' => $results]);
}


    public function showTmdb($tmdb_id)
    {
        $tmdbData = $this->tmdb->getMovie((int) $tmdb_id);

        if (!$tmdbData) {
            abort(404, 'Filme não encontrado na TMDB.');
        }

        $director = null;
        if (!empty($tmdbData['credits']['crew'])) {
            foreach ($tmdbData['credits']['crew'] as $crew) {
                if (isset($crew['job']) && strtolower($crew['job']) === 'director') {
                    $director = $crew['name'];
                    break;
                }
            }
        }

        $genres = !empty($tmdbData['genres']) ? implode(', ', array_column($tmdbData['genres'], 'name')) : null;

        $posterUrl   = $this->tmdb->getImageUrl($tmdbData['poster_path'] ?? null, 'w500');
        $backdropUrl = $this->tmdb->getImageUrl($tmdbData['backdrop_path'] ?? null, 'w1280');

        $filme = Filme::where('tmdb_id', $tmdb_id)
            ->where('user_id', Auth::id())
            ->first();
        $similarMovies = $this->tmdb->getSimilarMovies((int) $tmdb_id);
        if ($director) {
                $directorMovies = $this->tmdb->getMoviesByDirector($director, 7);
            }
        $tmdbRating = isset($tmdbData['vote_average']) ? round($tmdbData['vote_average'], 1) : null;
            

        return view('filmes.show_tmdb', compact(
            'tmdbData', 'director', 'genres', 'posterUrl', 'backdropUrl', 'filme', 'tmdb_id', 'similarMovies','tmdbRating'
        ));
    }

    // Salvar/atualizar no banco
    public function saveTmdb(Request $request, $tmdb_id)
    {
        $validated = $request->validate([
            'nota' => 'nullable|numeric|min:0|max:10',
            'comentarios' => 'nullable|string|max:5000',
            'data_assistida' => 'nullable|date',
            'plataforma' => 'nullable|string|max:255',
            'assistido' => 'nullable|boolean',
        ]);

        $data['assistido'] = $request->input('assistido', 0) ? 1 : 0;


        $tmdbData = $this->tmdb->getMovie((int) $tmdb_id);

        if (!$tmdbData) {
            return back()->with(ToastMessages::tmdbUnavailable());
        }

        $director = null;
        if (!empty($tmdbData['credits']['crew'])) {
            foreach ($tmdbData['credits']['crew'] as $crew) {
                if (isset($crew['job']) && strtolower($crew['job']) === 'director') {
                    $director = $crew['name'];
                    break;
                }
            }
        }

        $genres = !empty($tmdbData['genres']) ? implode(', ', array_column($tmdbData['genres'], 'name')) : null;

        $posterUrl   = $this->tmdb->getImageUrl($tmdbData['poster_path'] ?? null, 'w500');
        $backdropUrl = $this->tmdb->getImageUrl($tmdbData['backdrop_path'] ?? null, 'w1280');

        $filme = Filme::updateOrCreate(
        [
            'tmdb_id' => $tmdb_id,
            'user_id' => Auth::id(),
        ],
        [
            'nome' => $tmdbData['title'] ?? ($tmdbData['name'] ?? 'Sem título'),
            'descricao' => $tmdbData['overview'] ?? $request->input('descricao'),
            'diretor' => $director,
            'genero' => $genres,
            'poster' => $posterUrl,
            'poster_banner' => $backdropUrl,
            'plataforma' => $validated['plataforma'] ?? null,
            'data_assistida' => $validated['data_assistida'] ?? null,
            'nota' => $validated['nota'] ?? null,
            'comentarios' => $validated['comentarios'] ?? null,
            'ano_lancamento' => !empty($tmdbData['release_date'])
                ? date('Y', strtotime($tmdbData['release_date']))
                : null,
            'assistido' => $data['assistido'],
        ]
    );


        $toast = $filme->wasRecentlyCreated
            ? ToastMessages::movieAdded()
            : ToastMessages::movieUpdated();

        return redirect()
            ->route('filmes.showTmdb', $filme->tmdb_id)
            ->with($toast);
    }

    public function assistidos(Filme $filme)
    {

        if ($filme->user_id !== Auth::id()) {
        abort(403);
    }
        return view('filmes.show', compact('filme'));
    }


    public function naoAssistidos()
    {
        $filmes = Filme::where('user_id', Auth::id())
                ->where('assistido', false)
                        ->orderBy('nome') // opcional: ordenar por título
                        ->get();

        return view('filmes.naoAssistidos', compact('filmes'));
    }

        public function marcarAssistido(Request $request, $id)
        {
            $filme = Filme::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();


            $filme->assistido = true;
            $filme->nota = $request->input('nota');
            $filme->comentario = $request->input('comentario');
            $filme->data_visualizacao = $request->input('data_visualizacao');
            $filme->save();

            return redirect()->route('filmes.show', $filme->id)
                            ->with(ToastMessages::markedAsWatched());
        }


    // Tela inicial da busca
    public function busca()
    {
        return view('filmes.busca');
    }

    // Executa a pesquisa na TMDB
    public function buscarTmdb(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return redirect()->route('filmes.busca')->with(ToastMessages::missingData());
        }

        $page = (int) $request->get('page', 1);

        $response = $this->tmdb->searchMovies($query, $page);

        $raw = $response['results'] ?? [];
        $totalPages = $response['total_pages'] ?? 1;
        $totalResults = $response['total_results'] ?? count($raw);

        $results = collect($raw)->map(function ($r) {
            return (object) [
                'tmdb_id'      => $r['id'],
                'nome'         => $r['title'] ?? ($r['name'] ?? 'Sem título'),
                'release_date' => $r['release_date'] ?? null,
                'descricao'    => $r['overview'] ?? null,
                'poster_url'   => $this->tmdb->getImageUrl($r['poster_path'] ?? null, 'w500'),
            ];
        });

        $paginator = new LengthAwarePaginator(
            $results,
            $totalResults,
            20, 
            $page,
            [
                'path'  => route('filmes.buscarTmdb'),
                'query' => $request->query(),
            ]
        );

        return view('filmes.busca', [
            'results' => $paginator,
            'query'   => $query,
        ]);
    }

}
