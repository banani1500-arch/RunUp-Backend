<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // ========================
    // MÉTODOS WEB (Blade)
    // ========================

    // Mostrar formulario de registro web
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Registrar usuario desde web
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $this->createUser($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Usuario registrado correctamente',
                'user' => $user
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al registrar usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Mostrar formulario de login web
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Login desde web
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/MenuPrincipal');
        }

        return back()->with('error', 'Correo o contraseña incorrectos.');
    }

    // Logout web
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // ========================
    // MÉTODOS API (Flutter / JSON)
    // ========================

    // Registrar usuario desde API
    public function apiRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = $this->createUser($request->all());

        return response()->json([
            'message' => 'Usuario registrado con éxito',
            'user' => $user
        ], 201);
    }

   // Login desde API (Android)
public function apiLogin(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (!Auth::attempt($credentials)) {
        return response()->json(['error' => 'Correo o contraseña incorrectos.'], 401);
    }

    $user = Auth::user();

    // Generar token y guardarlo en user_tokens
    $token = \Illuminate\Support\Str::random(60);
    \App\Models\UserToken::create([
        'user_id'  => $user->id,
        'token'    => $token,
        'platform' => $request->header('X-Platform', 'android'),
    ]);

    return response()->json([
        'message' => 'Login exitoso',
        'token'   => $token,
        'user_id' => $user->id,
        'name'    => $user->name,
		'role'    => $user->role,
    ]);
}

    // ========================
    // MÉTODOS PRIVADOS
    // ========================

    // Crear usuario (web o API)
    private function createUser(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user', // <-- asigna siempre un role por defecto
        ]);
    }
}
