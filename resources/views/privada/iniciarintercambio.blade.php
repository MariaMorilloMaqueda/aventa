@extends('plantillas.baseprivada')

@section('titulo', strtoupper('Iniciar un intercambio'))

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

    <div class="contenedor-tabla tabla-intercambiar">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Prenda</th>
                    <th>Propietario</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $prendaSolicitada->titulo }}</td>
                    <td>{{ $prendaSolicitada->user->name }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Se hacen comprobaciones tambien en el frontend con HTML5 -->
    <section class="seccion-comun">
        <form class="formulario-catalogo" action="{{ route('iniciarintercambio', $prendaSolicitada->id) }}" method="post">
            @csrf
            <div class="grupo-input">
                <label for="prenda_ofrecida_id"> Elige qué prenda quieres ofrecer a cambio:</label>
                <select name="prenda_ofrecida_id" required>
                    <option value="" selected disabled>Selecciona una de tus prendas</option>
                    @foreach ($misPrendas as $miPrenda)
                        <option value="{{ $miPrenda->id }}">
                            {{ $miPrenda->titulo }} ({{ $miPrenda->talla }})
                        </option>
                    @endforeach
                </select>
            </div>
            <input class="boton" type="submit" value="Enviar">
        </form>
    </section>
    
    <a href="{{ url()->previous()  }}" class="enlace-boton">Cancelar</a>
@endsection