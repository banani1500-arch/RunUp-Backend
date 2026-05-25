<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Marker;
use App\Notifications\EntrenoCreado;



class EntrenoCreado extends Notification
{

    use Queueable;

    protected $marker;

    public function __construct(Marker $marker)
    {
        $this->marker = $marker;

    }

    public function via($notifiable)
    {

        return ['database'];
    }

    public function toArray($notifiable)
    {
        return[
            'message' => "El entreno '{$this->marker->title}' ha sido creado con éxito",
            'marker_id' => $this->marker->id,
        ];
    }



















}