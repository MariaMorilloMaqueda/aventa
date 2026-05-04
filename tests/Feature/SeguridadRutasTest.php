<?php

namespace Tests\Feature;

use Tests\TestCase;

class SeguridadRutasTest extends TestCase
{
    /**
     * Prueba 2.1: Acceso a rutas protegidas (Navegación)
     */
    public function test_visitante_no_puede_acceder_a_subir_prenda()
    {
        // 1. ENTRADA / EJECUCIÓN: Simulamos una petición GET de un usuario sin loguear
        $respuesta = $this->get('/subirprenda'); 

        // 2. SALIDA ESPERADA: El middleware "auth" actúa, devuelve código 302 y redirige al login
        $respuesta->assertStatus(302);
        $respuesta->assertRedirect('/login');
    }
}