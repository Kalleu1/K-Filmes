<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveTmdbFilmeRequest;
use App\Http\Requests\UpdateFilmeRequest;
use App\Models\Filme;
use App\Services\FilmeBibliotecaService;
use App\Services\ColorThemeService;
use App\Services\FilmeMediaService;
use App\Services\TMDBService;
use App\Services\TmdbMovieMapperService;
use App\Support\Toast\ToastMessages;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;


class FilmeController extends Controller
{

    protected $tmdb;
    protected $tmdbMovieMapper;

    public function __construct(TMDBService $tmdb, TmdbMovieMapperService $tmdbMovieMapper)
    {
        $this->tmdb = $tmdb;
        $this->tmdbMovieMapper = $tmdbMovieMapper;
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
            

            [
                'director' => $director,
                'genres' => $genres,
                'posterUrl' => $posterUrl,
                'backdropUrl' => $backdropUrl,
                'tmdbRating' => $tmdbRating,
            ] = $this->tmdbMovieMapper->mapDetails($tmdbData);

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

    
    public function destroy(Filme $filme, FilmeMediaService $filmeMediaService)
    {
        $this->authorize('delete', $filme);
        $filmeMediaService->deleteMovieImages($filme);
        $filme->delete();
        return redirect()->route('filmes.biblioteca')
            ->with(ToastMessages::movieDeleted());
    }

    public function biblioteca(Request $request, FilmeBibliotecaService $filmeBibliotecaService)
    {
        $userId = Auth::id();
        $filmes = $filmeBibliotecaService->paginateBiblioteca($request, $userId);

        $plataformas = Filme::where('user_id', $userId)
            ->whereNotNull('plataforma')
            ->where('plataforma', '!=', '')
            ->distinct()
            ->orderBy('plataforma', 'asc')
            ->pluck('plataforma')
            ->toArray();

        $anos = Filme::where('user_id', $userId)
            ->whereNotNull('ano_lancamento')
            ->distinct()
            ->orderBy('ano_lancamento', 'desc')
            ->pluck('ano_lancamento')
            ->toArray();

        return view('filmes.biblioteca', compact('filmes', 'plataformas', 'anos'));
    }

    public function buscarBiblioteca(Request $request)
    {
        return redirect()->route('filmes.biblioteca', $request->query());
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
            $results[] = $this->tmdbMovieMapper->mapSearchResult($r);
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

        [
            'director' => $director,
            'genres' => $genres,
            'posterUrl' => $posterUrl,
            'backdropUrl' => $backdropUrl,
            'tmdbRating' => $tmdbRating,
        ] = $this->tmdbMovieMapper->mapDetails($tmdbData);

        $filme = $this->userFilmesQuery()
            ->where('tmdb_id', $tmdb_id)
            ->first();
        $similarMovies = $this->tmdb->getSimilarMovies((int) $tmdb_id);
        if ($director) {
                $directorMovies = $this->tmdb->getMoviesByDirector($director, 7);
            }
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

        $filme = Filme::updateOrCreate(
        [
            'tmdb_id' => $tmdb_id,
            'user_id' => Auth::id(),
        ],
        $this->tmdbMovieMapper->mapLocalMovieAttributesFromTmdb(
            $tmdbData,
            $validated,
            $data['assistido'],
            $request->input('descricao')
        )
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
            return (object) $this->tmdbMovieMapper->mapSearchResult($r);
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

}

