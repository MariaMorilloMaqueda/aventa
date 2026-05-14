<?php

namespace App\Http\Controllers;

use App\Models\Prenda;
use App\Models\Imagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrendaController extends Controller
{
    public function irAMisPrendas(Request $request) {

        $usuario = $request->user();
        $titulo = "";

        // CAMBIO: Si es Admin O Empleado, ven TODAS las prendas de la plataforma
        if ($usuario->esAdmin() || $usuario->esEmpleado()) {
            $prendas = Prenda::with('user')->paginate(4)->appends($request->all()); 
            $titulo = "las prendas de todos los usuarios";
        } else {
            // Si es usuario normal, solo se traen las suyas
            $prendas = Prenda::where('user_id', $usuario->id)->paginate(4)->appends($request->all());
            $titulo = "tus prendas subidas";
        }

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
        $usuarioLogueado = auth()->user();

        // VALIDACIÓN: Bloqueamos a admins y empleados. SOLO usuarios normales pueden subir ropa.
        if ($usuarioLogueado->rol !== 'usuario') {
            return redirect()->route('catalogo')->with('error', 'Solo los usuarios estándar pueden subir prendas a la plataforma.');
        }

        return view('privada.subirprenda');
    }

    public function subirPrenda (Request $request) {

        // VALIDACIÓN DE SEGURIDAD EXTRA: Por si intentan forzar la petición POST
        if (auth()->user()->rol !== 'usuario') {
            return redirect()->route('catalogo')->with('error', 'No tienes permisos para realizar esta acción.');
        }

        // VALIDACIÓN DE DATOS
        $datos = $request->validate(
            [
                // Permite letras, números, espacios, puntos, comas, guiones y barras. Prohíbe símbolos raros (@, #, €, etc)
                'titulo' => ['required', 'string', 'min:4', 'max:50', 'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\.,\-\/]+$/'],
                'descripcion' => ['required', 'string', 'max:250', 'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\.,\-\/]+$/'],
                
                'tipo' => 'required|in:Camiseta,Calzonas,Pantalón,Sudadera,Chándal,Bufanda,Otro',
                
                // Estrictos: Solo letras, espacios y guiones (sin números)
                'deporte' => ['required', 'string', 'min:4', 'max:50', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]+$/'],
                'talla' => 'required|in:XS,S,M,L,XL,XXL,Única',
                'equipo' => ['required', 'string', 'min:4', 'max:50', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]+$/'],
                'color' => ['required', 'string', 'min:4', 'max:50', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]+$/'],
                
                'anio' => 'nullable|integer|min:1900|max:' . date('Y'),
                'etiquetas' => 'nullable|string|max:255',
                'estado' => 'required|in:Nuevo,Seminuevo,Usado',
                'imagen' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048'
            ],
            [
                // MENSAJES PERSONALIZADOS
                'titulo.required' => 'El título de la prenda es obligatorio.',
                'titulo.min' => 'El título es demasiado corto.',
                'titulo.max' => 'El título es demasiado largo.',
                'titulo.regex' => 'El título contiene símbolos no permitidos. Usa solo letras, números o puntuación básica.',
                
                'descripcion.required' => 'Debes añadir una descripción a la prenda.',
                'descripcion.max' => 'La descripción no puede superar los 250 caracteres.',
                'descripcion.regex' => 'La descripción contiene símbolos no permitidos.',
                
                'tipo.required' => 'Por favor, selecciona un tipo de prenda.',
                'tipo.in' => 'El tipo de prenda seleccionado no es válido.',
                
                'deporte.required' => 'Es obligatorio indicar un deporte.',
                'deporte.min' => 'El deporte es demasiado corto.',
                'deporte.max' => 'El deporte es demasiado largo.',
                'deporte.regex' => 'El deporte solo puede contener letras, espacios y guiones.', 
                
                'talla.required' => 'Por favor, selecciona una talla.',
                'talla.in' => 'La talla seleccionada no es válida.',
                
                'equipo.required' => 'Es obligatorio indicar un equipo.',
                'equipo.min' => 'El equipo es demasiado corto.',
                'equipo.max' => 'El equipo es demasiado largo.',
                'equipo.regex' => 'El equipo solo puede contener letras, espacios y guiones.', 
                
                'color.required' => 'Es obligatorio indicar un color.',
                'color.min' => 'El color es demasiado corto.',
                'color.max' => 'El color es demasiado largo.',
                'color.regex' => 'El color solo puede contener letras, espacios y guiones.', 
                
                'anio.integer' => 'El año debe ser un número.',
                'anio.min' => 'El año mínimo es 1900.',
                'anio.max' => 'El año no puede ser del futuro.',
                
                'etiquetas.max' => 'Demasiadas etiquetas (máximo 255 caracteres).',
                
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
        
        // CAMBIO: Aquí NO metemos al empleado. 
        // Si el usuario es el dueño de la prenda o es administrador, puede eliminarla.
        // Si el empleado intenta entrar aquí, se irá al 'else' y le dará error. ¡Seguridad backend lista!
        if ($prenda->user_id === auth()->id() || $request->user()->esAdmin()) {

            if ($prenda->imagenes) {
                $rutaImagen = str_replace('storage/', '', $prenda->imagenes->url);
                Storage::disk('public')->delete($rutaImagen);
                $prenda->imagenes()->delete();
            }

            $prenda->imagenes()->delete(); 
            $prenda->delete(); 
            $resultado = redirect()->route('misprendas')->with('mensaje', '¡Se ha eliminado la prenda seleccionada!');

        } else {
            // Mensaje adaptado para atrapar a los empleados listillos
            $resultado = redirect()->route('misprendas')->with('error', 'No tienes permisos suficientes para eliminar esta prenda.');
        }

        return $resultado;
    }

    public function irAEditarPrenda(Request $request, Prenda $prenda) {
        
        // CAMBIO: El empleado SÍ puede entrar al formulario de edición para moderar
        if ($prenda->user_id === auth()->id() || $request->user()->esAdmin() || $request->user()->esEmpleado()) {
            $resultado = view('privada.editarprenda', ['prenda' => $prenda]);
        } else {
            $resultado = redirect()->route('misprendas')->with('error', 'No puedes editar una prenda de otro usuario.');
        }

        return $resultado;
    }

    public function editarPrenda(Request $request, Prenda $prenda) {
        
        // CAMBIO: El empleado SÍ puede guardar los cambios de la edición
        if ($prenda->user_id === auth()->id() || $request->user()->esAdmin() || $request->user()->esEmpleado()) {

            // VALIDACIÓN DE DATOS (Se queda exactamente igual que lo tenías)
            $datos = $request->validate([
                'titulo' => ['required', 'string', 'min:4', 'max:50', 'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\.,\-\/]+$/'],
                'descripcion' => ['required', 'string', 'max:250', 'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\.,\-\/]+$/'],
                'tipo' => 'required|in:Camiseta,Calzonas,Pantalón,Sudadera,Chándal,Bufanda,Otro',
                'deporte' => ['required', 'string', 'min:4', 'max:50', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]+$/'],
                'talla' => 'required|in:XS,S,M,L,XL,XXL,Única',
                'equipo' => ['required', 'string', 'min:4', 'max:50', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]+$/'],
                'color' => ['required', 'string', 'min:4', 'max:50', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]+$/'],
                'anio' => 'nullable|integer|min:1900|max:' . date('Y'),
                'etiquetas' => 'nullable|string|max:255',
                'estado' => 'required|in:Nuevo,Seminuevo,Usado',
                'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
            ], [
                // Mensajes estándar
                'titulo.required' => 'El título de la prenda es obligatorio.',
                'descripcion.required' => 'La descripción es obligatoria.',
                'tipo.required' => 'El tipo de prenda es obligatorio.',
                'deporte.required' => 'El deporte es obligatorio.',
                'talla.required' => 'La talla es obligatoria.',
                'equipo.required' => 'El equipo es obligatorio.',
                'color.required' => 'El color es obligatorio.',
                'estado.required' => 'El estado es obligatorio.',
                
                // MENSAJES PARA LAS EXPRESIONES REGULARES (REGEX)
                'titulo.regex' => 'El título contiene caracteres no permitidos (solo letras, números y .,-/)',
                'descripcion.regex' => 'La descripción contiene caracteres no permitidos (solo letras, números y .,-/)',
                'deporte.regex' => 'El deporte solo puede contener letras y guiones (sin números)',
                'equipo.regex' => 'El equipo solo puede contener letras y guiones (sin números)',
                'color.regex' => 'El color solo puede contener letras y guiones (sin números)',
            ]);

            // MODIFICACIÓN DEL MODELO --> Eloquent
            if ($datos) {
                $arrayEtiquetas = [];
                if ($request->etiquetas) {
                    $arrayEtiquetas = array_map('trim', explode(',', $request->etiquetas));
                }

                $disponibilidad = $request->has('no_disponible') ? 0 : 1;

                $prenda->titulo = $request->titulo;
                $prenda->descripcion = $request->descripcion;
                $prenda->tipo = $request->tipo;
                $prenda->deporte = $request->deporte;
                $prenda->talla = $request->talla;
                $prenda->equipo = $request->equipo;
                $prenda->color = $request->color;
                $prenda->anio = $request->anio;
                $prenda->etiquetas = $arrayEtiquetas; 
                $prenda->estado = $request->estado;
                $prenda->disponible = $disponibilidad;
                $prenda->save();

                if ($request->hasFile('imagen')) {
                    if ($prenda->imagenes) {
                        $rutaImagen = str_replace('storage/', '', $prenda->imagenes->url);
                        Storage::disk('public')->delete($rutaImagen);
                    }
                    $prenda->imagenes()->delete(); 

                    $rutaImagen = $request->file('imagen')->store('prendas', 'public');
                    
                    $imagen = new Imagen;
                    $imagen->prenda_id = $prenda->id;
                    $imagen->url = 'storage/' . $rutaImagen;
                    $imagen->save();
                }
            }
            
            $resultado = redirect()->route('misprendas')->with('mensaje', '¡Prenda actualizada con éxito!');

        } else {
            $resultado = redirect()->route('misprendas')->with('error', 'No tienes permisos para editar esta prenda.');
        }

        return $resultado;
    }
}
