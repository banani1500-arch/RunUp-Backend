@extends('layouts.pestanas')

@section('title', 'Perfil del Usuario')

@section('content')
<h2>Perfil del Usuario</h2>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<form action="{{ route('perfil.actualizar') }}" method="POST">
    @csrf

    <table>
        <tr>
            <th>Nombre</th>
            <td>
                <input type="text" name="nombre" value="{{ old('nombre', $user->nombre) }}">
                @error('nombre')<div class="error-message">{{ $message }}</div>@enderror
            </td>
        </tr>
        <tr>
            <th>Edad</th>
            <td>
                <input type="number" name="edad" value="{{ old('edad', $user->edad) }}">
                @error('edad')<div class="error-message">{{ $message }}</div>@enderror
            </td>
        </tr>
        <tr>
            <th>Altura (cm)</th>
            <td>
                <input type="number" name="altura" value="{{ old('altura', $user->altura) }}">
                @error('altura')<div class="error-message">{{ $message }}</div>@enderror
            </td>
        </tr>
        <tr>
            <th>Peso (kg)</th>
            <td>
                <input type="number" name="peso" value="{{ old('peso', $user->peso) }}">
                @error('peso')<div class="error-message">{{ $message }}</div>@enderror
            </td>
        </tr>
    </table>

    <button type="submit">Actualizar Perfil</button>
</form>
@endsection
