<?php

namespace App\Http\Controllers;

use App\Models\Filme;
use App\Services\TMDBService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $filmes = Filme::all();
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

        Filme::create($data);

        return redirect()->route('dashboard')->with('success', 'Filme adicionado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
{
    $filme = Filme::findOrFail($id);

    $tmdbData = null;
    $director = null;
    $genres = null;
    $posterUrl = null;
    $backdropUrl = null;

    // Se o filme tem tmdb_id, busca os dados do TMDB
    if ($filme->tmdb_id) {
        $tmdbData = $this->tmdb->getMovie((int) $filme->tmdb_id);

        if ($tmdbData) {
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
            $backdropUrl = $this->tmdb->getImageUrl($tmdbData['backdrop_path'] ?? null, 'w780');
        }
    }

    // Escolhe a view com base no status do filme
    if ($filme->assistido) {
        return view('filmes.show', compact(
            'filme', 'tmdbData', 'director', 'genres', 'posterUrl', 'backdropUrl'
        ));
    } else {
        return view('filmes.show_tmdb', compact(
            'filme', 'tmdbData', 'director', 'genres', 'posterUrl', 'backdropUrl'
        ));
    }
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Filme $filme)
    {
        return view('filmes.edit', compact('filme'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Filme $filme)
    {
        
    $data = $request->validate([
        
        'plataforma' => 'nullable|string',
        'data_assistida' => 'nullable|date',
        'nota' => 'nullable|numeric|min:0|max:10',
        'comentarios' => 'nullable|string',
        
    ]);

    $filme->update($data);

    return redirect()->route('filmes.biblioteca')->with('success', 'Filme atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Filme $filme)
    {
    if($filme->poster) {
        Storage::disk('public')->delete($filme->poster);
    }
    if($filme->poster_banner) {
        Storage::disk('public')->delete($filme->poster_banner);
    }
        $filme->delete();
    return redirect()->route('filmes.biblioteca')->with('success', 'Filme deletado com sucesso!');
    }

    public function biblioteca(Request $request)
    {
        $query = Filme::query();

        if ($request->has('assistido') && $request->assistido !== '') {
            $query->where('assistido', $request->boolean('assistido'));
        }

        if ($request->has('favorito') && $request->favorito !== '') {
            $query->where('favorito', $request->boolean('favorito'));
        }

        if ($request->filled('genero')) {
            $query->where('genero', 'LIKE', '%' . $request->genero . '%');
            
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

        $filmes = $query->orderBy('created_at', 'desc')->paginate(50);

        return view('filmes.biblioteca', compact('filmes'));
    }

    public function buscarBiblioteca(Request $request)
        {
            $query = $request->input('q');

            $filmes = Filme::when($query, function ($qBuilder) use ($query) {
                $qBuilder->where(function ($sub) use ($query) {
                    $sub->where('nome', 'like', '%' . $query . '%')
                        ->orWhere('diretor', 'like', '%' . $query . '%')
                        ->orWhere('generos', 'like', '%' . $query . '%')
                        ->orWhere('ano_lancamento', 'like', '%' . $query . '%');
                });
            })
            ->orderBy('nome') // opcional: ordena alfabeticamente
            ->get();

            return view('filmes.biblioteca', compact('filmes', 'query'));
        }

        public function toggleFavorito(Request $request, $id)
        {
            $filme = Filme::findOrFail($id);

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
        $backdropUrl = $this->tmdb->getImageUrl($tmdbData['backdrop_path'] ?? null, 'w780');

        $filme = Filme::where('tmdb_id', $tmdb_id)->first();

        return view('filmes.show_tmdb', compact(
            'tmdbData', 'director', 'genres', 'posterUrl', 'backdropUrl', 'filme', 'tmdb_id'
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
            return back()->withErrors('Não foi possível obter dados do TMDB.');
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
        $backdropUrl = $this->tmdb->getImageUrl($tmdbData['backdrop_path'] ?? null, 'w780');

        $filme = Filme::updateOrCreate(
            ['tmdb_id' => $tmdb_id],
            [
                'nome' => $tmdbData['title'] ?? ($tmdbData['name'] ?? $request->input('nome', 'Sem título')),
                'descricao' => $tmdbData['overview'] ?? $request->input('descricao'),
                'diretor' => $director,
                'genero' => $genres,
                'poster' => $posterUrl,
                'poster_banner' => $backdropUrl,
                'plataforma' => $validated['plataforma'] ?? null,
                'data_assistida' => $validated['data_assistida'] ?? null,
                'nota' => $validated['nota'] ?? null,
                'comentarios' => $validated['comentarios'] ?? null,
                'ano_lancamento' => !empty($tmdbData['release_date']) ? date('Y', strtotime($tmdbData['release_date'])) : null,
                'assistido' => $data['assistido'],
            ]
);


        return redirect()->route('filmes.showTmdb', $filme->tmdb_id)
            ->with('success', 'Filme salvo/atualizado com sucesso.');
    }

    public function assistidos(Filme $filme)
    {
        return view('filmes.show', compact('filme'));
    }


    public function naoAssistidos()
    {
        $filmes = Filme::where('assistido', false)
                        ->orderBy('nome') // opcional: ordenar por título
                        ->get();

        return view('filmes.naoAssistidos', compact('filmes'));
    }

        public function marcarAssistido(Request $request, $id)
    {
        $filme = Filme::findOrFail($id);

        $filme->assistido = true;
        $filme->nota = $request->input('nota');
        $filme->comentario = $request->input('comentario');
        $filme->data_visualizacao = $request->input('data_visualizacao');
        $filme->save();

        return redirect()->route('filmes.show', $filme->id)
                        ->with('success', 'Filme marcado como assistido!');
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
            return redirect()->route('filmes.busca')->with('error', 'Digite algo para pesquisar.');
        }

        $response = $this->tmdb->searchMovies($query);
        $raw = $response['results'] ?? [];
        $results = [];

        foreach ($raw as $r) {
            $results[] = (object) [
                'tmdb_id'      => $r['id'],
                'nome'         => $r['title'] ?? ($r['name'] ?? 'Sem título'),
                'release_date' => $r['release_date'] ?? null,
                'descricao'    => $r['overview'] ?? null,
                'poster_url'   => $this->tmdb->getImageUrl($r['poster_path'] ?? null, 'w500'),
            ];
        }

        return view('filmes.busca', compact('results', 'query'));
    }






}
