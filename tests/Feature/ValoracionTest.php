<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Intercambio;
use App\Models\Valoracion;

class ValoracionTest extends TestCase
{
    // Resetea la base de datos de pruebas cada vez que se ejecuta
    use RefreshDatabase; 

    /**
     * Prueba 1.2: Cambio de estado automático del Intercambio tras Valoraciones
     */
    public function test_cambia_estado_intercambio_tras_dos_valoraciones()
    {
        // 1. ENTRADA: Preparamos la base de datos simulada
        $solicitante = User::create(['name' => 'Usuario A', 'email' => 'a@test.com', 'password' => bcrypt('12345678')]);
        $receptor = User::create(['name' => 'Usuario B', 'email' => 'b@test.com', 'password' => bcrypt('12345678')]);

        // CREAMOS LAS PRENDAS PARA SATISFACER LA BASE DE DATOS
        $prendaSolicitada = new \App\Models\Prenda();
        $prendaSolicitada->user_id = $receptor->id;
        $prendaSolicitada->titulo = 'Camiseta Real Betis';
        $prendaSolicitada->descripcion = 'Camiseta de prueba';
        $prendaSolicitada->tipo = 'Camiseta';
        $prendaSolicitada->deporte = 'Fútbol';
        $prendaSolicitada->talla = 'L';
        $prendaSolicitada->equipo = 'Real Betis';
        $prendaSolicitada->color = 'Verde y blanco';
        $prendaSolicitada->estado = 'Nuevo';
        $prendaSolicitada->disponible = 1;
        $prendaSolicitada->save();

        $prendaOfrecida = new \App\Models\Prenda();
        $prendaOfrecida->user_id = $solicitante->id;
        $prendaOfrecida->titulo = 'Camiseta Sevilla FC';
        $prendaOfrecida->descripcion = 'Camiseta de prueba';
        $prendaOfrecida->tipo = 'Camiseta';
        $prendaOfrecida->deporte = 'Fútbol';
        $prendaOfrecida->talla = 'M';
        $prendaOfrecida->equipo = 'Sevilla FC';
        $prendaOfrecida->color = 'Blanco y rojo';
        $prendaOfrecida->estado = 'Nuevo';
        $prendaOfrecida->disponible = 1;
        $prendaOfrecida->save();

        // Creamos un intercambio en estado "completado"
        $intercambio = new Intercambio();
        $intercambio->solicitante_id = $solicitante->id;
        $intercambio->receptor_id = $receptor->id;
        $intercambio->prenda_solicitada_id = $prendaSolicitada->id; 
        $intercambio->prenda_ofrecida_id = $prendaOfrecida->id;    
        $intercambio->estado = 'completado';
        $intercambio->save();

        // 2. EJECUCIÓN: Simulamos que ambos usuarios valoran mediante peticiones HTTP POST
        // Valora el Solicitante usando la ruta nombrada
        $this->actingAs($solicitante)->post(route('valorarintercambio', $intercambio->id), [
            'puntuacion' => '5',
            'comentario' => 'Todo perfecto'
        ]);

        // Valora el Receptor usando la ruta nombrada
        $this->actingAs($receptor)->post(route('valorarintercambio', $intercambio->id), [
            'puntuacion' => '4',
            'comentario' => 'Muy amable'
        ]);

        // 3. SALIDA ESPERADA: Recargamos el intercambio de la BD y comprobamos su nuevo estado
        $intercambio->refresh(); // Actualiza el objeto con los datos nuevos de la BD
        
        $this->assertEquals(
            'Valorado', 
            $intercambio->estado, 
            'El estado del intercambio debería haber cambiado a valorado automáticamente.'
        );
    }
}