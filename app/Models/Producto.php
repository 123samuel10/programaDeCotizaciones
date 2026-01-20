<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
   use HasFactory;

    protected $fillable = [
        'nombre',
        'marca',
        'modelo',
        'tipo',
        'capacidad',
        'peso',
        'dimensiones',
        'color',
        'precio',
        'stock',
    ];

    public function cotizaciones()
    {
        return $this->belongsToMany(Cotizacion::class, 'cotizacion_productos')
                    ->withPivot('cantidad', 'subtotal')
                    ->withTimestamps();
    }
}
