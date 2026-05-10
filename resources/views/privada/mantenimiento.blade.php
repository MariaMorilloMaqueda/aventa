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

    <section class="seccion-comun">
        <header class="cabecera-comun">
            <h2>Historial de copias de seguridad</h2>
        </header>
    </section>

    <div class="contenedor-tabla">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Fecha y Hora</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backups as $backup)
                    <tr>
                        <td>{{ $backup['fecha'] }}</td>
                        <td style="color: #28a745; font-weight: bold;"> Completado con éxito</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center; font-style: italic; color: gray;">
                            No se han realizado copias de seguridad todavía.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection