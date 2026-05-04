@extends('plantillas.baseprivada')

@section('titulo', strtoupper('Edita tu prenda'))

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

    <section class="seccion-comun">
        <!-- Se hacen comprobaciones tambien en el frontend con HTML5 -->
        <form class="formulario-catalogo formulario-prenda" action="{{ route('actualizarprenda', ['prenda' => $prenda->id]) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <!-- Se añade JavaScript para validar el tamaño de los archivos mediante la clase validar-archivo -->
            <div class="grupo-input">
                <label for="imagen">Cambiar imagen (Opcional):</label>
                <input type="file" id="imagen" name="imagen" accept=".jpeg, .png, .jpg, .webp" class="validar-peso">
            </div>

            <div class="grupo-input">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" value="{{ old('titulo', $prenda->titulo) }}" minlength="4" maxlength="50" required>
            </div>

            <div class="grupo-input col-completa">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" maxlength="250" required>{{ old('descripcion', $prenda->descripcion) }}</textarea>
            </div>

            <div class="grupo-input">
                <label for="tipo">Tipo:</label>
                <select id="tipo" name="tipo" required>
                    <option value="Camiseta" {{ old('tipo', $prenda->tipo) == 'Camiseta' ? 'selected' : '' }}>Camiseta</option>
                    <option value="Calzonas" {{ old('tipo', $prenda->tipo) == 'Calzonas' ? 'selected' : '' }}>Calzonas</option>
                    <option value="Pantalón" {{ old('tipo', $prenda->tipo) == 'Pantalón' ? 'selected' : '' }}>Pantalón</option>
                    <option value="Sudadera" {{ old('tipo', $prenda->tipo) == 'Sudadera' ? 'selected' : '' }}>Sudadera</option>
                    <option value="Chándal" {{ old('tipo', $prenda->tipo) == 'Chándal' ? 'selected' : '' }}>Chándal</option>
                    <option value="Bufanda" {{ old('tipo', $prenda->tipo) == 'Bufanda' ? 'selected' : '' }}>Bufanda</option>
                    <option value="Otro" {{ old('tipo', $prenda->tipo) == 'Otro' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>

            <div class="grupo-input">
                <label for="deporte">Deporte:</label>
                <input type="text" id="deporte" name="deporte" value="{{ old('deporte', $prenda->deporte) }}" minlength="4" maxlength="50" required>
            </div>

            <div class="grupo-input">
                <label for="talla">Talla:</label>
                <select id="talla" name="talla" required>
                    <option value="XS" {{ old('talla', $prenda->talla) == 'XS' ? 'selected' : '' }}>XS</option>
                    <option value="S" {{ old('talla', $prenda->talla) == 'S' ? 'selected' : '' }}>S</option>
                    <option value="M" {{ old('talla', $prenda->talla) == 'M' ? 'selected' : '' }}>M</option>
                    <option value="L" {{ old('talla', $prenda->talla) == 'L' ? 'selected' : '' }}>L</option>
                    <option value="XL" {{ old('talla', $prenda->talla) == 'XL' ? 'selected' : '' }}>XL</option>
                    <option value="XXL" {{ old('talla', $prenda->talla) == 'XXL' ? 'selected' : '' }}>XXL</option>
                    <option value="Única" {{ old('talla', $prenda->talla) == 'Única' ? 'selected' : '' }}>Única</option>
                </select>
            </div>

            <div class="grupo-input">
                <label for="equipo">Equipo:</label>
                <input type="text" id="equipo" name="equipo" value="{{ old('equipo', $prenda->equipo) }}" minlength="4" maxlength="50" required>
            </div>

            <div class="grupo-input">
                <label for="color">Color:</label>
                <input type="text" id="color" name="color" value="{{ old('color', $prenda->color) }}" minlength="4" maxlength="50" required>
            </div>

            <div class="grupo-input">
                <label for="anio">Año:</label>
                <input type="number" id="anio" name="anio" value="{{ old('anio', $prenda->anio) }}" min="1900" max="{{ date('Y') }}">
            </div>

            <div class="grupo-input col-completa">
                <label for="etiquetas">Etiquetas (separadas por comas):</label>
                <input type="text" id="etiquetas" name="etiquetas" placeholder="Ej: retro, oferta, manga larga" value="{{ old('etiquetas', is_array($prenda->etiquetas) ? implode(', ', $prenda->etiquetas) : $prenda->etiquetas) }}" maxlength="255">
            </div>

            <div class="grupo-input">
                <label for="estado">Estado:</label>
                <select id="estado" name="estado" required>
                    <option value="Nuevo" {{ old('estado', $prenda->estado) == 'Nuevo' ? 'selected' : '' }}>Nuevo</option>
                    <option value="Seminuevo" {{ old('estado', $prenda->estado) == 'Seminuevo' ? 'selected' : '' }}>Seminuevo</option>
                    <option value="Usado" {{ old('estado', $prenda->estado) == 'Usado' ? 'selected' : '' }}>Usado</option>
                </select>
            </div>

            <div class="grupo-input input-disponible">
                <label for="no_disponible">Marcar como no disponible:</label>
                <input class="no-disponible" type="checkbox" id="no_disponible" name="no_disponible" value="0" {{ old('no_disponible', $prenda->disponible) == 0 ? 'checked' : '' }}>
            </div>

            <div class="boton-subir">
                <a href="{{ url()->previous() }}" class="enlace-boton">Volver</a>
                <input type="submit" class="boton" value="Guardar Cambios">
            </div>
        </form>
    </section>
@endsection