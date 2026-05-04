<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Prenda;

class SeguridadPrendaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prueba 2.3: Eliminar prenda de otro usuario (Seguridad)
     */
    public function test_usuario_no_puede_eliminar_prenda_ajena()
    {
        // 1. ENTRADA: Preparamos dos usuarios distintos
        $usuarioIntruso = User::create(['name' => 'Intruso', 'email' => 'intruso@test.com', 'password' => bcrypt('12345678')]);
        $usuarioPropietario = User::create(['name' => 'Propietario', 'email' => 'propietario@test.com', 'password' => bcrypt('12345678')]);

        // Creamos una prenda que pertenece al PROPIETARIO
        $prendaUsuarioB = new Prenda();
        $prendaUsuarioB->user_id = $usuarioPropietario->id;
        $prendaUsuarioB->titulo = 'Sudadera Nike';
        $prendaUsuarioB->descripcion = 'Sudadera de prueba';
        $prendaUsuarioB->tipo = 'Sudadera';
        $prendaUsuarioB->deporte = 'Multideporte';
        $prendaUsuarioB->talla = 'L';
        $prendaUsuarioB->equipo = 'Ninguno';
        $prendaUsuarioB->color = 'Negro';
        $prendaUsuarioB->estado = 'Usado';
        $prendaUsuarioB->disponible = 1;
        $prendaUsuarioB->save();

        // 2. EJECUCIÓN: El usuario INTRUSO intenta borrar la prenda del propietario
        $respuesta = $this->actingAs($usuarioIntruso)->delete(route('borrarprenda', $prendaUsuarioB->id));

        // 3. SALIDA ESPERADA:
        // A) Comprobamos que la base de datos de prendas SIGUE teniendo esa prenda (no se ha borrado)
        $this->assertDatabaseHas('prendas', [
            'id' => $prendaUsuarioB->id
        ]);

        // B) Comprobamos que el sistema bloquea la acción y hace un redirect (302)
        $respuesta->assertStatus(302);
    }
}