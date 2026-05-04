<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Prenda extends Model
{
    use HasFactory;
    const TIPOS=['Camiseta', 'Calzonas', 'Pantalón', 'Sudadera', 'Chándal', 'Bufanda','Otro'];
    const TALLAS = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'Única'];
    const ESTADOS = ['Nuevo', 'Seminuevo', 'Usado'];
    protected $table = "prendas";
    //$fillable permite guardar datos masivamente
    protected $fillable = [
        'user_id', 'titulo', 'descripcion', 
        'tipo', 'deporte', 'talla', 'equipo', 
        'color', 'anio', 'etiquetas', 
        'estado', 'disponible'
    ];

    // Le decimos a Laravel cómo tratar los campos especiales
    protected $casts = [
        'etiquetas' => 'array',      // Convierte el JSON automáticamente a Array
        'disponible' => 'boolean',   // Asegura que siempre sea true o false
        'anio' => 'integer',
    ];

    // Una prenda pertenece a un usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Una prenda tiene 1 imagen
    public function imagenes(): HasOne
    {
        return $this->HasOne(Imagen::class);
    }
}
