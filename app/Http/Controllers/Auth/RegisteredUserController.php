<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]+$/'],
            'email' => [
                'required', 
                'string', 
                'lowercase', 
                'email', 
                'max:255', 
                'unique:'.User::class, 
                'regex:/^[\w\.\-]+@[\w\.\-]+\.[a-zA-Z]{2,}$/'
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            // MENSAJES PERSONALIZADOS
            'name.required' => 'El nombre es obligatorio.',
            'name.regex' => 'El nombre contiene símbolos no permitidos. Usa solo letras.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo debe ser válido.',
            'email.unique' => 'Este correo ya está registrado en Aventa.',
            'email.regex' => 'El correo electrónico debe tener una extensión válida (ej: @gmail.com).',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        // 2. LA CREACIÓN DEL USUARIO (Esto lo dejas tal cual lo tenías)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route('catalogo'));
        //return redirect()->intended(RouteServiceProvider::HOME); // Lo que viene por defecto en laravel
    }
}
