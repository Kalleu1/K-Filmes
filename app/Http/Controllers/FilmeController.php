<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveTmdbFilmeRequest;
use App\Http\Requests\StoreFilmeRequest;
use App\Http\Requests\UpdateFilmeRequest;
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
            

            $filmes = $this->userFilmesQuery()
                ->orderBy('created_at', 'desc')
                ->get();

            return view('filmes.index', compact('filmes'));
        }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $tmdbData = null;

        $tmdbId = $request->query('tmdb_id'); 
        

        if ($request->has('tmdb_id')) {
            $tmdbData = $this->tmdb->getMovie($request->tmdb_id);
        }

        return view('filmes.create', compact('tmdbData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFilmeRequest $request)
    {
        $data = $request->validated();

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
    
    $filme = $this->findUserFilmeOrFail($id);


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
            $director = $this->extractDirector($tmdbData);
            // Gêneros
            $genres = $this->extractGenres($tmdbData);
            // Poster e Banner
            ['posterUrl' => $posterUrl, 'backdropUrl' => $backdropUrl] = $this->tmdbImageUrls($tmdbData);

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
    public function update(UpdateFilmeRequest $request, Filme $filme)
    {
        $this->authorize('update', $filme);
    
        $data = $request->validated();

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
        $query = $this->userFilmesQuery();

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

        $filmes = $this->userFilmesQuery()
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
           $filme = $this->findUserFilmeOrFail($id);


            // Atualiza o campo favorito com base no que veio no request
            $filme->favorito = $request->input('favorito') ? 1 : 0;
            $filme->save();

            return response()->json([
                'success' => true,
                'favorito' => $filme->favorito
            ]);
        }








    public function search(Request $request)
{
    $q = $request->input('q');
    if (!$q) {
        return response()->json(['results' => []]);
    }

    $res = $this->tmdb->searchMovies($q, 1);
    $results = [];

    if ($res && isset($res['results'])) {
        foreach ($res['results'] as $r) {
            $results[] = [
                'tmdb_id'      => $r['id'],
                'nome'         => $r['title'] ?? ($r['name'] ?? 'Sem título'),
                'release_date' => $r['release_date'] ?? null,
                'descricao'    => $r['overview'] ?? null,
                'poster_url'   => $this->tmdb->getImageUrl($r['poster_path'] ?? null, 'w500'),
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

        $director = $this->extractDirector($tmdbData);
        $genres = $this->extractGenres($tmdbData);
        ['posterUrl' => $posterUrl, 'backdropUrl' => $backdropUrl] = $this->tmdbImageUrls($tmdbData);

        $filme = $this->userFilmesQuery()
            ->where('tmdb_id', $tmdb_id)
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
    public function saveTmdb(SaveTmdbFilmeRequest $request, $tmdb_id)
    {
        $validated = $request->validated();

        $data['assistido'] = $request->input('assistido', 0) ? 1 : 0;


        $tmdbData = $this->tmdb->getMovie((int) $tmdb_id);

        if (!$tmdbData) {
            return back()->with(ToastMessages::tmdbUnavailable());
        }

        $director = $this->extractDirector($tmdbData);
        $genres = $this->extractGenres($tmdbData);
        ['posterUrl' => $posterUrl, 'backdropUrl' => $backdropUrl] = $this->tmdbImageUrls($tmdbData);

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
        $filmes = $this->userFilmesQuery()
                ->where('assistido', false)
                        ->orderBy('nome') // opcional: ordenar por título
                        ->get();

        return view('filmes.naoAssistidos', compact('filmes'));
    }

        public function marcarAssistido(Request $request, $id)
        {
            $filme = $this->findUserFilmeOrFail($id);


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

    private function userFilmesQuery()
    {
        return Filme::where('user_id', Auth::id());
    }

    private function findUserFilmeOrFail($id): Filme
    {
        return $this->userFilmesQuery()
            ->where('id', $id)
            ->firstOrFail();
    }

    private function extractDirector(?array $tmdbData): ?string
    {
        if (empty($tmdbData['credits']['crew']) || !is_array($tmdbData['credits']['crew'])) {
            return null;
        }

        foreach ($tmdbData['credits']['crew'] as $crew) {
            if (($crew['job'] ?? null) && strtolower($crew['job']) === 'director') {
                return $crew['name'] ?? null;
            }
        }

        return null;
    }

    private function extractGenres(?array $tmdbData): ?string
    {
        if (empty($tmdbData['genres']) || !is_array($tmdbData['genres'])) {
            return null;
        }

        return implode(', ', array_column($tmdbData['genres'], 'name'));
    }

    private function tmdbImageUrls(array $tmdbData): array
    {
        return [
            'posterUrl' => $this->tmdb->getImageUrl($tmdbData['poster_path'] ?? null, 'w500'),
            'backdropUrl' => $this->tmdb->getImageUrl($tmdbData['backdrop_path'] ?? null, 'w1280'),
        ];
    }

}
