<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('titulo', 100);
            $table->text('descripcion')->nullable();
            $table->enum('tipo', [
                'Camiseta', 
                'Calzonas', 
                'Pantalón', 
                'Sudadera', 
                'Chándal', 
                'Bufanda', 
                'Otro'
            ])->default('Otro');
            $table->string('deporte', 50);
            $table->enum('talla', ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'Única'])->default('Única');
            $table->string('equipo', 50)->nullable();
            $table->string('color', 30)->nullable(); // La IA detecta el color
            $table->integer('anio')->nullable();     // La IA estima el año
            $table->json('etiquetas')->nullable();   // Etiquetas que va a detectar la ia
            $table->enum('estado', ['Nuevo', 'Seminuevo', 'Usado'])->default('Usado');
            $table->boolean('disponible')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prendas');
    }
};
