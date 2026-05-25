<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marker extends Model
{
    protected $fillable = ['title', 'tipoEntreno', 'lat', 'lng', 'user_id', 'kilometros', 'tiempo'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}