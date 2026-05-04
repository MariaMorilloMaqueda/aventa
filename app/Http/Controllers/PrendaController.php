<?php

namespace App\Http\Controllers;

use App\Models\Prenda;
use App\Models\Imagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrendaController extends Controller
{
    public function irAMisPrendas(Request $request) {

        // VALIDACIÓN DE DATOS
        $usuario = $request->user();
        $titulo = "";

        // Lógica de filtrado 
        if ($usuario->esAdmin()) {
            // Si es admin, se traen todas las prendas y se carga la relación 'user' para saber de quién son
            $prendas = Prenda::with('user')->paginate(4)->appends($request->all()); //En lugar de usar get() que saca todas las prendas de golpe, se usa paginate() que muestra páginas de 4 prendas y que recuerde los filtros que aplicó el usuario.

            $titulo = "las prendas de todos los usuarios";
        } else {
            // Si es usuario normal, solo se traen las suyas
            $prendas = Prenda::where('user_id', $usuario->id)->paginate(4)->appends($request->all());
            $titulo = "tus prendas subidas";
        }

        // GENERACIÓN DE LA VISTA
        return view('privada.misprendas', ['prendas' => $prendas,'mensajePersonalizado' => $titulo]);
    }

    public function buscarPrenda(Request $request) {
        // Se busca en base de datos: se cargan las relaciones (user, imagenes) y se filtran solo las prendas disponibles
        $consulta = Prenda::with(['user', 'imagenes'])->where('disponible', true);

        // Filtro de búsqueda 
        if ($request->filled('buscar')) {
            $termino = strtolower($request->buscar); // Se pasa todo a minúsculas tanto lo introducido por el usuario como lo almacenado en la base de datos
            $consulta->where(function($subConsulta) use ($termino) {
                $subConsulta->whereRaw('LOWER(titulo) LIKE ?', ['%' . $termino . '%'])
                  ->orWhereRaw('LOWER(equipo) LIKE ?', ['%' . $termino . '%'])
                  ->orWhereRaw('LOWER(etiquetas) LIKE ?', ['%' . $termino . '%']);
            });
        }

        // Filtro por deporte
        if ($request->filled('deporte')) {
            $consulta->where('deporte', $request->deporte);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $consulta->where('estado', $request->estado);
        }

        // Ejecución de la consulta a la base de datos con los filtros aplicados
        $prendas = $consulta-> get();
        // GENERACIÓN DE LA VISTA (con los resultados obtenidos)
        return view('catalogo', ['prendas' => $prendas]);
    }

    public function irASubirPrenda() {
        return view('privada.subirprenda');
    }

    public function subirPrenda (Request $request) {

        // VALIDACIÓN DE DATOS
        $datos = $request -> validate (
            [
                'titulo' => 'required|string|min:4|max:50',
                'descripcion' => 'required|string|max:250',
                'tipo' => 'required|in:Camiseta,Calzonas,Pantalón,Sudadera,Chándal,Bufanda,Otro',
                'deporte' => 'required|string|min:4|max:50',
                'talla' => 'required|in:XS,S,M,L,XL,XXL,Única',
                'equipo' => 'required|string|min:4|max:50',
                'color' => 'required|string|min:4|max:50',
                'anio' => 'nullable|integer|min:1900|max:' . date('Y'), // Opcional, número, entre 1900 y este año
                'etiquetas' => 'nullable|string|max:255',               // Opcional, texto
                'estado' => 'required|in:Nuevo,Seminuevo,Usado',
                'imagen' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048' // <-- Validación de la imagen (máx 2MB)
            ],
            [
                    // MENSAJES PERSONALIZADOS
                    'titulo.required' => 'El título de la prenda es obligatorio.',
                    'titulo.min' => 'El título es demasiado corto (mínimo 4 caracteres).',
                    'titulo.max' => 'El título es demasiado largo (máximo 50 caracteres).',
                    
                    'descripcion.required' => 'Debes añadir una descripción a la prenda.',
                    'descripcion.max' => 'La descripción no puede superar los 250 caracteres.',
                    
                    'tipo.required' => 'Por favor, selecciona un tipo de prenda.',
                    'tipo.in' => 'El tipo de prenda seleccionado no es válido.',
                    
                    'deporte.required' => 'Es obligatorio indicar un deporte.',
                    'deporte.min' => 'El deporte es demasiado corto (mínimo 4 caracteres).',
                    'deporte.max' => 'El deporte es demasiado largo (máximo 50 caracteres).',
                    
                    'talla.required' => 'Por favor, selecciona un tipo de prenda.',
                    'talla.in' => 'La talla seleccionada no es válida.',
                    
                    'equipo.required' => 'Es obligatorio indicar un equipo.',
                    'equipo.min' => 'El equipo es demasiado corto (mínimo 4 caracteres).',
                    'equipo.max' => 'El equipo es demasiado largo (máximo 50 caracteres).',
                    
                    'color.required' => 'Es obligatorio indicar un color.',
                    'color.min' => 'El color es demasiado corto (mínimo 4 caracteres)..',
                    'color.max' => 'El color es demasiado largo (máximo 50 caracteres).',
                    
                    'anio.integer' => 'El año debe ser un número entero.',
                    'anio.min' => 'El año mínimo permitido es 1900.',
                    'anio.max' => 'El año no puede ser superior al actual.',
                    
                    'etiquetas.max' => 'Has escrito demasiadas etiquetas (máximo 255 caracteres).',
                    
                    'estado.required' => 'Por favor, selecciona un estado.',
                    'estado.in' => 'El estado seleccionado no es válido.',
                    
                    'imagen.required' => 'Es obligatorio subir una imagen de la prenda.',
                    'imagen.image' => 'El archivo debe ser una imagen real.',
                    'imagen.mimes' => 'Formatos permitidos: jpeg, png, jpg o webp.',
                    'imagen.max' => 'La imagen pesa demasiado (límite: 2MB).'
                ]
        );

        // MODIFICACIÓN DEL MODELO --> Eloquent
        if ($datos) {

            // Convertimos el texto de las etiquetas en un array.
            $arrayEtiquetas = [];
            if ($request->etiquetas) {
                // Explode separa por comas, array_map('trim') quita los espacios en blanco sobrantes
                $arrayEtiquetas = array_map('trim', explode(',', $request->etiquetas));
            }

            $prenda = new Prenda;
            $prenda->user_id = auth()->id();
            $prenda->titulo = $request->titulo;
            $prenda->descripcion = $request->descripcion;
            $prenda->tipo = $request->tipo;
            $prenda->deporte = $request->deporte;
            $prenda->talla = $request->talla;
            $prenda->equipo = $request->equipo;
            $prenda->color = $request->color;
            $prenda->anio = $request->anio;
            $prenda->etiquetas = $arrayEtiquetas; // Guardamos el array (Laravel lo pasa a JSON automáticamente)
            $prenda->estado = $request->estado;
            $prenda->disponible = true;
            $prenda->save();

            // GESTIÓN DE LA IMAGEN
            if ($request->hasFile('imagen')) {
                // Sube el archivo a storage/app/public/prendas y devuelve la ruta
                $rutaImagen = $request->file('imagen')->store('prendas', 'public');

                // Crea el registro en la base de datos de imágenes
                $imagen = new Imagen;
                $imagen->prenda_id = $prenda->id; // Se enlaza con la prenda recién creada
                $imagen->url = 'storage/' . $rutaImagen;
                $imagen->save();
            }
        }

        // GENERACIÓN DE LA VISTA
        return redirect()->route('formsubirprenda')->with('mensaje', 'Prenda guardada. ¡Sube la siguiente!');
    }

    public function borrarPrenda(Request $request, Prenda $prenda) {
        // VALIDACIÓN DE USUARIO
        // Si el usuario es el dueño de la prenda o es administrador, puede eliminarla
        if ($prenda->user_id === auth()->id() || $request->user()->esAdmin()) {

            foreach ($prenda->imagenes as $imagen) {
                // Le quitamos el 'storage/' inicial para que Laravel encuentre el archivo original
                $rutaImagen = str_replace('storage/', '', $imagen->url);
                Storage::disk('public')->delete($rutaImagen);
            }

            $prenda->imagenes()->delete(); // Se elimina la imagen
            $prenda->delete(); // Se elimina la prenda completa
            $resultado = redirect()->route('misprendas')->with('mensaje', '¡Se ha eliminado la prenda seleccionada!');

        } else {

            $resultado = redirect()->route('misprendas')->with('error', 'No puedes eliminar una prenda de otro usuario.');

        }

        // DEVOLUNCIÓN DE LA VISTA
        return $resultado;
    }

    public function irAEditarPrenda(Request $request, Prenda $prenda) {
        // VALIDACIÓN DE USUARIO
        // Si el usuario es el dueño de la prenda o es administrador, puede ir a editar
        if ($prenda->user_id === auth()->id() || $request->user()->esAdmin()) {

            $resultado = view('privada.editarprenda', ['prenda' => $prenda]);
            
        } else {
            $resultado = redirect()->route('misprendas')->with('error', 'No puedes editar una prenda de otro usuario.');
            
        }

        // DEVOLUNCIÓN DE LA VISTA
        return $resultado;
    }

    public function editarPrenda(Request $request, Prenda $prenda) {
        // VALIDACIÓN DE USUARIO
        // Si el usuario es el dueño de la prenda o es administrador, puede editarla
        if ($prenda->user_id === auth()->id() || $request->user()->esAdmin()) {

            // VALIDACIÓN DE DATOS
            $datos = $request->validate(
                [
                    'titulo' => 'required|string|min:4|max:50',
                    'descripcion' => 'required|string|max:250',
                    'tipo' => 'required|in:Camiseta,Calzonas,Pantalón,Sudadera,Chándal,Bufanda,Otro',
                    'deporte' => 'required|string|min:4|max:50',
                    'talla' => 'required|in:XS,S,M,L,XL,XXL,Única',
                    'equipo' => 'required|string|min:4|max:50',
                    'color' => 'required|string|min:4|max:50',
                    'anio' => 'nullable|integer|min:1900|max:' . date('Y'),
                    'etiquetas' => 'nullable|string|max:255',
                    'estado' => 'required|in:Nuevo,Seminuevo,Usado',
                    'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048' // Imagen ahora es opcional
                ],
                [
                    // MENSAJES PERSONALIZADOS
                    'titulo.required' => 'El título de la prenda es obligatorio.',
                    'titulo.min' => 'El título es demasiado corto (mínimo 4 caracteres).',
                    'titulo.max' => 'El título es demasiado largo (máximo 50 caracteres).',
                    
                    'descripcion.required' => 'Debes añadir una descripción a la prenda.',
                    'descripcion.max' => 'La descripción no puede superar los 250 caracteres.',
                    
                    'tipo.required' => 'Por favor, selecciona un tipo de prenda.',
                    'tipo.in' => 'El tipo de prenda seleccionado no es válido.',
                    
                    'deporte.required' => 'Es obligatorio indicar un deporte.',
                    'deporte.min' => 'El deporte es demasiado corto (mínimo 4 caracteres).',
                    'deporte.max' => 'El deporte es demasiado largo (máximo 50 caracteres).',
                    
                    'talla.required' => 'Por favor, selecciona un tipo de prenda.',
                    'talla.in' => 'La talla seleccionada no es válida.',
                    
                    'equipo.required' => 'Es obligatorio indicar un equipo.',
                    'equipo.min' => 'El equipo es demasiado corto (mínimo 4 caracteres).',
                    'equipo.max' => 'El equipo es demasiado largo (máximo 50 caracteres).',
                    
                    'color.required' => 'Es obligatorio indicar un color.',
                    'color.min' => 'El color es demasiado corto (mínimo 4 caracteres)..',
                    'color.max' => 'El color es demasiado largo (máximo 50 caracteres).',
                    
                    'anio.integer' => 'El año debe ser un número entero.',
                    'anio.min' => 'El año mínimo permitido es 1900.',
                    'anio.max' => 'El año no puede ser superior al actual.',
                    
                    'etiquetas.max' => 'Has escrito demasiadas etiquetas (máximo 255 caracteres).',
                    
                    'estado.required' => 'Por favor, selecciona un estado.',
                    'estado.in' => 'El estado seleccionado no es válido.',
                    
                    'imagen.image' => 'El archivo debe ser una imagen real.',
                    'imagen.mimes' => 'Formatos permitidos: jpeg, png, jpg o webp.',
                    'imagen.max' => 'La imagen pesa demasiado (límite: 2MB).'
                ]
            );

            // MODIFICACIÓN DEL MODELO --> Eloquent
            if ($datos) {

                // Convertimos el texto de las etiquetas en un array.
                $arrayEtiquetas = [];
                if ($request->etiquetas) {
                    // Explode separa por comas, array_map('trim') quita los espacios en blanco sobrantes
                    $arrayEtiquetas = array_map('trim', explode(',', $request->etiquetas));
                }

                // Si el request trae 'no_disponible' (ckeckbox marcado), la variable vale 2 (no disponible). Si no lo trae, vale 1.
                $disponibilidad = $request->has('no_disponible') ? 0 : 1;

                // Atualizamos los campos de la prenda ya creada
                $prenda->titulo = $request->titulo;
                $prenda->descripcion = $request->descripcion;
                $prenda->tipo = $request->tipo;
                $prenda->deporte = $request->deporte;
                $prenda->talla = $request->talla;
                $prenda->equipo = $request->equipo;
                $prenda->color = $request->color;
                $prenda->anio = $request->anio;
                $prenda->etiquetas = $arrayEtiquetas; // Guardamos el array (Laravel lo pasa a JSON automáticamente)
                $prenda->estado = $request->estado;
                $prenda->disponible = $disponibilidad;
                $prenda->save();

                // // GESTIÓN DE LA IMAGEN. ¿Ha subido una imagen nueva?
                if ($request->hasFile('imagen')) {
                    
                    // 1) Se borra la imagen física antigua del disco duro
                    foreach ($prenda->imagenes as $imagenAntigua) {
                        $rutaImagen = str_replace('storage/', '', $imagenAntigua->url);
                        Storage::disk('public')->delete($rutaImagen);
                    }

                     $prenda->imagenes()->delete(); // Se elimina la imagen

                    // 2) Se sube la nueva imagen
                    $rutaImagen = $request->file('imagen')->store('prendas', 'public');
                    
                    // 3) Se guarda la nueva ruta en la base de datos
                    $imagen = new Imagen;
                    $imagen->prenda_id = $prenda->id;
                    $imagen->url = 'storage/' . $rutaImagen;
                    $imagen->save();
                }

            }
            
            // Preparamos el mensaje de éxito
            $resultado = redirect()->route('misprendas')->with('mensaje', '¡Prenda actualizada con éxito!');

        } else {
            // Preparamos el mensaje de error si intenta hackear la URL
            $resultado = redirect()->route('misprendas')->with('error', 'No puedes editar una prenda de otro usuario.');
        }

        // DEVOLUNCIÓN DE LA VISTA
        return $resultado;
    }
}
