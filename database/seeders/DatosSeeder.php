<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Prenda;
use App\Models\Imagen;
use App\Models\Intercambio;
use App\Models\Valoracion;

class DatosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ---------------------------------------------------------
        // USUARIOS, PRENDAS E IMAGENES
        // ---------------------------------------------------------
        //Usuario 1
        if (User::where('email', 'admin@aventa.com')->count() == 0) {
            $uAdmin = new User;
            $uAdmin->name = 'Administrador Principal';
            $uAdmin->email = 'admin@aventa.com';
            $uAdmin->password = bcrypt('admin');
            $uAdmin->rol = 'admin';
            $uAdmin->email_verified_at = now();
            $uAdmin->save();
        }

        // Usario 2
        if (User::where('email', 'admin2@aventa.com')->count() == 0) {
            $uAdmin2 = new User;
            $uAdmin2->name = 'Administrador Secundario';
            $uAdmin2->email = 'admin2@aventa.com';
            $uAdmin2->password = bcrypt('admin2');
            $uAdmin2->rol = 'admin';
            $uAdmin2->email_verified_at = now();
            $uAdmin2->save();
        }

        //Empleado
        if (User::where('email', 'empleado@aventa.com')->count() == 0) {
            $uEmpleado = new User;
            $uEmpleado->name = 'Empleado Aventa';
            $uEmpleado->email = 'empleado@aventa.com';
            $uEmpleado->password = bcrypt('empleado');
            $uEmpleado->rol = 'empleado'; // Le asignamos el nuevo rol
            $uEmpleado->email_verified_at = now();
            $uEmpleado->save();
        }

        // Usuario 3
        if (User::where('email', 'sergio.moda@mail.com')->count() == 0) {
            $uSergio = new User;
            $uSergio->name = 'Sergio Moda';
            $uSergio->email = 'sergio.moda@mail.com';
            $uSergio->password = bcrypt('sergiomoda');
            $uSergio->rol = 'usuario';
            $uSergio->email_verified_at = now();
            $uSergio->save();
        }
        // Prendas del usuario 3
        if (isset($uSergio) && Prenda::where('titulo', 'Camiseta España 2010')->count() == 0) {
            $p1 = new Prenda;
            $p1->user_id = $uSergio->id;
            $p1->titulo = 'Camiseta España 2010';
            $p1->descripcion = 'Camiseta de la selección española del mundial.';
            $p1->tipo = 'Camiseta';
            $p1->deporte = 'Fútbol';
            $p1->talla = 'L';
            $p1->equipo = 'España';
            $p1->color = 'Rojo';
            $p1->anio = 2010;
            $p1->etiquetas = ['mundial', 'iniesta', 'retro'];
            $p1->estado = 'Seminuevo';
            $p1->disponible = true;
            $p1->save();

            // Imagen P1
            if (Imagen::where('prenda_id', $p1->id)->count() == 0) {
                $img1 = new Imagen;
                $img1->prenda_id = $p1->id;
                $img1->url = 'storage/prendas/espana_2010.jpeg';
                $img1->save();
            }
        }
        if (isset($uSergio) && Prenda::where('titulo', 'Camiseta Boston Celtics Bird')->count() == 0) {
            $p2 = new Prenda;
            $p2->user_id = $uSergio->id;
            $p2->titulo = 'Camiseta Boston Celtics Bird';
            $p2->descripcion = 'Camiseta de los Boston Celtics';
            $p2->tipo = 'Camiseta';
            $p2->deporte = 'Baloncesto';
            $p2->talla = 'XL';
            $p2->equipo = 'Celtics';
            $p2->color = 'Verde';
            $p2->anio = 2025;
            $p2->etiquetas = [];
            $p2->estado = 'Nuevo';
            $p2->disponible = true;
            $p2->save();

            // Imagen P2
            if (Imagen::where('prenda_id', $p2->id)->count() == 0) {
                $img2 = new Imagen;
                $img2->prenda_id = $p2->id;
                $img2->url = 'storage/prendas/celtics_retro.jpg';
                $img2->save();
            }
        }
        if (isset($uSergio) && Prenda::where('titulo', 'Bufanda Torrent FC')->count() == 0) {
            $p3 = new Prenda;
            $p3->user_id = $uSergio->id;
            $p3->titulo = 'Bufanda Torrent FC';
            $p3->descripcion = 'Bufanda animadora del Torrent FC.';
            $p3->tipo = 'Bufanda';
            $p3->deporte = 'Fútbol';
            $p3->talla = 'Única';
            $p3->equipo = 'Torrent FC';
            $p3->color = 'Naranja';
            $p3->anio = 2021;
            $p3->etiquetas = ['local', 'animacion'];
            $p3->estado = 'Nuevo';
            $p3->disponible = true;
            $p3->save();

            // Imagen P3
            if (Imagen::where('prenda_id', $p3->id)->count() == 0) {
                $img3 = new Imagen;
                $img3->prenda_id = $p3->id;
                $img3->url = 'storage/prendas/bufanda_torrentFC.png';
                $img3->save();
            }
        }

        // Usuario 4
        if (User::where('email', 'ana.villegas@mail.com')->count() == 0) {
            $uAna = new User;
            $uAna->name = 'Ana Villegas';
            $uAna->email = 'ana.villegas@mail.com';
            $uAna->password = bcrypt('anavillegas');
            $uAna->rol = 'usuario';
            $uAna->email_verified_at = now();
            $uAna->save();
        }
        // Prendas del usuario 4
        if (isset($uAna) && Prenda::where('titulo', 'Camiseta Argentina Messi 2022')->count() == 0) {
            $p4 = new Prenda;
            $p4->user_id = $uAna->id;
            $p4->titulo = 'Camiseta Argentina Messi 2022';
            $p4->descripcion = 'Camiseta de la selección argentina de Messi.';
            $p4->tipo = 'Camiseta';
            $p4->deporte = 'Futbol';
            $p4->talla = 'M';
            $p4->equipo = 'Argentina';
            $p4->color = 'Celeste';
            $p4->anio = 2022;
            $p4->etiquetas = ['mundial', 'messi'];
            $p4->estado = 'Nuevo';
            $p4->disponible = true;
            $p4->save();

            // Imagen P4
            if (Imagen::where('prenda_id', $p4->id)->count() == 0) {
                $img4 = new Imagen;
                $img4->prenda_id = $p4->id;
                $img4->url = 'storage/prendas/messi_final.jpeg';
                $img4->save();
            }
        }
        if (isset($uAna) && Prenda::where('titulo', 'Camiseta Detroit Pistons Retro')->count() == 0) {
            $p5 = new Prenda;
            $p5->user_id = $uAna->id;
            $p5->titulo = 'Camiseta Detroit Pistons Retro';
            $p5->descripcion = 'Camiseta de los Pistons';
            $p5->tipo = 'Camiseta';
            $p5->deporte = 'Baloncesto';
            $p5->talla = 'S';
            $p5->equipo = 'Pistons';
            $p5->color = 'Azul';
            $p5->anio = 1989;
            $p5->etiquetas = ['retro', 'baloncesto'];
            $p5->estado = 'Usado';
            $p5->disponible = true;
            $p5->save();

            // Imagen P5
            if (Imagen::where('prenda_id', $p5->id)->count() == 0) {
                $img5 = new Imagen;
                $img5->prenda_id = $p5->id;
                $img5->url = 'storage/prendas/pistons_89.jpg';
                $img5->save();
            }
        }
        if (isset($uAna) && Prenda::where('titulo', 'Calzonas Argentina')->count() == 0) {
            $p6 = new Prenda;
            $p6->user_id = $uAna->id;
            $p6->titulo = 'Calzonas Argentina';
            $p6->descripcion = 'Pantalones cortos de la selección.';
            $p6->tipo = 'Calzonas';
            $p6->deporte = 'Fútbol';
            $p6->talla = 'L';
            $p6->equipo = 'Argentina';
            $p6->color = 'Negro';
            $p6->anio = 2022;
            $p6->etiquetas = ['mundial'];
            $p6->estado = 'Seminuevo';
            $p6->disponible = true;
            $p6->save();

            // Imagen P6
            if (Imagen::where('prenda_id', $p6->id)->count() == 0) {
                $img6= new Imagen;
                $img6->prenda_id = $p6->id;
                $img6->url = 'storage/prendas/calzonas_argentina.webp';
                $img6->save();
            }
        }

        // Usuario 5
        if (User::where('email', 'lucas.sevilla@mail.com')->count() == 0) {
            $uLucas = new User;
            $uLucas->name = 'Lucas Sevilla';
            $uLucas->email = 'lucas.sevilla@mail.com';
            $uLucas->password = bcrypt('lucassevilla');
            $uLucas->rol = 'usuario';
            $uLucas->email_verified_at = now();
            $uLucas->save();
        }
        // Prendas del usuario 5
        if (isset($uLucas) && Prenda::where('titulo', 'Camiseta PSG Jordan Blanca')->count() == 0) {
            $p7 = new Prenda;
            $p7->user_id = $uLucas->id;
            $p7->titulo = 'Camiseta PSG Jordan Blanca';
            $p7->descripcion = 'Camiseta del PSG de la marca Jordan de la temporada 18/19';
            $p7->tipo = 'Camiseta';
            $p7->deporte = 'Futbol';
            $p7->talla = 'M';
            $p7->equipo = 'PSG';
            $p7->color = 'Blanco';
            $p7->anio = 2019;
            $p7->etiquetas = ['PSG', 'Futbol'];
            $p7->estado = 'Nuevo';
            $p7->disponible = true;
            $p7->save();

            // Imagen P7
            if (Imagen::where('prenda_id', $p7->id)->count() == 0) {
                $img7 = new Imagen;
                $img7->prenda_id = $p7->id;
                $img7->url = 'storage/prendas/psg_jordan.png';
                $img7->save();
            }
        }
        if (isset($uLucas) && Prenda::where('titulo', 'Sudadera Real Betis')->count() == 0) {
            $p8 = new Prenda;
            $p8->user_id = $uLucas->id;
            $p8->titulo = 'Sudadera Real Betis';
            $p8->descripcion = 'Sudadera de entrenamiento.';
            $p8->tipo = 'Sudadera';
            $p8->deporte = 'Fútbol';
            $p8->talla = 'XL';
            $p8->equipo = 'Real Betis';
            $p8->color = 'Verde';
            $p8->anio = 2023;
            $p8->etiquetas = ['entrenamiento'];
            $p8->estado = 'Usado';
            $p8->disponible = true;
            $p8->save();

            // Imagen p8
            if (Imagen::where('prenda_id', $p8->id)->count() == 0) {
                $img8 = new Imagen;
                $img8->prenda_id = $p8->id;
                $img8->url = 'storage/prendas/sudadera_betis.webp';
                $img8->save();
            }
        }
        if (isset($uSergio) && Prenda::where('titulo', 'Bufanda Boston Celtics')->count() == 0) {
            $p9 = new Prenda;
            $p9->user_id = $uSergio->id;
            $p9->titulo = 'Bufanda Boston Celtics';
            $p9->descripcion = 'Bufanda oficial de la NBA.';
            $p9->tipo = 'Bufanda';
            $p9->deporte = 'Baloncesto';
            $p9->talla = 'Única';
            $p9->equipo = 'Celtics';
            $p9->color = 'Verde';
            $p9->anio = 2018;
            $p9->etiquetas = ['nba'];
            $p9->estado = 'Seminuevo';
            $p9->disponible = true;
            $p9->save();

            // Imagen P9
            if (Imagen::where('prenda_id', $p9->id)->count() == 0) {
                $img9 = new Imagen;
                $img9->prenda_id = $p9->id;
                $img9->url = 'storage/prendas/bufanda_bostonCeltics.webp';
                $img9->save();
            }
        }

        // Usuario 6
        if (User::where('email', 'pepe.fonmojada@mail.com')->count() == 0) {
            $uPepe = new User;
            $uPepe->name = 'Pepe Fonmojada';
            $uPepe->email = 'pepe.fonmojada@mail.com';
            $uPepe->password = bcrypt('pepefonmojada');
            $uPepe->rol = 'usuario';
            $uPepe->activo = false;
            $uPepe->email_verified_at = now();
            $uPepe->save();
        }
        // Prendas del usuario 6
        if (isset($uPepe) && Prenda::where('titulo', 'Chándal Lakers')->count() == 0) {
            $p10 = new Prenda;
            $p10->user_id = $uPepe->id;
            $p10->titulo = 'Chándal Lakers';
            $p10->descripcion = 'Chándal completo morado y oro.';
            $p10->tipo = 'Chándal';
            $p10->deporte = 'Baloncesto';
            $p10->talla = 'M';
            $p10->equipo = 'Lakers';
            $p10->color = 'Morado';
            $p10->anio = 2020;
            $p10->etiquetas = ['lebron'];
            $p10->estado = 'Nuevo';
            $p10->disponible = true;
            $p10->save();

            // Imagen 10
            if (Imagen::where('prenda_id', $p10->id)->count() == 0) {
                $img10 = new Imagen;
                $img10->prenda_id = $p10->id;
                $img10->url = 'storage/prendas/chandal_lakers.jpg';
                $img10->save();
            }
        }

        // ---------------------------------------------------------
        // INTERCAMBIOS Y VALORACIONES
        // ---------------------------------------------------------
        // INTERCAMBIO 1: Sergio pide a Ana la prenda P4 (Argentina), ofreciendo P1 (España) -> PENDIENTE
        if (isset($uSergio) && isset($uAna) && isset($p4) && isset($p1)) {
            if (Intercambio::where('solicitante_id', $uSergio->id)->where('prenda_solicitada_id', $p4->id)->count() == 0) {
                $int1 = new Intercambio;
                $int1->solicitante_id = $uSergio->id;
                $int1->receptor_id = $uAna->id;
                $int1->prenda_solicitada_id = $p4->id;
                $int1->prenda_ofrecida_id = $p1->id;
                $int1->estado = 'Pendiente';
                $int1->save();
            }
        }

        // INTERCAMBIO 2: Lucas pide a Sergio la prenda P2 (Celtics), ofreciendo P7 (PSG) -> VALORADO
        if (isset($uLucas) && isset($uSergio) && isset($p2) && isset($p7)) {
            if (Intercambio::where('solicitante_id', $uLucas->id)->where('prenda_solicitada_id', $p2->id)->count() == 0) {
                $int2 = new Intercambio;
                $int2->solicitante_id = $uLucas->id;
                $int2->receptor_id = $uSergio->id;
                $int2->prenda_solicitada_id = $p2->id;
                $int2->prenda_ofrecida_id = $p7->id;
                $int2->estado = 'Valorado';
                $int2->save();

                // Valoraciones del Intercambio 2
                if (Valoracion::where('intercambio_id', $int2->id)->count() == 0) {
                    $v1 = new Valoracion;
                    $v1->intercambio_id = $int2->id;
                    $v1->evaluador_id = $uLucas->id;
                    $v1->puntuacion = 5;
                    $v1->comentario = 'Excelente trato con Sergio. La camiseta llegó en perfecto estado.';
                    $v1->save();

                    $v2 = new Valoracion;
                    $v2->intercambio_id = $int2->id;
                    $v2->evaluador_id = $uSergio->id;
                    $v2->puntuacion = 5;
                    $v2->comentario = 'Lucas es un usuario muy serio. Intercambio rápido y fluido.';
                    $v2->save();
                }
            }
        }

        // INTERCAMBIO 3: Ana pide a Pepe la prenda P10 (Lakers), ofreciendo P5 (Pistons) -> ACEPTADO (SIN VALORAR)
        if (isset($uAna) && isset($uPepe) && isset($p10) && isset($p5)) {
            if (Intercambio::where('solicitante_id', $uAna->id)->where('prenda_solicitada_id', $p10->id)->count() == 0) {
                $int3 = new Intercambio;
                $int3->solicitante_id = $uAna->id;
                $int3->receptor_id = $uPepe->id;
                $int3->prenda_solicitada_id = $p10->id;
                $int3->prenda_ofrecida_id = $p5->id;
                $int3->estado = 'Completado';
                $int3->save();
            }
        }

        // INTERCAMBIO 4: Pepe pide a Sergio la prenda P9 (Bufanda Celtics), ofreciendo P10 (Lakers) -> VALORADO
        if (isset($uPepe) && isset($uSergio) && isset($p9) && isset($p10)) {
            if (Intercambio::where('solicitante_id', $uPepe->id)->where('prenda_solicitada_id', $p9->id)->count() == 0) {
                $int4 = new Intercambio;
                $int4->solicitante_id = $uPepe->id;
                $int4->receptor_id = $uSergio->id;
                $int4->prenda_solicitada_id = $p9->id;
                $int4->prenda_ofrecida_id = $p10->id;
                $int4->estado = 'Valorado';
                $int4->save();

                if (Valoracion::where('intercambio_id', $int4->id)->count() == 0) {
                    $v3 = new Valoracion;
                    $v3->intercambio_id = $int4->id;
                    $v3->evaluador_id = $uPepe->id;
                    $v3->puntuacion = 4;
                    $v3->comentario = null; // SIN COMENTARIO
                    $v3->save();

                    $v4 = new Valoracion;
                    $v4->intercambio_id = $int4->id;
                    $v4->evaluador_id = $uSergio->id;
                    $v4->puntuacion = 5;
                    $v4->comentario = 'Todo genial, muy recomendable el intercambio con Pepe.';
                    $v4->save();
                }
            }
        }

        // INTERCAMBIO 5: Ana pide a Sergio la prenda P3 (Bufanda Torrent), ofreciendo P6 (Calzonas) -> VALORADO
        if (isset($uAna) && isset($uSergio) && isset($p3) && isset($p6)) {
            if (Intercambio::where('solicitante_id', $uAna->id)->where('prenda_solicitada_id', $p3->id)->count() == 0) {
                $int5 = new Intercambio;
                $int5->solicitante_id = $uAna->id;
                $int5->receptor_id = $uSergio->id;
                $int5->prenda_solicitada_id = $p3->id;
                $int5->prenda_ofrecida_id = $p6->id;
                $int5->estado = 'Valorado';
                $int5->save();

                if (Valoracion::where('intercambio_id', $int5->id)->count() == 0) {
                    $v5 = new Valoracion;
                    $v5->intercambio_id = $int5->id;
                    $v5->evaluador_id = $uAna->id;
                    $v5->puntuacion = 3;
                    $v5->comentario = null; // SIN COMENTARIO
                    $v5->save();

                    $v6 = new Valoracion;
                    $v6->intercambio_id = $int5->id;
                    $v6->evaluador_id = $uSergio->id;
                    $v6->puntuacion = 2;
                    $v6->comentario = 'La prenda de Ana tenía un pequeño hilo suelto que no se veía en la foto.';
                    $v6->save();
                }
            }
        }
    }
}
