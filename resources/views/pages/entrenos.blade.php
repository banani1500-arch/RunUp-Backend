@extends('layouts.pestanas')

@section('content')
<div class="container mt-3">

    <h2 class="mb-3">Mis Entrenos</h2>

    
   @foreach(auth()->user()->unreadNotifications as $notification)
    <div class="alert alert-info">
        {{ $notification->data['message'] }}
    </div>
    @endforeach

    @php
    auth()->user()->unreadNotifications->markAsRead();
    @endphp


    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Tipo Entreno</th>
                <th>Kilómetros</th>
                <th>Tiempo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($markers as $marker)
                <tr>
                    <td>{{ $marker->title }}</td>
                    <td>{{ $marker->tipoEntreno }}</td>
                    <td>{{ $marker->kilometros }}</td>
                    <td>{{ $marker->tiempo }}</td>
                    <td>
                        <a href="{{ route('marker.edit', $marker) }}" class="btn btn-sm btn-primary">Editar</a>

                        <form action="{{ route('marker.destroy', $marker->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este entrenamiento?')">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No hay entrenamientos aún.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Botón para volver al Menú Principal -->
    <div class="text-center mt-4">
        <a href="{{ route('MenuPrincipal') }}" class="btn btn-success rounded-pill px-4 py-2" style="background-color: #b2f2bb; color: #000;">
    Volver al Menú Principal
</a>

    </div>

</div>
@endsection
