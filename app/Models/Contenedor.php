<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Contenedor extends Model
{
   use HasFactory;

    protected $table = 'contenedores';

    protected $fillable = [
        'seguimiento_id','numero_contenedor','bl','naviera',
        'puerto_salida','puerto_llegada','etd','eta','estado'
    ];

    protected $casts = [
        'etd' => 'date',
        'eta' => 'date',
    ];

    public function seguimiento()
    {
        return $this->belongsTo(Seguimiento::class, 'seguimiento_id');
    }
}
