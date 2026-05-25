<?php

namespace App\Http\Controllers;

use App\Models\Marcador;
use Illuminate\Http\Request;

class MarcadorController extends Controller
{
    // LISTAR TODOS LOS MARCADORES (GET)
    public function index()
    {
        return response()->json(Marcador::all());
    }

    // GUARDAR MARCADOR (POST)
    public function store(Request $request)
    {

        
        $validated = $request->validate([
            'title' => 'required|string',
            'tiempo' => 'required|string',
            'tipoEntreno' => 'required|string',
            'kilometros' => 'required',
            'lat' => 'required',
            'lng' => 'required'
        ]);

    // CAMPO FLUTTER A BBDD
       

        $marcador = Marcador::create($validated);

        return response()->json($marcador, 201);
    }
}

