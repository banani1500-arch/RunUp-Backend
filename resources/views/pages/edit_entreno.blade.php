@extends('layouts.pestanas')

@section('content')
<h2>Editar Entrenamiento</h2>

<form action="{{ route('marker.update', $marker) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="title" value="{{ $marker->title }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Descripción</label>
        <textarea name="description" class="form-control">{{ $marker->tipoEntreno }}</textarea>
    </div>
    <div class="mb-3">
        <label>Kilómetros</label>
        <input type="number" step="0.01" name="kilometros" value="{{ $marker->kilometros }}" class="form-control">
    </div>
    <div class="mb-3">
        <label>Tiempo</label>
        <input type="number" step="0.01" name="tiempo" value="{{ $marker->tiempo }}" class="form-control">
    </div>
    <button class="btn btn-success">Actualizar</button>
</form>
@endsection
