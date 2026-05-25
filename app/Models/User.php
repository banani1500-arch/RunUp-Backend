<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relación: un usuario puede tener muchos tokens FCM
     */
    public function tokens()
    {
        return $this->hasMany(UserToken::class, 'user_id', 'id');
    }

    /**
     * Enviar notificación a todos los tokens del usuario
     *
     * @param string $title
     * @param string $body
     * @param array $data (opcional)
     * @return void
     */
    public function sendNotificationToAllTokens(string $title, string $body, array $data = [])
    {
        foreach ($this->tokens as $token) {
            app(\App\Services\FCMv1Service::class)->sendToToken(
                $token->token,
                $title,
                $body,
                $data // ahora permite enviar datos adicionales
            );
        }
    }
}
