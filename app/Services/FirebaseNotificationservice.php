<?php

namespace App\Services;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;


class FirebaseNotifiactionService
{
    public function sendToToken(string $token, string $title, string $body, array $data = ['user']): void
    {

        $message = Cloudmessage::withTarget('token', $token)
        ->withNotification(Notification::create($title, $body))
        ->withData($data);


        app('firebase.messageing')->send($message);

    }
}