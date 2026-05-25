<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MarkerController;
use App\Http\Controllers\MarcadorController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FCMController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

// Rutas públicas
Route::post('/register', [AuthController::class, 'apiRegister']);
Route::post('/login',    [AuthController::class, 'apiLogin']);

// Rutas protegidas con token propio
Route::middleware('token.auth')->group(function () {
    Route::get('/markers',  [MarkerController::class, 'apiIndex']);
    Route::post('/markers', [MarkerController::class, 'apiStore']);

    Route::post('/marcadores',        [MarcadorController::class, 'store']);
    Route::post('/upload',            [UploadController::class, 'upload']);
    Route::post('/fcm-token',         [FCMController::class, 'storeToken'])->name('fcm-token.store');
    Route::post('/send-notification', [NotificationController::class, 'sendNotificationToUser']);

    Route::get('/me', function (Request $request) {
        return response()->json(['user_id' => $request->auth_user_id]);
    });
	
// ------------------------
// RUTA PARA GENERACIÓN DEL PLAN DE ENTRENAMIENTO
// ------------------------

Route::post('/generate-plan', function (Request $request) {
    try {
        $apiKey = env('ANTHROPIC_API_KEY');

        $maxDias = match($request->periodo) {
            '1 semana'      => '7',
            '2 semanas'         => '14',
            '4 semanas' => '28',
            default            => '7'
        };

        $response = Http::withHeaders([
            'x-api-key'         => $apiKey,
            'anthropic-version' => '2023-06-01',
            'Content-Type'      => 'application/json'
        ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
            "model"      => "claude-sonnet-4-20250514",
            "max_tokens" => 4000,
            "messages"   => [
                [
                    "role"    => "user",
                    "content" => "Eres un entrenador de running experto. Genera un plan para {$request->distancia} nivel {$request->nivel} durante {$request->periodo}. Devuelve SOLO el siguiente JSON sin markdown ni texto extra: {\"dias\":[{\"offset\":0,\"tipo\":\"rodaje\",\"descripcion\":\"45 min rodaje suave a 6:00 min/km\"}]}. Tipos válidos: rodaje, series, fartlek, descanso. Genera EXACTAMENTE {$maxDias} días numerados desde offset 0. El JSON debe estar completo y cerrado correctamente."
                ]
            ]
        ]);

        $data = $response->json();
        $text = trim(preg_replace('/```json|```/', '', $data['content'][0]['text'] ?? ''));
        $plan = json_decode($text, true);

        if (!$plan || !isset($plan['dias'])) {
            return response()->json(['error' => 'JSON inválido', 'raw' => $text], 500);
        }

        return response()->json($plan);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// ------------------------
// RUTA PARA ELIMINAR DATOS
// ------------------------
	
//---------Eliminar zapas----------
	
Route::delete('/zapatillas/{id}', function (Request $request, $id) {
    $user = \DB::table('users')->where('id', $request->auth_user_id)->first();
    $query = \DB::table('zapatillas')->where('id', $id);
    if ($user->role !== 'admin') {
        $query->where('user_id', $request->auth_user_id);
    }
    $query->delete();
    return response()->json(['ok' => true]);
});
	
//---------Eliminar markers----------
Route::delete('/markers/{id}', function (Request $request, $id) {
    $user = \DB::table('users')->where('id', $request->auth_user_id)->first();
    $query = \DB::table('markers')->where('id', $id);
    if ($user->role !== 'admin') {
        $query->where('user_id', $request->auth_user_id);
    }
    $query->delete();
    return response()->json(['ok' => true]);
});
	
//---------Eliminar palnificaciones----------
Route::delete('/calendario/{id}', function (Request $request, $id) {
    $user = \DB::table('users')->where('id', $request->auth_user_id)->first();
    $query = \DB::table('calendario')->where('id', $id);
    if ($user->role !== 'admin') {
        $query->where('user_id', $request->auth_user_id);
    }
    $query->delete();
    return response()->json(['ok' => true]);
});

//---------------------------------------------------------------------------

// ------------- Calendario -----------------
    Route::get('/calendario', function (Request $request) {
        $items = \DB::table('calendario')
            ->where('user_id', $request->auth_user_id)
            ->get();
        return response()->json($items);
    });

    Route::post('/calendario', function (Request $request) {
        $id = \DB::table('calendario')->insertGetId([
            'user_id'     => $request->auth_user_id,
            'titulo'      => $request->titulo ?? '',
            'tipo'        => $request->tipo,
            'fecha'       => $request->fecha,
            'descripcion' => $request->descripcion,
            'completado'  => 0,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        return response()->json(['id' => $id]);
    });

    // ── Zapatillas ────────────────
    Route::get('/zapatillas', function (Request $request) {
        $zapas = \DB::table('zapatillas')
            ->where('user_id', $request->auth_user_id)
            ->get();
        return response()->json($zapas);
    });

    Route::post('/zapatillas', function (Request $request) {
        $id = \DB::table('zapatillas')->insertGetId([
            'user_id'               => $request->auth_user_id,
            'marca'                 => $request->marca ?? '',
            'modelo'                => $request->modelo ?? '',
            'kilometros_acumulados' => $request->kilometros_acumulados ?? 0,
            'fecha_compra'          => $request->fecha_compra ?? null,
            'notas'                 => $request->notas ?? '',
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);
        return response()->json(['id' => $id]);
    });
	
	Route::put('/zapatillas/{id}', function (Request $request, $id) {
    \DB::table('zapatillas')
        ->where('id', $id)
        ->where('user_id', $request->auth_user_id)
        ->update([
            'kilometros_acumulados' => $request->kilometros_acumulados ?? 0,
            'fecha_compra'          => $request->fecha_compra ?? null,
            'updated_at'            => now(),
        ]);
    return response()->json(['ok' => true]);
});

// ── Perfil ───────────────────────────
Route::get('/perfil', function (Request $request) {
    $user = \DB::table('users')
        ->where('id', $request->auth_user_id)
        ->select('name', 'edad', 'altura', 'peso')
        ->first();
    return response()->json($user ?? (object)[]);
});

Route::post('/perfil', function (Request $request) {
    \DB::table('users')
        ->where('id', $request->auth_user_id)
        ->update([
			'name'       => $request->nombre,
            'edad'       => $request->edad,
            'altura'     => $request->altura,
            'peso'       => $request->peso,
            'updated_at' => now(),
        ]);
    return response()->json(['ok' => true]);
});
//-----------------------------------------------------------------
	
// ------------------------
// RUTAS PARA EL ADMIN
// ------------------------

// ── Lista de usuarios (solo admin) ───
Route::get('/users', function (Request $request) {
    $user = \DB::table('users')->where('id', $request->auth_user_id)->first();
    if ($user->role !== 'admin') {
        return response()->json(['error' => 'No autorizado'], 403);
    }
    return response()->json(
        \DB::table('users')->select('id','name','email','role','created_at')->get()
    );
});

// ── Admin: eliminar usuario ───
Route::delete('/users/{id}', function (Request $request, $id) {
    $user = \DB::table('users')->where('id', $request->auth_user_id)->first();
    if ($user->role !== 'admin') {
        return response()->json(['error' => 'No autorizado'], 403);
    }
    // Eliminar todos sus datos
    \DB::table('markers')->where('user_id', $id)->delete();
    \DB::table('zapatillas')->where('user_id', $id)->delete();
    \DB::table('calendario')->where('user_id', $id)->delete();
    \DB::table('user_tokens')->where('user_id', $id)->delete();
    \DB::table('users')->where('id', $id)->delete();
    return response()->json(['ok' => true]);
});

// ── Admin: cambiar rol ────
Route::put('/users/{id}/role', function (Request $request, $id) {
    $user = \DB::table('users')->where('id', $request->auth_user_id)->first();
    if ($user->role !== 'admin') {
        return response()->json(['error' => 'No autorizado'], 403);
    }
    \DB::table('users')->where('id', $id)->update([
        'role'       => $request->role,
        'updated_at' => now(),
    ]);
    return response()->json(['ok' => true]);
});

// ── Admin: datos de un usuario ────
Route::get('/users/{id}/datos', function (Request $request, $id) {
    $user = \DB::table('users')->where('id', $request->auth_user_id)->first();
    if ($user->role !== 'admin') {
        return response()->json(['error' => 'No autorizado'], 403);
    }
    return response()->json([
        'markers'    => \DB::table('markers')->where('user_id', $id)->get(),
        'zapatillas' => \DB::table('zapatillas')->where('user_id', $id)->get(),
        'calendario' => \DB::table('calendario')->where('user_id', $id)->get(),
    ]);
});
//----------------------------------------------------------------------------------
	
// ------------------------
// RUTAS SCREEN ALIMENTACIÓN
// ------------------------

// ----------------Alimentación ----------------
Route::get('/alimentacion', function (Request $request) {
    $items = \DB::table('alimentacion')
        ->where('user_id', $request->auth_user_id)
        ->get();
    return response()->json($items);
});

Route::post('/alimentacion', function (Request $request) {
    $id = \DB::table('alimentacion')->insertGetId([
        'user_id'        => $request->auth_user_id,
        'distancia'      => $request->distancia,
        'nombre'         => $request->nombre,
        'desayuno'       => $request->desayuno,
        'comida'         => $request->comida,
        'suplementacion' => $request->suplementacion,
        'cena'           => $request->cena,
        'created_at'     => now(),
        'updated_at'     => now(),
    ]);
    return response()->json(['id' => $id]);
});

Route::delete('/alimentacion/{id}', function (Request $request, $id) {
    $user = \DB::table('users')->where('id', $request->auth_user_id)->first();
    $query = \DB::table('alimentacion')->where('id', $id);
    if ($user->role !== 'admin') {
        $query->where('user_id', $request->auth_user_id);
    }
    $query->delete();
    return response()->json(['ok' => true]);
});

Route::post('/generate-menu', function (Request $request) {
    try {
        $apiKey = env('ANTHROPIC_API_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'API key no encontrada'], 500);
        }
        $response = Http::withHeaders([
            'x-api-key'         => $apiKey,
            'anthropic-version' => '2023-06-01',
            'Content-Type'      => 'application/json'
        ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
            "model"      => "claude-sonnet-4-20250514",
            "max_tokens" => 1000,
            "messages"   => [[
                "role"    => "user",
                "content" => "Eres un nutricionista deportivo experto en running. Genera un menú equilibrado para un corredor que entrena para {$request->distancia}. Devuelve SOLO este JSON sin markdown: {\"nombre\":\"Menú para {$request->distancia}\",\"desayuno\":\"descripción del desayuno\",\"comida\":\"descripción de la comida\",\"suplementacion\":\"Recuperador muscular post-entreno + Bebida isotónica durante el entreno\",\"cena\":\"descripción de la cena\"}. Sé específico con cantidades y alimentos pero sin complicarte, comida normal española."
            ]]
        ]);
        $data = $response->json();
        $text = trim(preg_replace('/```json|```/', '', $data['content'][0]['text'] ?? ''));
        $menu = json_decode($text, true);
        if (!$menu || !isset($menu['desayuno'])) {
            return response()->json(['error' => 'JSON inválido', 'raw' => $text], 500);
        }
        return response()->json($menu);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
});