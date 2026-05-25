<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marcador extends Model
{
    use HasFactory;

    protected $table = 'datos';

    protected $fillable = [
        'nombre',
        'tiempo',
        'tipoEntreno',
        'kilometros',
        'lat',
        'lng',
        'user_id'
    ];
}



