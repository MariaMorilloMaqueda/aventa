@extends('plantillas.base')

@section('titulo', strtoupper('gestionar usuarios'))

@section('contenido')

    {{-- MENSAJES DE ALERTA --}}
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
            <h2>Panel de Administración: Listado de Usuarios Registrados</h2>
        </header>
    </section>

    <div class="contenedor-tabla">
        <table class="tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Fecha de Registro</th>
                    <th>Estado</th>
                    <th>Valoraciones</th>
                    <th>Acciones</th> {{-- Ahora la ven todos los moderadores --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($usuarios as $u)
                    <tr>
                        <td>{{ $u->id }}</td>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ ucfirst($u->rol) }}</td>
                        <td>{{ $u->created_at ? $u->created_at->format('d/m/Y') : 'Desconocida' }}</td>
                        
                        {{-- COLUMNA ESTADO: Solo informativa --}}
                        <td>
                            @if($u->activo)
                                <span style="color: #28a745; font-weight: bold;">Activo</span>
                            @else
                                <span style="color: #dc3545; font-weight: bold;">Suspendido</span>
                            @endif
                        </td>

                        {{-- COLUMNA VALORACIONES --}}
                        <td>
                            @if ($u->rol === 'usuario')
                                <form action="{{ route('valoracionesusuario', ['id' => $u->id]) }}" method="GET">
                                    <button class="boton" type="submit" title="Ver las valoraciones de este usuario">
                                        Ver historial
                                    </button>
                                </form>
                            @else
                                <span class="sin-comentario" style="font-style: italic; color: gray;">No aplica</span>
                            @endif
                        </td>
                        
                        {{-- COLUMNA ACCIONES: Agrupa todos los botones --}}
                        <td>
                            @if (auth()->id() !== $u->id)
                                <div class="botones-misprendas" style="display: flex; gap: 10px; align-items: center; justify-content: center;">
                                    
                                    {{-- Acción 1: Suspender/Activar (Visible para Admin y Empleado) --}}
                                    <form action="{{ route('usuarios.toggleActivo', $u->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        @if($u->activo)
                                            <button type="submit" class="boton" style="background-color: #ffc107; color: black; border: none;" title="Suspender cuenta">
                                                Suspender
                                            </button>
                                        @else
                                            <button type="submit" class="boton" style="background-color: #28a745; color: white; border: none;" title="Activar cuenta">
                                                Activar
                                            </button>
                                        @endif
                                    </form>

                                    {{-- Acción 2: Borrar (SOLO visible para Admin) --}}
                                    @if (auth()->user()->esAdmin())
                                        <form action="{{ route('borrarusuario', ['usuario' => $u->id]) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este usuario de forma permanente? Se borrarán también todas sus prendas.');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="boton boton-rojo" type="submit" title="Borrar cuenta permanentemente">
                                                Borrar
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            @else
                                <span style="color: gray; font-style: italic;">(Tú)</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="botones-valoraciones">
        <a href="{{ route('catalogo') }}" class="enlace-boton">Volver al catálogo</a>
    </div>

@endsection