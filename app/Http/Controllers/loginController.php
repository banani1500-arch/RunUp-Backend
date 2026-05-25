<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Registro de usuario
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken("token")->plainTextToken;

        return response()->json([
            "user"  => $user,
            "token" => $token
        ], 201);
    }

    /**
     * Login de usuario
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // Verifica credenciales
        if (!Auth::attempt($request->only("email", "password"))) {
            return response()->json([
                "message" => "Credenciales incorrectas"
            ], 401);
        }

        $user  = Auth::user();
        $token = $user->createToken("token")->plainTextToken;

        return response()->json([
            "user"  => $user,
            "token" => $token
        ], 200);
    }

    /**
     * Logout del usuario
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            "message" => "Sesión cerrada"
        ]);
    }
}
