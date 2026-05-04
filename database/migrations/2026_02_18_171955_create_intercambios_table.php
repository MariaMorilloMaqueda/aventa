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
        Schema::create('intercambios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitante_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('receptor_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('prenda_solicitada_id')->constrained('prendas')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('prenda_ofrecida_id')->constrained('prendas')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('estado', ['Pendiente', 'Completado', 'Cancelado', 'Valorado'])->default('Pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intercambios');
    }
};
