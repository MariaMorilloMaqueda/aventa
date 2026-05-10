<?php

namespace App\Http\Controllers;

use App\Models\Prenda;
use App\Models\Intercambio;
use App\Models\Valoracion;
use Illuminate\Http\Request;

class IntercambioController extends Controller {
    
    public function irAIntercambios(Request $request) {

        $usuario = $request->user();
        
        // BLOQUEO DE SEGURIDAD: Solo usuarios estándar pueden acceder
        if ($usuario->rol !== 'usuario') {
            return redirect()->route('catalogo')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        // USUARIO NORMAL: ve sus propios intercambios
        $userId = $usuario->id;

        $intercambios = Intercambio::with([
            'solicitante', 
            'receptor', 
            'prendaOfrecida.imagenes', 
            'prendaSolicitada.imagenes'
        ])
        ->where('solicitante_id', $userId)
        ->orWhere('receptor_id', $userId)
        ->get();

        $idsIntercambios = $intercambios->pluck('id');

        $valoraciones = Valoracion::with(['intercambio.solicitante', 'intercambio.receptor'])
            ->whereIn('intercambio_id', $idsIntercambios)
            ->get();

        $titulo = "este es tu historial de intercambios";

        // GENERACIÓN DE LA VISTA
        return view('privada.intercambios', ['intercambios' => $intercambios,'valoraciones' => $valoraciones,'mensajePersonalizado' => $titulo]);
    }

    public function irAIntercambiar(Request $request, Prenda $prenda) {

        // VALIDACIÓN DE USUARIO
        // Se valida si el usuario registrado es administrador.
        if ($request->user()->esAdmin()) {
            $resultado = redirect()->route('catalogo')->with('error', 'Los administradores no participan en intercambios.');
        }
        // Se valida si el solicitante es el dueño de la prenda.
        elseif ($prenda->user_id === auth()->id()) {
        $resultado = redirect()->route('catalogo')->with('error', 'No puedes intercambiar tu propia prenda.');
        } else {
        // Se buscan aquellas prendas que estén disponibles
        $misPrendas = Prenda::where('user_id', auth()->id())->get();
        $resultado = view('privada.iniciarintercambio', ['prendaSolicitada' => $prenda, 'misPrendas' => $misPrendas]);
        }
        
        return $resultado;
    }

    public function iniciarIntercambio (Request $request, Prenda $prenda) {

        // VALIDACIÓN DE USUARIO
        // Se valida si el solicitante es el dueño de la prenda.
        if ($prenda->user_id === auth()->id()) {
            
        $resultado = redirect()->route('catalogo')->with('error', 'No puedes intercambiar tu propia prenda.');
        
        } else {

            // VALIDACIÓN DE DATOS
            $datos = $request->validate([
                'prenda_ofrecida_id' => 'required|exists:prendas,id'
            ], [
                'prenda_ofrecida_id.required' => 'Debes seleccionar una prenda de tu armario para intercambiar.',
                'prenda_ofrecida_id.exists' => 'La prenda seleccionada no es válida.'
            ]);

            // MODIFICACIÓN DEL MODELO --> Eloquent
            if ($datos) {
                $intercambio = new Intercambio;
                // En este caso, todas las asignaciones son automátias excepto la prenda ofecida
                $intercambio->solicitante_id = auth()->id();
                $intercambio->receptor_id = $prenda->user_id;
                $intercambio->prenda_solicitada_id = $prenda->id;
                $intercambio->prenda_ofrecida_id = $request->prenda_ofrecida_id;
                $intercambio->estado = 'pendiente';
                
                $intercambio->save();
            }

            $resultado = redirect()->route('intercambios')->with('mensaje', '¡Propuesta de intercambio enviada con éxito!');
        }

        // DEVOLUNCIÓN DE LA VISTA
        return $resultado;
    }

    public function aceptarIntercambio(Request $request, Intercambio $intercambio) {

        // VALIDACIÓN DE USUARIO Y ESTADO
        $usuario = $request->user();

        // Solo se puede aceptar si el estado esta en pendiente y es el usuario autenticado (receptor).
        if (strtolower($intercambio->estado) !== 'pendiente' || $intercambio->receptor_id !== $usuario->id) {

            $resultado = redirect()->back()->with('error', 'No tienes permiso para aceptar este intercambio.');

        } else {

            // MODIFICACIÓN DEL MODELO --> Eloquent (las prendas pasan al estado no disponible gracias al evento del modelo)
            $intercambio->estado = 'completado';
            $intercambio->save();

            $resultado = redirect()->route('intercambios')->with('mensaje', '¡Has aceptado el intercambio! Las prendas ya no están disponibles en el catálogo.');
        }

        // DEVOLUNCIÓN DE LA VISTA
        return  $resultado;
    }


    public function cancelarIntercambio(Request $request, Intercambio $intercambio) {

        // VALIDACIÓN DE USUARIO Y ESTADO
        $esParticipante = auth()->id() === $intercambio->solicitante_id || auth()->id() === $intercambio->receptor_id;
        $esCancelable = strtolower($intercambio->estado) === 'pendiente';

        // Solo se puede cancelar si el estado esta en pendiente y el usuario es participante del intercambio
        if (!$esCancelable || !$esParticipante) {

            $resultado = redirect()->back()->with('error', 'No tienes permiso para cancelar este intercambio.');

        } else {
            // MODIFICACIÓN DEL MODELO --> Eloquent
            $intercambio->estado = 'cancelado';
            $intercambio->save();

            $resultado = redirect()->route('intercambios')->with('mensaje', 'El intercambio ha sido cancelado.');
        }

        // DEVOLUNCIÓN DE LA VISTA
        return $resultado;
    }
}
