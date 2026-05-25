<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserToken;
use App\Models\User;
use App\Notifications\CustomNotification;
use App\Services\FirebaseNotificationService;
use GuzzleHttp\Client;
use Notification;
use App\Models\FcmToken;


class NotificationController extends Controller
{
   
      public function storeToken(Request $request)

    {
       

        $request->validate([
            'token' => 'required|string',
            'platform' => 'required|string|in:android,ios',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        // Guardar o actualizar el token FCM
        $fcmToken = FcmToken::updateOrCreate(
            ['user_id' => $request->user_id],
            [
                'token' => $request->token,
                'platform' => $request->platform,
            ]
        );

        return response()->json([
            'message' => 'Token FCM guardado correctamente',
            'data' => $fcmToken
        ], 200);
    }


    // Método para enviar notificación a todos los administradores
    public function sendAdminNotification()
    {
        $users = User::where('role', 'admin')->get();

        Notification::send($users, new CustomNotification("Mensaje especial para admin"));

        return response()->json(['message' => 'Notificación enviada a administradores']);
    }

    // Método para enviar una notificación a un usuario específico
    public function sendUniqueNotification()
    {
        $user = User::find(1); 
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $user->notify(new CustomNotification('Has sido seleccionado'));

        return response()->json(['message' => 'Notificación enviada a un usuario']);
    }

    // ENVIAR NOTIFICACIÓN 
    public function sendNotificationToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        // Buscar el token del usuario
        $userToken = UserToken::where('user_id', $request->user_id)->first();
        if (!$userToken) {
            return response()->json(['error' => 'Usuario sin token'], 404);
        }

        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        $serverKey = env('FCM_SERVER_KEY'); 

        $notification = [
            'title' => $request->title,
            'body' => $request->body,
            'sound' => 'default',
        ];

        $fcmNotification = [
            'to' => $userToken->token,
            'notification' => $notification,
            'data' => ['extra_information' => 'opcional']
        ];

        $client = new Client();
        $response = $client->post($fcmUrl, [
            'headers' => [
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $fcmNotification,
        ]);

        return response()->json([
            'message' => 'Notificación enviada',
            'response' => json_decode($response->getBody(), true),
        ]);
    }
}