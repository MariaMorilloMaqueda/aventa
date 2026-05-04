@extends('plantillas.base')

@section('titulo', strtoupper('Valoraciones de ' . $user->name))

@section('contenido')

    @if (session('error'))
        <div class="mensaje-error">
            {{ session('error') }}
        </div>
    @endif

    @if (session('mensaje'))
        <div class="mensaje">
            {{ session('mensaje') }}
        </div>
    @endif

    <section class="seccion-comun">
        <header class="cabecera-comun">
            <h2>Estas son las valoraciones recibidas de {{ $user->name }}.</h2>
        </header>
    </section>

    @if ($valoraciones->where('evaluador_id', '!=', $user->id)->isEmpty())
        <p>Este usuario no ha recibido ni ninguna valoración aún.</p>
    @else
        <div class="contenedor-tabla">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Escrita por</th>
                        <th>Comentario</th>
                        <th>Puntuación</th>
                        @if (auth()->check() && auth()->user()->esAdmin())
                            <th>Eliminar valoración</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($valoraciones->where('evaluador_id', '!=', $user->id) as $valoracion)
                        <tr>
                            <td>
                                {{ $valoracion->intercambio->solicitante_id === $user->id
                                    ? $valoracion->intercambio->receptor->name 
                                    : $valoracion->intercambio->solicitante->name 
                                }}
                            </td>
                            <td>
                                @if ($valoracion->comentario)
                                    {{ $valoracion->comentario }}
                                @else
                                    <p class="sin-comentario">Valoración sin comentario</p>
                                @endif
                            </td>
                            <td>
                                <div class="contenedor-estrellas">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $valoracion->puntuacion)
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="estrella estrella-llena">
                                                <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="estrella estrella-vacia">
                                                <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                    @endfor
                                </div>
                            </td>
                            @if (auth()->check() && auth()->user()->esAdmin())
                                <td>
                                    <form action="{{ route('borrarvaloracion', ['valoracion' => $valoracion->id]) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta valoración?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="boton boton-rojo" type="submit">
                                            Borrar
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <section class="seccion-comun">
        <header class="cabecera-comun">
            <h2>Estas son las valoraciones realizadas por {{ $user->name }}.</h2>
        </header>
    </section>

    @if ($valoraciones->where('evaluador_id', $user->id)->isEmpty())
        <p>Este usuario no ha recibido ni ninguna valoración aún.</p>
    @else

        <div class="contenedor-tabla">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Usuario valorado</th>
                        <th>Comentario</th>
                        <th>Puntuación</th>
                        @if (auth()->check() && auth()->user()->esAdmin())
                            <th>Eliminar valoración</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($valoraciones->where('evaluador_id', $user->id) as $valoracion)
                        <tr>
                            <td>
                                {{ $valoracion->intercambio->solicitante_id === $user->id
                                    ? $valoracion->intercambio->receptor->name 
                                    : $valoracion->intercambio->solicitante->name 
                                }}
                            </td>
                            <td>
                                @if ($valoracion->comentario)
                                    {{ $valoracion->comentario }}
                                @else
                                    <p class="sin-sin-comentario">Valoración sin comentario</p>
                                @endif
                            </td>
                            <td>
                               <div class="contenedor-estrellas">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $valoracion->puntuacion)
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="estrella estrella-llena">
                                                <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="estrella estrella-vacia">
                                                <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                    @endfor
                                </div>
                            </td>
                            @if (auth()->check() && auth()->user()->esAdmin())
                                <td>
                                    <form action="{{ route('borrarvaloracion', ['valoracion' => $valoracion->id]) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta valoración?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="boton boton-rojo" type="submit">
                                            Borrar
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    <div class="botones-valoraciones">
        <a href="{{ url()->previous() }}" class="enlace-boton">Volver</a>
        @if (auth()->check() && auth()->user()->esAdmin())
            <form action="{{ route('borrarusuario', ['usuario' => $user->id]) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este usuario de forma permanente?');">
                @csrf
                @method('DELETE')
                <button class="boton boton-rojo" type="submit">
                    Borrar Usuario
                </button>
            </form>
        @endif
    </div>
    
@endsection