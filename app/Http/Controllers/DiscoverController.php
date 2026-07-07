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
        return view('discover.index');
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
