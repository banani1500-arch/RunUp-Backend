<?php
namespace App\Http\Middleware;

use Closure;
use App\Models\UserToken;

class TokenAuth
{
    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['error' => 'Token requerido'], 401);
        }

        $userToken = UserToken::where('token', $token)->first();

        if (!$userToken) {
            return response()->json(['error' => 'Token inválido'], 401);
        }

        $request->merge(['auth_user_id' => $userToken->user_id]);

        return $next($request);
    }
}