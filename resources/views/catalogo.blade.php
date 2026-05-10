@extends('plantillas.base')

@section('titulo', strtoupper('Catalogo general'))

@section('contenido')

    @if (session('error'))
        <div class="mensaje-error">
            {{ session('error') }}
        </div>
    @endif

    <section class="seccion-comun">
        <header class="cabecera-comun">
            <h2>
                @auth
                    Hola {{ Auth::user()->name }}! Este es el catálogo general.
                @else
                    Hola! Este es el catálogo general.
                @endauth
            </h2>
            <p class="parrafo-catalogo">Listado de Prendas disponibles</p>
        </header>

        <form class="formulario-catalogo" action="{{ url()->current() }}" method="GET">
            
            <div class="grupo-input">
                <label for="buscar">Buscar:</label>
                <input type="text" id="buscar" name="buscar" placeholder="Título o equipo..." value="{{ request('buscar') }}">
            </div>

            <div class="grupo-input">
                <label for="deporte">Deporte:</label>
                <select id="deporte" name="deporte">
                    <option value="">Todos</option>
                    <option value="Fútbol" {{ request('deporte') == 'Fútbol' ? 'selected' : '' }}>Fútbol</option>
                    <option value="Baloncesto" {{ request('deporte') == 'Baloncesto' ? 'selected' : '' }}>Baloncesto</option>
                    <option value="Tenis" {{ request('deporte') == 'Tenis' ? 'selected' : '' }}>Tenis</option>
                    <option value="Balonmano" {{ request('deporte') == 'Balonmano' ? 'selected' : '' }}>Balonmano</option>
                </select>
            </div>

            <div class="grupo-input">
                <label for="estado">Estado:</label>
                <select id="estado" name="estado">
                    <option value="">Cualquiera</option>
                    <option value="Nuevo" {{ request('estado') == 'Nuevo' ? 'selected' : '' }}>Nuevo</option>
                    <option value="Seminuevo" {{ request('estado') == 'Seminuevo' ? 'selected' : '' }}>Seminuevo</option>
                    <option value="Usado" {{ request('estado') == 'Usado' ? 'selected' : '' }}>Usado</option>
                </select>
            </div>

            <div class="botones-catalogo">
                <button class="boton" type="submit">Filtrar</button>
                <a href="{{ url()->current() }}" class="enlace-boton">Limpiar</a>
            </div>
        </form>
    </section>

    @if ($prendas->isEmpty())
        <p>No hay coincidencias de búsqueda o no existen prendas disponibles.</p>
    @else
        <div class="grid-prendas">
            @foreach ($prendas as $prenda)
                <article class="tarjeta-prenda">
                    <div class="tarjeta-imagen">
                        @if($prenda->imagenes)
                            <img src="{{ asset($prenda->imagenes->url) }}" alt="{{ $prenda->titulo }}">
                        @else
                            <div class="tarjeta-sin-imagen">Sin imagen</div>
                        @endif
                        
                        <span class="tarjeta-estado {{ strtolower($prenda->estado) }}">{{ $prenda->estado }}</span>
                    </div>

                    <div class="tarjeta-contenido">
                        <h3 class="tarjeta-titulo">{{ $prenda->titulo }}</h3>
                        
                        <p class="tarjeta-propietario">
                            Por: <a href="{{ route('valoracionesusuario', $prenda->user->id) }}" class="enlace-usuario">{{ $prenda->user->name }}</a>
                        </p>

                        <!-- Se limita la descripción a 60 caracteres -->
                        <p class="tarjeta-descripcion">{{ Str::limit($prenda->descripcion, 60) }}</p>
                        
                        <div class="tarjeta-detalles">
                            <span>{{ $prenda->tipo }}</span> • 
                            <span>{{ $prenda->deporte }}</span> • 
                            <span>{{ $prenda->talla }}</span> • 
                            <span>{{ $prenda->color }}</span><br>
                            <span>{{ $prenda->anio }}</span>
                        </div>
                    </div>

                    @if (!auth()->check() || (!auth()->user()->esAdmin() && !auth()->user()->esEmpleado()))
                        <div class="tarjeta-acciones tarjeta-acciones-intercambio">
                            @guest
                                <form action="{{ route('register') }}" method="get" class="w-100">
                                    <button type="submit" class="boton boton-intercambio" title="Iniciar Intercambio">
                                        Intercambiar
                                    </button>
                                </form>
                            @endguest
                            
                            @auth
                                @if (Auth::id() === $prenda->user_id) 
                                    <div class="w-100">
                                        <button type="button" class="boton boton-intercambio boton-bloqueado" disabled title="Esta es tu prenda">
                                            Intercambiar
                                        </button>
                                    </div>
                                @else
                                    <form action="{{ route('formintercambio', $prenda->id) }}" method="get" class="w-100">
                                        <button type="submit" class="boton boton-intercambio" title="Iniciar Intercambio">
                                            Intercambiar
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
@endsection