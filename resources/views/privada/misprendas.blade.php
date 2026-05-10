@extends('plantillas.baseprivada')

{{-- CAMBIO: El título cambia si es admin o empleado --}}
@section('titulo', (auth()->user()->esAdmin() || auth()->user()->esEmpleado()) ? strtoupper('Prendas de la plataforma') : strtoupper('Mis prendas'))

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
                <h2>Hola {{ Auth::user()->name}}, estas son {{ $mensajePersonalizado }}.</h2>
            </header>
        </section>

        @if ($prendas->isEmpty())
            <p>No hay prendas disponibles.</p>
        @else
            <div class="contenedor-tabla">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Título</th>
                            <th>Descripción</th>
                            <th>Tipo</th>
                            <th>Deporte</th>
                            <th>Talla</th>
                            <th>Color</th>
                            <th>Año</th>
                            <th>Estado</th>
                            <th>Disponible</th>
                            @if (Auth::user()->esAdmin() || Auth::user()->esEmpleado()) 
                                <th>Propietario</th>
                            @endif
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prendas as $prenda)
                            <tr>
                                <td>
                                    @if($prenda->imagenes)
                                        <img src="{{ asset($prenda->imagenes->url) }}" alt="{{ $prenda->titulo }}" class="img-miniatura">
                                    @else
                                        <span class="img-texto">Sin imagen</span>
                                    @endif
                                </td>
                                <td>{{ $prenda->titulo }}</td>
                                <td>{{ $prenda->descripcion }}</td>
                                <td>{{ $prenda->tipo }}</td>
                                <td>{{ $prenda->deporte }}</td>
                                <td>{{ $prenda->talla }}</td>
                                <td>{{ $prenda->color }}</td>
                                <td>{{ $prenda->anio }}</td>
                                <td>{{ $prenda->estado }}</td>
                                <td>{{ $prenda->disponible == 1 ? 'Sí' : 'No' }}</td>
                                
                                @if (Auth::user()->esAdmin() || Auth::user()->esEmpleado()) 
                                    <td>{{ $prenda->user->name }}</td>
                                @endif
                                
                                <td>
                                    <div class="botones-misprendas">
                                        <form action="{{ route('formeditarprenda', ['prenda' => $prenda->id]) }}" method="get">
                                            <input class="boton" type="submit" value="Editar">
                                        </form>

                                        @if (Auth::user()->esAdmin() || Auth::id() === $prenda->user_id)
                                            <form action="{{route('borrarprenda',['prenda'=>$prenda->id])}}" method="post" onsubmit="return confirm('¿Estás seguro de que quieres borrar esta prenda?');">
                                                @csrf
                                                @method('DELETE')
                                                <input class="boton boton-rojo" type="submit" value="Borrar">
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div>{{ $prendas->onEachSide(1)->links() }}</div>
            </div>
        @endif
    @endauth
@endsection