<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use App\Models\User;

class ValidacionPrendaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prueba 2.2: Subir una prenda (Validación de Entradas Incorrectas)
     */
    public function test_rechaza_prenda_con_datos_incorrectos()
    {
        // 1. ENTRADA: Preparamos un usuario logueado y un archivo PDF "falso"
        $usuario = User::create([
            'name' => 'Usuario Prueba', 
            'email' => 'prueba@test.com', 
            'password' => bcrypt('12345678')
        ]);

        // Simulamos un archivo PDF de 100kb
        $archivoPdf = UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf');

        // 2. EJECUCIÓN: Enviamos la petición POST
        $respuesta = $this->actingAs($usuario)->post(route('prendasubida'), [
            'titulo' => '', // DATO INCORRECTO: Vacío
            'descripcion' => 'Descripción de prueba correcta',
            'tipo' => 'Camiseta',
            'deporte' => 'Fútbol',
            'talla' => 'M',
            'equipo' => 'Sevilla',
            'color' => 'Rojo',
            'estado' => 'Nuevo',
            'imagen' => $archivoPdf // DATO INCORRECTO: Es un PDF
        ]);

        // 3. SALIDA ESPERADA:
        // A) Comprobamos que la base de datos de prendas sigue vacía (no se guardó nada)
        $this->assertDatabaseCount('prendas', 0);

        // B) Comprobamos que el sistema hace un redirect (código 302) de vuelta al formulario
        $respuesta->assertStatus(302);

        // C) Comprobamos que la sesión contiene exactamente los mensajes de error personalizados
        $respuesta->assertSessionHasErrors([
            'titulo' => 'El título de la prenda es obligatorio.',
            'imagen' => 'El archivo debe ser una imagen real.'
        ]);
    }
}