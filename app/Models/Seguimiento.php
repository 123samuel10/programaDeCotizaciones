<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Seguimiento extends Model
{
   use HasFactory;

    protected $table = 'seguimientos';

    protected $fillable = [
        'venta_id','proveedor_id','pais_destino','tipo_envio','incoterm',
        'estado','etd','eta','observaciones'
    ];

    protected $casts = [
        'etd' => 'date',
        'eta' => 'date',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function contenedores()
    {
        return $this->hasMany(Contenedor::class, 'seguimiento_id');
    }

    public function eventos()
    {
        return $this->hasMany(SeguimientoEvento::class, 'seguimiento_id')->latest('fecha_evento');
    }
}
