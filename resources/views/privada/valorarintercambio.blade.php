@extends('plantillas.baseprivada')

@section('titulo', strtoupper('Valorar un intercambio'))

@section('contenido')

    @if ($errors->any())
        <div class="mensaje-error">
            <p>Se han producido errores en el formulario:</p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('mensaje'))
        <div class="mensaje">
            {{ session('mensaje') }}
        </div>
    @endif

    <!-- Se hacen comprobaciones tambien en el frontend con HTML5 -->
    <section class="seccion-comun">
        <form class="formulario-catalogo formulario-prenda" action="{{ route('valorarintercambio', $intercambio->id) }}" method="post">
            @csrf
            <div class="grupo-input col-completa">
                <label>Comentario:</label>
                <textarea name="comentario" id="" maxlength="500"></textarea>
            </div>
            <div class="grupo-input">
                <label> Puntuación:</label>
                <select name="puntuacion" id="" required>
                    <option value="" selected disabled>-- Valora del 1 al 5 --</option>
                    <option value="1">1 - Muy mala</option>
                    <option value="2">2 - Mala</option>
                    <option value="3">3 - Normal</option>
                    <option value="4">4 - Buena</option>
                    <option value="5">5 - Excelente</option>
                </select>
            </div>
            <div class="boton-subir">
                <a href="{{ url()->previous()  }}" class="enlace-boton">Volver</a>
                <input  class="boton" type="submit" value="Enviar">
            </div>
        </form>
    </section>
@endsection