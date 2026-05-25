<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FcmToken;

class FCMController extends Controller
{
    // Guardar token FCM
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'platform' => 'required|string|in:android,ios',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $fcmToken = FcmToken::updateOrCreate(
            ['user_id' => $request->user_id, 'token' => $request->token],
            [
                'token' => $request->token,
                'platform' => $request->platform,
            ]
        );

        return response()->json([
            'message' => 'Token FCM guardado correctamente',
            'data' => $fcmToken
        ], 200);
    }
}





