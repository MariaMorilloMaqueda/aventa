@extends('plantillas.baseprivada')

@section('titulo', strtoupper('Subir una prenda'))

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

    <div id="mensaje-ia"></div>

    <section class="seccion-comun">
        <form class="formulario-catalogo formulario-prenda" action="{{ route('prendasubida') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="grupo-input">
                <label for="imagen">Subir imagen:</label>
                <input type="file" id="imagen" name="imagen" accept=".jpeg, .png, .jpg, .webp" class="validar-peso" required>
            </div>

            <div class="grupo-input">
                <label for="titulo">Título:</label>
                <input 
                    type="text" 
                    id="titulo" 
                    name="titulo" 
                    minlength="4" 
                    maxlength="50" 
                    pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\.,\-\/]+" 
                    title="Solo se permiten letras, números, espacios y los caracteres . , - /" 
                    required>
            </div>

            <div class="grupo-input col-completa">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" maxlength="250" required></textarea>
            </div>

            <div class="grupo-input">
                <label for="tipo">Tipo:</label>
                <select id="tipo" name="tipo" required>
                    <option value="" selected disabled>Selecciona un tipo</option>
                    <option value="Camiseta">Camiseta</option>
                    <option value="Calzonas">Calzonas</option>
                    <option value="Pantalón">Pantalón</option>
                    <option value="Sudadera">Sudadera</option>
                    <option value="Chándal">Chándal</option>
                    <option value="Bufanda">Bufanda</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>

            <div class="grupo-input">
                <label for="deporte">Deporte:</label>
                <input 
                    type="text" 
                    id="deporte" 
                    name="deporte" 
                    minlength="4" 
                    maxlength="50" 
                    pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]+" 
                    title="Solo se permiten letras, espacios y guiones (sin números)" 
                    required>
            </div>

            <div class="grupo-input">
                <label for="talla">Talla:</label>
                <select id="talla" name="talla" required>
                    <option value="" selected disabled>Selecciona una talla</option>
                    <option value="XS">XS</option>
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                    <option value="XXL">XXL</option>
                    <option value="Única">Única</option>
                </select>
            </div>

            <div class="grupo-input">
                <label for="equipo">Equipo:</label>
                <input 
                    type="text" 
                    id="equipo" 
                    name="equipo" 
                    minlength="4" 
                    maxlength="50" 
                    pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]+" 
                    title="Solo se permiten letras, espacios y guiones (sin números)" 
                    required>
            </div>

            <div class="grupo-input">
                <label for="color">Color:</label>
                <input 
                    type="text" 
                    id="color" 
                    name="color" 
                    minlength="4" 
                    maxlength="50" 
                    pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]+" 
                    title="Solo se permiten letras, espacios y guiones (sin números)" 
                    required>
            </div>

            <div class="grupo-input">
                <label for="anio">Año:</label>
                <input type="number" id="anio" name="anio" min="1900" max="{{ date('Y') }}">
            </div>

            <div class="grupo-input col-completa">
                <label for="etiquetas">Etiquetas (separadas por comas):</label>
                <input type="text" id="etiquetas" name="etiquetas" placeholder="Ej: retro, oferta, manga larga" maxlength="255">
            </div>

            <div class="grupo-input">
                <label for="estado">Estado:</label>
                <select id="estado" name="estado" required>
                    <option value="" selected disabled>Selecciona el estado</option>
                    <option value="Nuevo">Nuevo</option>
                    <option value="Seminuevo">Seminuevo</option>
                    <option value="Usado">Usado</option>
                </select>
            </div>
            
            <div class="boton-subir">
                <input class="boton" type="submit" value="Subir">
            </div>
        </form>
    </section>

    <!-- JavaScript (AJAX) para el manejo de la IA -->
    @push('scripts')
    <script>
        // 1. Se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            
            // 2. Localización de los elementos del formulario
            let inputImagen = document.querySelector('input[name="imagen"]');
            let inputDescripcion = document.querySelector('textarea[name="descripcion"]');
            let selectTipo = document.querySelector('select[name="tipo"]');
            let inputColor = document.querySelector('input[name="color"]');
            let inputDeporte = document.querySelector('input[name="deporte"]');
            let inputEquipo = document.querySelector('input[name="equipo"]');
            let inputAnio = document.querySelector('input[name="anio"]'); 
            let inputEtiquetas = document.querySelector('input[name="etiquetas"]');
            let botonSubmit = document.querySelector('input[type="submit"]');

            // Se almacena el mensaje de éxito o error
            let mensajeIA = document.getElementById('mensaje-ia');

            // 3. Si el usuario selecciona un archivo arranca la acción
            if (inputImagen) {
                inputImagen.addEventListener('change', function() {
                    
                    // Si le dio a cancelar y no hay archivo, nos salimos
                    if (!this.files[0]) return;

                    // 4. Se modifica el botón mientras la IA está pensando
                    let textoOriginal = botonSubmit.value;
                    botonSubmit.value = "Analizando imagen...";
                    botonSubmit.disabled = true; // Se bloquea el botón para que no envíe el formulario a medias

                    // Carga el mensaje de éxito (la ia está pensando)
                    mensajeIA.innerHTML = '<div class="mensaje">Analizando la prenda con IA... Esto tomará unos segundos.</div>';
                    
                    // Se llama a la función animarMensajes declarada en la vista baseprivada
                    window.animarMensajes();

                    // 5. Preparamos el "paquete" con la foto para mandarlo al servidor
                    let formData = new FormData();
                    formData.append('imagen', this.files[0]);
                    formData.append('_token', '{{ csrf_token() }}'); // Billete de seguridad obligatorio en Laravel

                    // 6. Enviamos el paquete a nuestra ruta del IAController
                    fetch('{{ route("analizarimagen") }}', {
                        method: 'POST',
                        body: formData
                    })
                    .then(respuesta => respuesta.json()) // Se recibe la respuesta de Laravel
                    .then(data => {
                        // 7. Si la respuesta es correcta, se rellenan los campos
                        if (data.exito && data.datos) {
                            if(data.datos.descripcion) inputDescripcion.value = data.datos.descripcion;
                            if(data.datos.tipo) selectTipo.value = data.datos.tipo;
                            if(data.datos.color) inputColor.value = data.datos.color;
                            if(data.datos.deporte) inputDeporte.value = data.datos.deporte;
                            if(data.datos.equipo) inputEquipo.value = data.datos.equipo;
                            if(data.datos.año) inputAnio.value = data.datos.año;
                            if(data.datos.etiquetas_limpias) inputEtiquetas.value = data.datos.etiquetas_limpias;
                        
                            // Carga un mensaje de éxito si la ia pudo analizar la prenda
                            mensajeIA.innerHTML = '<div class="mensaje">¡Listo! Los campos se han rellenado automáticamente.</div>';
                            window.animarMensajes();
                        
                        } else {
                            // Carga un mensaje de error si la ia no pudo analizar la prenda
                            mensajeIA.innerHTML = '<div class="mensaje-error">La IA no ha podido analizar la imagen. Puedes rellenar los datos manualmente.</div>';
                            window.animarMensajes();
                        }
                    })
                    .catch(error => {
                        console.error("Error al conectar con Gemini:", error);
                        // Carga un mensaje de error si hubo problemas de conexión con la ia
                        mensajeIA.innerHTML = '<div class="mensaje-error">Hubo un problema al conectar con el servidor de IA.</div>';
                        window.animarMensajes();
                    })
                    .finally(() => {
                        // 8. Pase lo que pase (éxito o error), devolvemos el botón a la normalidad
                        botonSubmit.value = textoOriginal;
                        botonSubmit.disabled = false;
                    });
                });
            }
        });
    </script>
    @endpush
@endsection