<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Marker;
use App\Models\User;

class PageController extends Controller
{
    public function perfil() {
        $user = Auth::user(); 
        return view('pages.perfil', compact('user'));
    }

public function MenuPrincipal() {
    $markers = Marker::where('user_id', Auth::id())->get(); // solo los del usuario logueado
    return view('MenuPrincipal', compact('markers'));
}


    public function actualizarPerfil(Request $request) {
        $user = Auth::user();

        // Validación de los datos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'edad' => 'required|integer|min:0|max:120',
            'altura' => 'required|integer|min:0',
            'peso' => 'required|integer|min:0',
        ]);

        // Guardar los datos
        $user->update([
            'nombre' => $request->nombre,
            'edad' => $request->edad,
            'altura' => $request->altura,
            'peso' => $request->peso,
        ]);

        return redirect()->route('perfil')->with('success', 'Perfil actualizado correctamente.');
    }

    public function entrenos() {
        return view('pages.entrenos');
    }

    public function carreras() {
        return view('pages.carreras');
    }
}