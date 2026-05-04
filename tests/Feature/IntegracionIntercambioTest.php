<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Prenda;
use App\Models\Intercambio;

class IntegracionIntercambioTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prueba 3.1: Prueba de Integración Descendente (Flujo de Intercambio)
     * Verifica la correcta interacción entre Usuarios, Prendas y el sistema de Intercambios.
     */
    public function test_integracion_flujo_crear_intercambio()
    {
        // 1. ENTRADA (Módulos inferiores): Preparamos las piezas individuales
        $solicitante = User::create(['name' => 'Carlos', 'email' => 'carlos@test.com', 'password' => bcrypt('1234')]);
        $receptor = User::create(['name' => 'Ana', 'email' => 'ana@test.com', 'password' => bcrypt('1234')]);

        $prendaSolicitada = Prenda::create([
            'user_id' => $receptor->id,
            'titulo' => 'Chaqueta de cuero',
            'descripcion' => 'Chaqueta vintage',
            'tipo' => 'Chaqueta',
            'deporte' => 'Ninguno',
            'talla' => 'M',
            'equipo' => 'Ninguno',
            'color' => 'Marrón',
            'estado' => 'Usado',
            'disponible' => 1
        ]);

        $prendaOfrecida = Prenda::create([
            'user_id' => $solicitante->id,
            'titulo' => 'Zapatillas Running',
            'descripcion' => 'Zapatillas casi nuevas',
            'tipo' => 'Zapatillas',
            'deporte' => 'Running',
            'talla' => '42',
            'equipo' => 'Ninguno',
            'color' => 'Rojo',
            'estado' => 'Nuevo',
            'disponible' => 1
        ]);

        // 2. EJECUCIÓN: El sistema integra las partes a través del Controlador
        $respuesta = $this->actingAs($solicitante)->post(route('iniciarintercambio', $prendaSolicitada->id), [
            'receptor_id' => $receptor->id,
            'prenda_solicitada_id' => $prendaSolicitada->id, 
            'prenda_ofrecida_id' => $prendaOfrecida->id,
        ]);

        // 3. SALIDA ESPERADA: Comprobamos que todas las partes se han integrado correctamente en la BD
        // A) Verificamos que no da error y redirige correctamente (suele ser a la lista de intercambios)
        $respuesta->assertStatus(302);

        // B) Verificamos que los modelos se han relacionado correctamente en la tabla intercambios
        $this->assertDatabaseHas('intercambios', [
            'solicitante_id' => $solicitante->id,
            'receptor_id' => $receptor->id,
            'prenda_solicitada_id' => $prendaSolicitada->id,
            'prenda_ofrecida_id' => $prendaOfrecida->id,
            'estado' => 'pendiente' // O el estado por defecto que le pongas al crearlo
        ]);
    }
}