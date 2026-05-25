@extends('layouts.app')

@section('title', 'Login')

@section('content')
<h3 class="text-2xl font-bold mb-6">Iniciar Sesión</h3>

<form method="POST" action="{{ route('login.post') }}" class="space-y-4">
    @csrf

    <div>
        <label for="email">Correo Electrónico</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required>
        @error('email')
            <p>{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password">Contraseña</label>
        <input type="password" name="password" id="password" required>
        @error('password')
            <p>{{ $message }}</p>
        @enderror
    </div>

    <div class="check-label">
        <input type="checkbox" name="remember">
        <span>Recordarme</span>
    </div>

    <a href="#" class="forgot-password">¿Olvidaste la contraseña?</a>

    <button type="submit">Iniciar Sesión</button>

    <p class="mt-4 text-sm">
        ¿Sin cuenta? 
        <a href="{{ route('register') }}">Regístrate aquí</a>
    </p>
</form>
@endsection


