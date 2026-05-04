<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Necesario para hacer peticiones a la API

class IAController extends Controller {
    
    public function analizarImagen(Request $request) {
        // 1. Nos aseguramos de que llega una imagen válida
        $request->validate([
            'imagen' => 'required|image|max:2048',
        ]);

        // 2. Se extrae la imagen y se convierte a texto (Base64)
        $imagen = $request->file('imagen');
        $base64 = base64_encode(file_get_contents($imagen->path()));
        $mimeType = $imagen->getClientMimeType();

        // 3. Prompt
        $prompt = "Eres un experto en moda deportiva y merchandising. Analiza esta imagen de una prenda y devuélveme ÚNICAMENTE un objeto JSON válido.
        El objeto JSON debe contener las siguientes claves exactas, con valores deducidos o sugeridos de la imagen:
        - 'descripcion': Una frase atractiva y comercial para vender la prenda (máximo 200 caracteres).
        - 'tipo': La categoría de la prenda. Elige OBLIGATORIAMENTE una de estas opciones: Camiseta, Calzonas, Pantalón, Sudadera, Chándal, Bufanda, Gorra, Otro.
        - 'color': El color predominante de la prenda.
        - 'deporte': El deporte principal al que está asociado. Deja una cadena vacía ('') si no es obvio.
        - 'equipo': El club o selección nacional. Deja una cadena vacía ('') si no tiene.
        - 'año': El año o temporada específica si es una edición especial o histórica. Deja null o una cadena vacía ('') si no es deducible.
        - 'etiquetas': Un array de PHP con 5-10 palabras clave separadas por comas que describan la prenda (ej: 'Argentina, Mundial, AFA, Retro, Albiceleste, Campeón').

        No incluyas texto de saludo, ni comillas invertidas de markdown, solo el objeto JSON puro y válido.";

        // 4. Preparación de la llamada a la API de Google Gemini (se utiliza el modelo flash ya que es el más rápido)
        $apiKey = env('GEMINI_API_KEY');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

        // 5. Se envía la petición
        $response = Http::withoutVerifying()->post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        ['inlineData' => [
                                'mimeType' => $mimeType,
                                'data' => $base64
                            ]
                        ]
                    ]
                ]
            ],
            // Se obliga a la IA a que responda estrictamente en formato JSON
            'generationConfig' => [
                'responseMimeType' => 'application/json',
            ]
        ]);

        // 6. Procesar la respuesta
        if ($response->successful()) {
            $resultado = $response->json();
            
            // Navegamos por el JSON que devuelve Google para encontrar el texto que ha escrito la IA
            $textoJson = $resultado['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            
            // Se limpian espacios y comillas raras
            $textoJson = trim($textoJson);
            $textoJson = str_replace(['```json', '```'], '', $textoJson);
            
            // Convertimos el texto en un array de PHP
            $datos = json_decode($textoJson, true);

            // Si la IA nos devuelve etiquetas como array, las convertimos a cadena separada por comas
            if (isset($datos['etiquetas']) && is_array($datos['etiquetas'])) {
                $datos['etiquetas_limpias'] = implode(', ', $datos['etiquetas']);
            } else {
                $datos['etiquetas_limpias'] = $datos['etiquetas'] ?? '';
            }

            // Se lo devolvemos al frontend (JavaScript) para que rellene el formulario
            $resultado = response()->json(['exito' => true, 'datos' => $datos]);
        } else {
            // Si la API falla o está caída, mandamos un error controlado
            $resultado = response()->json(['exito' => false, 'mensaje' => 'La IA no pudo analizar la imagen en este momento.'], 500);
        }

        return $resultado;
    }
}
