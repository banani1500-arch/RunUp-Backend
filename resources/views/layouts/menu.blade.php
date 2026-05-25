<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('header-title', 'Aplicación')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('styles')
</head>
<body>

<!-- MENÚ PRINCIPAL -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Menu Principal</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" 
            aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Opciones
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
            <li><a class="dropdown-item" href="{{ route('perfil') }}">Perfil</a></li>
            <li><a class="dropdown-item" href="{{ route('entrenos') }}">Entrenos</a></li>
            <li><a class="dropdown-item" href="{{ route('carreras') }}">Carreras</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<style>
.box-entrenos { background-color: #ff9999; transition: all 0.3s ease; }
.box-carreras { background-color: #99ccff; transition: all 0.3s ease; }
.box-perfil { background-color: #99ff99; transition: all 0.3s ease; }

/* Efecto hover */
.box-entrenos:hover, .box-carreras:hover, .box-perfil:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}
</style>

<div class="container mt-4">
    <h1 class="mb-3">@yield('header-title')</h1>
    <p class="lead">@yield('header-text')</p>
</div>

<div class="container">
    @yield('mapa-text')
</div>

<div class="container">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-danger">
            Cerrar Sesión
        </button>
    </form>
</div>

<style>
.container{

  size: 200PX;

}
</style>

<!-- Modal para nuevo entrenamiento -->
<div class="modal fade" id="markerModal" tabindex="-1" aria-labelledby="markerModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="markerForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="markerModalLabel">Nuevo Entrenamiento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="markerLat">
          <input type="hidden" id="markerLng">

          <div class="mb-3">
            <label>Nombre del Entreno</label>
            <input type="text" class="form-control" id="markerTitle" required>
          </div>

          <div class="mb-3">
            <label>Tipo de Entreno</label>
            <select class="form-control" id="markerTipo" required>
              <option value="">Seleccione...</option>
              <option value="Rodaje">Rodaje</option>
              <option value="Series">Series</option>
              <option value="Cambios">Cambios</option>
            </select>
          </div>

          <div class="mb-3">
            <label>Kilómetros</label>
            <input type="number" step="0.01" class="form-control" id="markerKm" required>
          </div>

          <div class="mb-3">
            <label>Tiempo (hh:mm:ss)</label>
            <input type="text" class="form-control" id="markerTiempo" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')

<script>
let map;

window.initMap = function() {
    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 6,
        center: { lat: 40.4168, lng: -3.7038 }
    });

    // Cargar markers existentes
    const markers = @json($markers ?? []);
    markers.forEach(m => {
        const marker = new google.maps.Marker({
            position: { lat: parseFloat(m.lat), lng: parseFloat(m.lng) },
            map
        });
        marker.addListener("click", () => {
            new google.maps.InfoWindow({
                content: `<b>${m.title}</b><br>${m.tipoEntreno} - ${m.kilometros} km - ${m.tiempo}`
            }).open(map, marker);
        });
    });

    // Click para nuevo marker
    map.addListener("click", (e) => {
        document.getElementById('markerLat').value = e.latLng.lat();
        document.getElementById('markerLng').value = e.latLng.lng();

        var markerModal = new bootstrap.Modal(document.getElementById('markerModal'));
        markerModal.show();
    });
});

// Enviar formulario del modal
document.getElementById('markerForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const title = document.getElementById('markerTitle').value;
    const tipoEntreno = document.getElementById('markerTipo').value;
    const kilometros = document.getElementById('markerKm').value;
    const tiempo = document.getElementById('markerTiempo').value;
    const lat = document.getElementById('markerLat').value;
    const lng = document.getElementById('markerLng').value;

     try {
        const response = await fetch("{{ route('marker.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ title, tipoEntreno, kilometros, tiempo, lat, lng })
        });

        // ⚠️ Aquí leemos la respuesta como JSON
        const data = await response.json();

        if (data.success) {
            // Añadir marker al mapa
            const marker = new google.maps.Marker({
                position: { lat: parseFloat(lat), lng: parseFloat(lng) },
                map
            });
            marker.addListener("click", () => {
                new google.maps.InfoWindow({
                    content: `<b>${title}</b><br>${tipoEntreno} - ${kilometros} km - ${tiempo}`
                }).open(map, marker);
            });

            // Cerrar modal
            const markerModal = bootstrap.Modal.getInstance(document.getElementById('markerModal'));
            markerModal.hide();

            // Opcional: mostrar alerta
            alert(data.message);

            // Recargar lista de entrenos o página
            location.reload();
        } else {
            alert("No se pudo guardar el entrenamiento");
        }

    } catch (error) {
        console.error("Error al guardar el entrenamiento:", error);
        alert("Error al guardar el entrenamiento");
    }
});
</script>

<!-- Cargar Google Maps con callback -->
<script async
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBE43IZvIsQwebIBcnnRkaAdX7Nr0dfKxs&callback=initMap">
</script>

<style>
.box {
  text-align: center;
  height: 40px;
  width: 200px;
  background-color: rgb(235, 201, 89);
}
</style>

</body>
</html>
