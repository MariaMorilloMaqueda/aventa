<!DOCTYPE html>
<html xmlns='http://www.w3.org/1999/xhtml' lang='es'>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>@yield('titulo', 'PÁGINA PRINCIPAL')</title>

    <!-- Se importan Tailwind y JS del autenticador de Breeze -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('public/css/estilo.css') }}?v={{ time() }}">
    
</head>
<body class="bg-gray-100">
    
    <!-- Llamamos al navegador creado por Breeze -->
    @include('layouts.navigation')

    <!-- Header extraido de Breeze para que haya consistencia visual en la navegación y cabecera -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                @yield('titulo', 'Página principal')
            </h2>
        </div>
    </header>

    <main>
        @yield('contenido')
    </main>

    <!-- JavaScript -> Función para manejar los mensajes de confirmación o error, aparecen y desaparecen progresivamente -->
    <script src="{{ asset('js/alertas.js') }}"></script>
</body>
</html>