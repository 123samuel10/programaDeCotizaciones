<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'empresa',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function cotizaciones()
{
    return $this->hasMany(\App\Models\Cotizacion::class, 'user_id');
}
public function ventas()
{
    return $this->hasMany(\App\Models\Venta::class, 'user_id');
}
public function seguimientoseventos()
{
    return $this->hasMany(\App\Models\SeguimientoEvento::class, 'creado_por');
}

}
