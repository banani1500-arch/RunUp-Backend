<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;


class CustomNotification extends Notification
{
    use Queueable;
    protected $message;

    public function __construct($message)
    {
        $this->message = $message;

    }

    public function via($notificable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notificable)
    {
        return (new MailMessage)
            ->line($this->message)
            ->action('Ver detalles', url('/'))
            ->line('Gracias por usar nuestra aplicación');
    }

    public function ToDatabase($notificable)
    {
        return[
            'message'=> $this->message,
        ];
    }



















}