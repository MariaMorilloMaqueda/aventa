<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Imagen extends Model
{
    use HasFactory;
    protected $table = 'imagenes'; 
    protected $fillable = ['prenda_id', 'url'];

    // Una imagen pertenece a una prenda
    public function prenda(): BelongsTo
    {
        return $this->belongsTo(Prenda::class);
    }
}
