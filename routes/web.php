<?php

use App\Http\Controllers\PrendaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IAController;
use App\Http\Controllers\IntercambioController;
use App\Http\Controllers\ValoracionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Valoracion;
use App\Models\User;

/*Route::get('/', function () {
    return view('welcome');
});*/

// Ruta pública (Visitantes)
Route::get('/', [PrendaController::class, 'buscarPrenda'])->name('paginaprincipal');

// Ruta privada (Catálogo para logueados)
Route::get('/catalogo', [PrendaController::class, 'buscarPrenda'])->middleware(['auth', 'verified'])->name('catalogo');

// Ruta a Mis prendas (un vez logueado)
Route::get('/misprendas', [PrendaController::class, 'irAMisPrendas'])->middleware('auth')->name('misprendas');

// Ruta al historial de intercambios y valoraciones
Route::get('/intercambios', [IntercambioController::class, 'irAIntercambios'])->middleware('auth')->name('intercambios');

// Ruta al historial de valoraciones de un usuario (que no es el autenticado)
Route::get('/usuario/{id}/valoraciones', function ($id) {
    
    $user = User::findOrFail($id);

    $valoraciones = Valoracion::with(['intercambio.solicitante', 'intercambio.receptor'])
        ->whereHas('intercambio', function ($consulta) use ($id) { // Funcion whereHas permite buscar directamente en el modelo valoracion
            $consulta->where('solicitante_id', $id)
                  ->orWhere('receptor_id', $id);
        })->get();

    return view('valoraciones', ['user' => $user,'valoraciones' => $valoraciones]);
    
})->name('valoracionesusuario');

// Ruta a subir una prenda
Route::get('/subirprenda', [PrendaController::class, 'irASubirPrenda'])->middleware('auth')->name('formsubirprenda');
// Acción de subir una prenda
Route::post('/subirprenda', [PrendaController::class, 'subirPrenda'])->middleware('auth')->name('prendasubida');

// Acción de eliminar una prenda
Route::delete('/misprendas/{prenda}/borrar', [PrendaController::class, 'borrarPrenda'])->middleware('auth')->name('borrarprenda');

// Ruta a editar una prenda
Route::get('/misprendas/{prenda}/editar', [PrendaController::class, 'irAEditarPrenda'])->middleware('auth')->name('formeditarprenda');
// Acción de actualizar prenda
Route::put('/misprendas/{prenda}/editar', [PrendaController::class, 'editarPrenda'])->middleware('auth')->name('actualizarprenda');

// Acción de analizar una imagen (IA).
Route::post('/api/analizar-imagen', [IAController::class, 'analizarImagen'])->middleware('auth')->name('analizarimagen');

// Ruta a iniciar un intermcabio
Route::get('/catalogo/{prenda}/intercambiar', [IntercambioController::class, 'irAIntercambiar'])->middleware('auth')->name('formintercambio');
// Acción de intercambiar
Route::post('/catalogo/{prenda}/intercambiar', [IntercambioController::class, 'iniciarIntercambio'])->middleware('auth')->name('iniciarintercambio');

// Acción de aceptar un intercambio
Route::post('/intercambios/{intercambio}/aceptar', [IntercambioController::class, 'aceptarIntercambio'])->middleware('auth')->name('aceptarintercambio');

// Acción de cancelar un intercambio
Route::post('/intercambios/{intercambio}/cancelar', [IntercambioController::class, 'cancelarIntercambio'])->middleware('auth')->name('cancelarintercambio');

// Ruta a valorar un intercambio
Route::get('/intercambios/{intercambio}/valorar', [ValoracionController::class, 'irAValorar'])->middleware('auth')->name('formvalorarintercambio');
// Acción de valorar un intercambio
Route::post('/intercambios/{intercambio}/valorar', [ValoracionController::class, 'valorarIntercambio'])->middleware('auth')->name('valorarintercambio');
// Acción de eliminar una valoración (solo para administradores)
Route::delete('/valoraciones/{valoracion}', [ValoracionController::class, 'borrarValoracion'])->middleware('auth')->name('borrarvaloracion');

// Ruta hacia gestionar usuarios
Route::get('/gestionarusuarios', [ProfileController::class, 'irAGestionUsuarios'])->middleware('auth')->name('gestionarusuarios');
//Acción de eliminar usuario
Route::delete('/usuarios/{usuario}/borrar', [ProfileController::class, 'borrarUsuario'])->name('borrarusuario');

// Ruta para activar/desactivar usuarios (Moderación)
Route::patch('/usuarios/{usuario}/toggle-activo', [ProfileController::class, 'toggleActivo'])->name('usuarios.toggleActivo')->middleware('auth');

//Autenticación de usuarios
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
