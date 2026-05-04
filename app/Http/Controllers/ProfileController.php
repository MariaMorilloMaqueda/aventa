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

    // Eliminar otro usuario (solo para administradores)
    public function borrarUsuario(Request $request, User $usuario) {
    
        // VALIDACIÓN 1: El usuario logueado debe ser administrador
        if (!$request->user()->esAdmin()) {
            $resultado = redirect()->route('catalogo')->with('error', 'No tienes permisos para realizar esta acción.');
        } 
        // VALIDACIÓN 2: El administrador no puede borrarse a sí mismo
        elseif ($request->user()->id === $usuario->id) {
            $resultado = redirect()->route('catalogo')->with('error', '¡No puedes eliminar tu propia cuenta de administrador!');
        }
        else {
            // Se guarda el nombre antes de que desaparezca para incluirmo en el mensaje
            $nombreBorrado = $usuario->name;
            
            $usuario->delete();
            
            $resultado = redirect()->route('catalogo')->with('mensaje', 'El usuario ' . $nombreBorrado . ' ha sido eliminado del sistema.');
        }

        return $resultado;
    }
}
