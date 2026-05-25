@extends('layouts.menu')

@section('header-title', 'Menu Principal')
@section('header-text')
Bienvenido {{ auth()->user()->name }}. Aquí podrás guardar tus entrenamientos y acceder a tus secciones principales.
@endsection

@section('mapa-text')
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <a href="{{ route('entrenos') }}" class="text-decoration-none">
            <div class="card text-center p-4 box-entrenos">
                <h3>Entrenos</h3>
                <p>Ver y registrar tus entrenamientos.</p>
            </div>
        </a>
    </div>

    <div class="col-md-4 mb-3">
        <a href="{{ route('carreras') }}" class="text-decoration-none">
            <div class="card text-center p-4 box-carreras">
                <h3>Carreras</h3>
                <p>Accede a tus carreras y resultados.</p>
            </div>
        </a>
    </div>

    <div class="col-md-4 mb-3">
        <a href="{{ route('perfil') }}" class="text-decoration-none">
            <div class="card text-center p-4 box-perfil">
                <h3>Perfil</h3>
                <p>Editar información de tu perfil.</p>
            </div>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">Mapa de Entrenamientos</div>
            <div class="card-body">
                <div id="map" style="height: 500px; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
{{-- Script del mapa (NO módulo) --}}
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
        // Guardamos lat/lng en el modal
        document.getElementById('markerLat').value = e.latLng.lat();
        document.getElementById('markerLng').value = e.latLng.lng();

        // Mostrar modal
        var markerModal = new bootstrap.Modal(document.getElementById('markerModal'));
        markerModal.show();
    });
};

// Enviar formulario del modal
document.getElementById('markerForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const title = document.getElementById('markerTitle').value;
    const tipoEntreno = document.getElementById('markerTipo').value;
    const kilometros = document.getElementById('markerKm').value;
    const tiempo = document.getElementById('markerTiempo').value;
    const lat = document.getElementById('markerLat').value;
    const lng = document.getElementById('markerLng').value;

    const response = await fetch("{{ route('marker.store') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ title, tipoEntreno, kilometros, tiempo, lat, lng })
    });

    if (response.ok) {
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
        var markerModal = bootstrap.Modal.getInstance(document.getElementById('markerModal'));
        markerModal.hide();

        // Recargar página para que se vea en "Mis Entrenos"
        location.reload();
    } else {
        alert("Error al guardar el entrenamiento");
    }
});

</script>

{{-- Script de Firebase (módulo) --}}
<script type="module">
import { initializeApp } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-app.js";
import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging.js";

const firebaseConfig = {
    apiKey: "AIzaSyDXJzPdwTbovTIPObZgEtsenWkWNRh6lDI",
    authDomain: "formacion-bruno.firebaseapp.com",
    projectId: "formacion-bruno",
    storageBucket: "formacion-bruno.appspot.com",
    messagingSenderId: "288182869770",
    appId: "1:288182869770:web:255f361764d0763ffea4f3"
};

const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

async function initFCM() {
    try {
        await Notification.requestPermission();

        const token = await getToken(messaging, {
            vapidKey: "TU_PUBLIC_VAPID_KEY"
        });

        if (token) {
            console.log("Token FCM:", token);

            await fetch("{{ route('fcm-token.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    token: token,
                    user_id: {{ auth()->user()->id }}
                })
            });
        }
    } catch (err) {
        console.error("Error obteniendo token FCM:", err);
    }
}

onMessage(messaging, (payload) => {
    alert((payload.notification?.title || "") + "\n" + (payload.notification?.body || ""));
});

initFCM();
</script>

{{-- Cargar Google Maps con callback --}}
<script async
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBE43IZvIsQwebIBcnnRkaAdX7Nr0dfKxs&callback=initMap">
</script>
@endpush
