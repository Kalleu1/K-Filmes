<?php

namespace App\Http\Controllers;

use App\Models\Filme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FilmeController extends Controller
{
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
    public function create()
    {
        return view('filmes.create');
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
        'poster' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        'poster_banner' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    ]);

        if($request->hasFile('poster')){
            $data['poster'] = $request->file('poster')->store('posters', 'public');
        }

        if ($request->hasFile('poster_banner')) {
            $data['poster_banner'] = $request->file('poster_banner')->store('posters_banners', 'public');
        }

        Filme::create($data);

        return redirect()->route('filmes.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        'nome' => 'required|string|max:255',
        'descricao' => 'nullable|string',
        'plataforma' => 'nullable|string',
        'data_assistida' => 'nullable|date',
        'diretor' => 'nullable|string|max:255',
        'genero' => 'nullable|string|max:255',
        'nota' => 'nullable|numeric|min:0|max:10',
        'comentarios' => 'nullable|string',
        'poster' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        'poster_banner' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    ]);

    if($request->hasFile('poster')) {
        $file = $request->file('poster');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('posters', $filename, 'public');
        $data['poster'] = $filename;
    }

    if($request->hasFile('poster_banner')) {
    $file = $request->file('poster_banner');
    $filename = time() . '_' . $file->getClientOriginalName();
    $file->storeAs('posters_banners', $filename, 'public');
    $data['poster_banner'] = $filename;
}

    $filme->update($data);

    return redirect()->route('filmes.index')->with('success', 'Filme atualizado com sucesso!');
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
    return redirect()->route('filmes.index')->with('success', 'Filme deletado com sucesso!');
    }
}
