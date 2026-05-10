@extends('plantillas.base')

@section('titulo', strtoupper('Mantenimiento del Sistema'))

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
            <h2>Gestión de Copias de Seguridad</h2>
            <p>Genera un punto de restauración manual de la base de datos y los archivos del sistema.</p>
            <div style="text-align: center; margin-top: 20px;">
                <a href="{{ route('ejecutar.backup') }}" class="boton">Realizar copia</a>
            </div>
        </header>
    </section>

@endsection