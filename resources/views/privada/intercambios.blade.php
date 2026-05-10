@extends('plantillas.baseprivada')

@section('titulo', strtoupper('Intercambios'))

@section('contenido')

    @if (session('mensaje'))
        <div class="mensaje">
            {{ session('mensaje') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mensaje-error">
            {{ session('error') }}
        </div>
    @endif

    @auth
        <section class="seccion-comun">
            <header class="cabecera-comun">
                <h2>Hola {{ Auth::user()->name}}, {{ $mensajePersonalizado }}.</h2>
                
                <p>Mis intercambios</p>
            </header>
        </section>

        @if ($intercambios->isEmpty())
            <p>No se han efectuado intercambios.</p>
        @else
            <div class="contenedor-tabla">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Solicitante</th>
                            <th>Receptor</th>
                            <th colspan="2">Prenda solicitada</th>
                            <th colspan="2">Prenda ofrecida</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($intercambios as $intercambio)
                            <tr>
                                <td>{{ $intercambio->solicitante->name }}</td>
                                <td>{{ $intercambio->receptor->name}}</td>
                                <td>{{ $intercambio->prendaSolicitada->titulo }}</td>
                                <td>
                                    @if($intercambio->prendaSolicitada->imagenes)
                                        <img src="{{ asset($intercambio->prendaSolicitada->imagenes->url) }}" alt="{{ $intercambio->prendaSolicitada->titulo }}" class="img-miniatura">
                                    @else
                                        <span class="img-texto">Sin imagen</span>
                                    @endif
                                </td>
                                <td>{{ $intercambio->prendaOfrecida->titulo }}</td>
                                <td>
                                    @if($intercambio->prendaOfrecida->imagenes)
                                        <img src="{{ asset($intercambio->prendaOfrecida->imagenes->url) }}" alt="{{ $intercambio->prendaOfrecida->titulo }}" class="img-miniatura">
                                    @else
                                        <span class="img-texto">Sin imagen</span>
                                    @endif
                                </td>
                                <td>{{ $intercambio->estado }}</td>
                                <td>
                                    <div class="botones-misprendas">
                                        @if (strtolower($intercambio->estado) === 'completado' && !$intercambio->valoradoPorUnUsuario())
                                                <form action="{{ route('formvalorarintercambio', ['intercambio' => $intercambio->id]) }}" method="get">
                                                    <button type="submit" class="boton boton-icono" title="Valorar Intercambio">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.53a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                                        </svg>
                                                    </button>
                                                </form>

                                        @elseif (in_array(strtolower($intercambio->estado), ['cancelado', 'valorado']))

                                        @else 
                                            
                                            <form action="{{ url('/chatify/' . $intercambio->obtenerIdDelOtroUsuario()) }}" method="get">
                                                <button type="button" class="boton boton-icono" title="Chatear" 
                                                        onclick="window.open('{{ url('/chatify/' . $intercambio->obtenerIdDelOtroUsuario()) }}', 'Chat', 'width=450,height=600,scrollbars=yes,resizable=yes');">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                                                    </svg>
                                                </button>
                                            </form>

                                            @if (strtolower($intercambio->estado) === 'pendiente')
                                                @if ($intercambio->receptor_id === Auth::id())
                                                    <form action="{{ route('aceptarintercambio', $intercambio->id) }}" method="post" onsubmit="return confirm('¿Quieres aceptar este intercambio?');">
                                                        @csrf
                                                        <button type="submit" class="boton boton-icono" title="Aceptar Intercambio">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('cancelarintercambio', $intercambio->id) }}" method="post" onsubmit="return confirm('¿Estás seguro de que quieres cancelar este intercambio?');">
                                                    @csrf
                                                    <button type="submit" class="boton boton-rojo boton-icono" title="Cancelar Intercambio">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <section class="seccion-comun">
            <header class="cabecera-comun">
                <h2>Valoraciones recibidas</h2>
            </header>
        </section>
        
        @if ($valoraciones->where('evaluador_id', '!=', Auth::id())->isEmpty())
            <p>Aún no has recibido valoraciones.</p>
        @else
            <div class="contenedor-tabla">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Escrita por</th>
                            <th>Comentario</th>
                            <th>Puntuación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($valoraciones->where('evaluador_id', '!=', Auth::id()) as $valoracion)
                            <tr>
                                <td>
                                    {{-- Mostramos el nombre de quien escribió la valoración --}}
                                    {{ $valoracion->evaluador->name }}
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <section class="seccion-comun">
            <header class="cabecera-comun">
                <h2>Valoraciones realizadas</h2>
            </header>
        </section>
        
        @if ($valoraciones->where('evaluador_id', Auth::id())->isEmpty())
            <p>No has realizado ninguna valoración todavía.</p>
        @else
            <div class="contenedor-tabla">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Usuario valorado</th>
                            <th>Mi Comentario</th>
                            <th>Puntuación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($valoraciones->where('evaluador_id', Auth::id()) as $valoracion)
                            <tr>
                                <td>
                                    {{-- Mostramos el nombre de la otra persona del intercambio --}}
                                    {{ $valoracion->intercambio->solicitante_id === Auth::id() 
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endauth
@endsection