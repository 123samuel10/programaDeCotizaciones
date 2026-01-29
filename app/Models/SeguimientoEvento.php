<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class SeguimientoEvento extends Model
{
  use HasFactory;

    protected $table = 'seguimientoeventos';

    protected $fillable = [
        'seguimiento_id','creado_por','tipo','titulo','descripcion','fecha_evento','archivo'
    ];

    protected $casts = [
        'fecha_evento' => 'datetime',
    ];

    public function seguimiento()
    {
        return $this->belongsTo(Seguimiento::class, 'seguimiento_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}
