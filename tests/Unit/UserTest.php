<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    /**
     * Prueba Unitaria 1: Condición favorable.
     * Verifica que el sistema detecta correctamente a un administrador.
     */
    public function test_comprueba_si_usuario_es_administrador()
    {
        // 1. ENTRADA: Instanciamos un usuario y le asignamos el rol de admin
        $admin = new User();
        $admin->rol = 'admin'; 

        // 2. EJECUCIÓN: Llamamos a la función de tu modelo
        $resultado = $admin->esAdmin();

        // 3. SALIDA ESPERADA: Afirmamos que el resultado DEBE ser true
        $this->assertTrue($resultado, 'El usuario debería ser reconocido como administrador.');
    }

    /**
     * Prueba Unitaria 2: Condición desfavorable.
     * Verifica que el sistema NO detecta como administrador a un usuario normal.
     */
    public function test_comprueba_si_usuario_normal_no_es_administrador()
    {
        // 1. ENTRADA: Instanciamos un usuario y le asignamos un rol del array ROLES
        $usuario = new User();
        $usuario->rol = 'usuario';

        // 2. EJECUCIÓN
        $resultado = $usuario->esAdmin();

        // 3. SALIDA ESPERADA: Afirmamos que el resultado DEBE ser false
        $this->assertFalse($resultado, 'El usuario normal no debería tener permisos de administrador.');
    }
}