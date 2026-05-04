<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    const ROLES=['admin', 'usuario'];

    // Un usuario tiene muchas prendas
    public function prendas(): HasMany
    {
        return $this->hasMany(Prenda::class);
    }

    // Un usuario inicia muchos intercambios (es el solicitante)
    public function intercambiosSolicitados(): HasMany
    {
        return $this->hasMany(Intercambio::class, 'solicitante_id');
    }

    // Un usuario recibe muchas solicitudes (es el receptor)
    public function intercambiosRecibidos(): HasMany
    {
        return $this->hasMany(Intercambio::class, 'receptor_id');
    }

    // Un usuario puede hacer muchas valoraciones
    public function valoracionesHechas(): hasMany
    {
        return $this->hasMany(Valoracion::class, 'evaluador_id');
    }

    // FUnción para saber si un usuario es administrador
    public function esAdmin()
    {
        return $this->rol === 'admin';
    }
}
