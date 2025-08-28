<?php

namespace App\Http\Controllers;

use App\Models\Filme;
use Illuminate\Http\Request;

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

        if($request->hasFile('poster')){
            $file = $request->file('poster');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->storeAs('posters', $filename, 'public'); // garante o disco correto
            $data['poster'] = $filename;
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
        'titulo' => 'required|string|max:255',
        'descricao' => 'nullable|string',
        'poster' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048'
    ]);

    if($request->hasFile('poster')) {
        $file = $request->file('poster');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('posters', $filename, 'public');
        $data['poster'] = $filename;
    }

    $filme->update($data);

    return redirect()->route('filmes.index')->with('success', 'Filme atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Filme $filme)
    {
        $filme->delete();
    return redirect()->route('filmes.index')->with('success', 'Filme deletado com sucesso!');
    }
}
