<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;


class Intercambio extends Model {
    use HasFactory;
    const ESTADOS=['Pendiente', 'Completado', 'Cancelado', 'Valorado'];
    protected $table = 'intercambios';
    protected $fillable = [
        'solicitante_id', 'receptor_id', 
        'prenda_solicitada_id', 'prenda_ofrecida_id', 'estado'
    ];

    // Relación con el usuario que pide
    public function solicitante(): BelongsTo {
        return $this->belongsTo(User::class, 'solicitante_id');
    }

    // Relación con el usuario que recibe la solicitud
    public function receptor(): BelongsTo {
        return $this->belongsTo(User::class, 'receptor_id');
    }

    // Relación con la prenda que QUIEREN conseguir
    public function prendaSolicitada(): BelongsTo {
        return $this->belongsTo(Prenda::class, 'prenda_solicitada_id');
    }

    // Relación con la prenda que OFRECEN a cambio
    public function prendaOfrecida(): BelongsTo {
        return $this->belongsTo(Prenda::class, 'prenda_ofrecida_id');
    }
    
    // Un intercambio tiene una valoración
    public function valoracion(): hasMany {
        return $this->hasMany(Valoracion::class);
    }

    // Función que analiza si el usuario registrado es receptor o solicitante
    public function obtenerIdDelOtroUsuario() {
        return $this->solicitante_id === auth()->id() ? $this->receptor_id : $this->solicitante_id;
    }
    
    // Función que analiza si un intercambio tiene ya una valoración realizada por el usuario autenticado
    public function valoradoPorUnUsuario() {
        return \App\Models\Valoracion::where('intercambio_id', $this->id)->where('evaluador_id', auth()->id())->exists();
    }

    // Función que cambia el estado de una prenda si el intercambio está en completado o valorado
    protected static function booted() {
        // Se dispara justo despuúes de que un intercambio se actualice en la base de datos
        static::updated(function ($intercambio) {
            
            // isDirty() comprueba si el campo 'estado' acaba de ser modificado en esta petición
            if ($intercambio->isDirty('estado')) {
                
                if (in_array($intercambio->estado, ['completado', 'valorado'])) {
                    
                    // Pasamos el booleano de ambas (las intercambiadas) prendas a false
                    $intercambio->prendaSolicitada->disponible = false;
                    $intercambio->prendaSolicitada->save();

                    $intercambio->prendaOfrecida->disponible = false;
                    $intercambio->prendaOfrecida->save();
                }
            }
        });
    }
}
