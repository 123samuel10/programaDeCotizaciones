<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{


    protected $fillable = [
        'cliente_id',
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

    // Relación con cliente (User)
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }
}
