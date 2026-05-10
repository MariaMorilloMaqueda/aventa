<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    // ---------------------------------------------------
    // GESTIÓN DE USUARIOS (NUEVO ROL EMPLEADO Y ADMIN)
    // ---------------------------------------------------

    // 1. Mostrar la vista con la tabla de usuarios
    public function irAGestionUsuarios(Request $request) 
    {
        $usuarioLogueado = $request->user();

        // VALIDACIÓN: Si es un usuario normal, le bloqueamos el paso
        if ($usuarioLogueado->rol === 'usuario') {
            return redirect()->route('catalogo')->with('error', 'No tienes permisos para acceder a la gestión de usuarios.');
        }

        // LÓGICA DE VISIBILIDAD DE USUARIOS
        if ($usuarioLogueado->rol === 'empleado') {
            // El empleado ve a todos los usuarios, EXCEPTO a los administradores
            $usuarios = User::where('rol', '!=', 'admin')->get();
        } else {
            // El administrador es el único que ve a todo el mundo
            $usuarios = User::all();
        }

        return view('privada.gestionarusuarios', compact('usuarios'));
    }

    // 2. Eliminar otro usuario (solo para administradores)
    public function borrarUsuario(Request $request, User $usuario) 
    {
        // VALIDACIÓN 1: El usuario logueado debe ser administrador
        if (!$request->user()->esAdmin()) {
            // Lo devolvemos a la misma tabla de gestión con un error
            $resultado = redirect()->route('gestionarusuarios')->with('error', 'Solo los administradores pueden realizar esta acción.');
        } 
        // VALIDACIÓN 2: El administrador no puede borrarse a sí mismo
        elseif ($request->user()->id === $usuario->id) {
            $resultado = redirect()->route('gestionarusuarios')->with('error', '¡No puedes eliminar tu propia cuenta de administrador!');
        }
        else {
            // Se guarda el nombre antes de que desaparezca para incluirlo en el mensaje
            $nombreBorrado = $usuario->name;
            
            $usuario->delete();
            
            // Redirigimos de nuevo a la tabla para que vea que ha desaparecido
            $resultado = redirect()->route('privada.gestionarusuarios')->with('mensaje', 'El usuario ' . $nombreBorrado . ' ha sido eliminado del sistema.');
        }

        return $resultado;
    }

    public function toggleActivo(Request $request, User $usuario)
    {
        $auth = auth()->user();

        // 1. SEGURIDAD: Solo Admin o Empleado pueden moderar
        if (!$request->user()->esAdmin() && !$request->user()->esEmpleado()) {
            return back()->with('error', 'No tienes permisos de moderación.');
        }

        // 2. SEGURIDAD: Nadie puede bloquearse a sí mismo
        if ($auth->id === $usuario->id) {
            return back()->with('error', 'No puedes suspender tu propia cuenta.');
        }

        // 3. LÓGICA: Invertimos el estado (si es true pasa a false, y viceversa)
        $usuario->activo = !$usuario->activo;
        $usuario->save();

        $mensaje = $usuario->activo 
            ? "La cuenta de {$usuario->name} ha sido reactivada." 
            : "La cuenta de {$usuario->name} ha sido suspendida temporalmente.";

        return back()->with('mensaje', $mensaje);
    }
}
