<?php

namespace App\Http\Controllers;


use App\Models\Intercambio;
use App\Models\Valoracion;
use Illuminate\Http\Request;

class ValoracionController extends Controller
{
    public function irAValorar(Intercambio $intercambio) {

        // VALIDACIÓN DE USUARIO
        // Se valida si el usuario es solicitante o receptor del intercambio así como si el intercambio está completado.
        $esParticipante = $intercambio->solicitante_id === auth()->id() || $intercambio->receptor_id === auth()->id();
        $estaCompletado = strtolower($intercambio->estado) === 'completado';
        if ($esParticipante && $estaCompletado) {

            $resultado = view('privada.valorarintercambio', ['intercambio' => $intercambio]);
            
        } else {
            $resultado = redirect()->route('intercambios')->with('error', 'No puedes valorar este intercambio');
            
        }

        // DEVOLUCIÓN DE LA VISTA
        return $resultado;
    }

    public function valorarIntercambio (Request $request, Intercambio $intercambio) {

        // VALIDACIÓN DE USUARIO
        // Se valida si el usuario es solicitante o receptor del intercambio así como si el intercambio está completado.
        $esParticipante = $intercambio->solicitante_id === auth()->id() || $intercambio->receptor_id === auth()->id();
        $estaCompletado = strtolower($intercambio->estado) === 'completado';
        
        if (!$esParticipante || !$estaCompletado) {
            $resultado = redirect()->route('intercambios')->with('error', 'Acción no permitida.');
        } else {

            // VALIDACIÓN DE DATOS
            $datos = $request -> validate (
                [
                    'comentario' => 'nullable|string|max:500',
                    'puntuacion' => 'required|in:1,2,3,4,5',
                ],
                [
                    // MENSAJES PERSONALIZADOS
                    'comentario.max' => 'El comentario es demasiado largo (máximo 500 caracteres).',
                    
                    'puntuacion.required' => 'Por favor, selecciona una puntiación entre 1 y 5.',
                    'puntuacion.in' => 'La puntuación seleccionada no es válido.',
                ]
            );

            // MODIFICACIÓN DEL MODELO --> Eloquent
            if ($datos) {

                $valoracion = new Valoracion;
                $valoracion->intercambio_id = $intercambio->id;
                $valoracion->evaluador_id = auth()->id();
                $valoracion->comentario = $request->comentario;
                $valoracion->puntuacion = $request->puntuacion;
                $valoracion->save();;

                // Debe o puede haber 2 valoraciones por intercambio
                $totalValoraciones = Valoracion::where('intercambio_id', $intercambio->id)->count();

                // Solo cambiamos el estado si ya han valorado las 2 personas
                if ($totalValoraciones >= 2) {
                    $intercambio->estado = 'valorado';
                    $intercambio->save();
                }
            }
            $resultado = redirect()->route('intercambios')->with('mensaje', '¡Valoración realizada con éxtio!');
        }

        // GENERACIÓN DE LA VISTA
       return $resultado;
    }

    public function borrarValoracion(Request $request, Valoracion $valoracion) {
        
        // VALIDACIÓN DE USUARIO (Solo Administrador)
        if ($request->user()->esAdmin()) {
            
            // Se recupera el intercambio al que pertenece esta valoración
            $intercambio = $valoracion->intercambio; 
            
            // MODIFICACIÓN DEL MODELO --> Eloquent
            $valoracion->delete(); // Se elimina la valoración de la base de datos
            
            // Se comprueba cuántas valoraciones quedan para ese intercambio
            $totalValoraciones = Valoracion::where('intercambio_id', $intercambio->id)->count();

            // Si al borrarla quedan menos de 2 valoraciones, el estado retrocede a "completado"
            if ($totalValoraciones < 2) {
                $intercambio->estado = 'completado';
                $intercambio->save();
            }
            
            $resultado = back()->with('mensaje', 'La valoración ha sido eliminada.');
            
        } else {
            
            $resultado = back()->with('error', 'Acción denegada. Solo los administradores pueden eliminar valoraciones.');
        }

        // DEVOLUCIÓN DE LA VISTA
        return $resultado;
    }
}
