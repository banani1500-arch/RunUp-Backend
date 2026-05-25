importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');

firebase.initializeApp({
    apiKey: "AIzaSyDXJzPdwTbovTIPObZgEtsenWkWNRh6lDI",
    authDomain: "formacion-bruno.firebaseapp.com",
    projectId: "formacion-bruno",
    storageBucket: "formacion-bruno.firebasestorage.app",
    messagingSenderId: "288182869770",
    appId: "1:288182869770:web:255f361764d0763ffea4f3"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
    const notificationTitle = payload.notification?.title || 'Notificación';
    const notificationOptions = {
        body: payload.notification?.body || '',
        icon: '/icon.png'
    };
    return self.registration.showNotification(notificationTitle, notificationOptions);
});