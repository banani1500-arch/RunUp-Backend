
@extends('layouts.app')

@section('title', 'Registro')

@section('content')
<h3 class="text-2xl font-bold text-center mb-6">Crear Cuenta</h3>

<form method="POST" action="{{ route('register.post') }}" class="space-y-4">
    @csrf

   
    <div>
        <label for="name" class="block text-gray-700">Nombre</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}" required
            class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
        @error('name')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    
    <div>
        <label for="email" class="block text-gray-700">Correo Electrónico</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required
            class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
        @error('email')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

   
    <div>
        <label for="password" class="block text-gray-700">Contraseña</label>
        <input type="password" name="password" id="password" required
            class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
        @error('password')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    
    <div>
        <label for="password_confirmation" class="block text-gray-700">Confirmar Contraseña</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required
            class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
    </div>

    
    <button type="submit"
        class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition-colors">
        Crear Cuenta
    </button>
</form>


<p class="text-center mt-4 text-sm">
    ¿Ya tienes una cuenta?
    <a href="{{ route('login') }}" class="text-blue-500 hover:underline">
        Inicia sesión aquí
    </a>
</p>

@endsection


