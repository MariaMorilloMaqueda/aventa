<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Valoracion extends Model
{
    use HasFactory;
    protected $table = 'valoraciones';
    protected $fillable = [
        'intercambio_id',
        'evaluador_id',
        'puntuacion', // Recordamos que va de 1 a 5
        'comentario',
    ];

    // Una valoración pertenece a un intercambio
    public function intercambio(): BelongsTo
    {
        return $this->belongsTo(Intercambio::class, 'intercambio_id');
    }

    // Un evaluador es un usuario
    public function evaluador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluador_id');
    }
}
