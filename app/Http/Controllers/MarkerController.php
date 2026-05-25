<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marker;
use Illuminate\Support\Facades\Auth;
use App\Notifications\EntrenoCreado;

class MarkerController extends Controller
{
    // Lista los marcadores del usuario para mostrar en la pestaña "Entrenos"
    public function index() {
        $markers = Marker::where('user_id', Auth::id())->get();
        return view('pages.entrenos', compact('markers'));
    }

    // Guardar nuevo marcador desde el mapa
    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'tipoEntreno' => 'nullable|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'kilometros' => 'nullable|numeric',
            'tiempo' => 'nullable|string',
        ]);

        $marker = Marker::create([
            'title' => $request->title,
            'tipoEntreno' => $request->tipoEntreno,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'kilometros' => $request->kilometros,
            'tiempo' => $request->tiempo,
            'user_id' => Auth::id(),
        ]);

        auth()->user()->notify(new EntrenoCreado($marker));
        return response()->json([
        'success' => true,
        'marker' => $marker,
        'message' => 'Entrenamiento creado correctamente'
        
        ]);
       
    }

    // Mostrar formulario para editar
    public function edit(Marker $marker) {
        $this->authorize('update', $marker); // protección de usuario
        return view('pages.edit_entreno', compact('marker'));
    }

    // Actualizar datos
    public function update(Request $request, Marker $marker) {
        $this->authorize('update', $marker);

        $request->validate([
            'title' => 'required|string|max:255',
            'tipoEntreno' => 'nullable|string',
            'kilometros' => 'nullable|numeric',
            'tiempo' => 'nullable|numeric',
        ]);

        $marker->update($request->only(['title','tipoEntreno','kilometros','tiempo']));

        return redirect()->route('entrenos')->with('success', 'Entrenamiento actualizado');
    }

    // Eliminar marcador
    public function destroy(Marker $marker) {
        $this->authorize('delete', $marker);
        $marker->delete();
        return redirect()->route('entrenos')->with('success', 'Entrenamiento eliminado');
    }
	
	// GET /api/markers  (Android)
public function apiIndex(Request $request)
{
    $markers = Marker::where('user_id', $request->auth_user_id)->get();
    return response()->json($markers);
}

// POST /api/markers  (Android)
public function apiStore(Request $request)
{
    $request->validate([
        'title'       => 'required|string|max:255',
        'tipoEntreno' => 'nullable|string',
        'lat'         => 'required|numeric',
        'lng'         => 'required|numeric',
        'kilometros'  => 'nullable|numeric',
        'tiempo'      => 'nullable|string',
    ]);

    $marker = Marker::create([
        'user_id'     => $request->auth_user_id,  // del token, seguro
        'title'       => $request->title,
        'tipoEntreno' => $request->tipoEntreno,
        'lat'         => $request->lat,
        'lng'         => $request->lng,
        'kilometros'  => $request->kilometros,
        'tiempo'      => $request->tiempo,
    ]);

    return response()->json(['success' => true, 'marker' => $marker], 201);
}
}