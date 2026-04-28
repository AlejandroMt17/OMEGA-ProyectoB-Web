{{--
    @file guest.blade.php
    @description Layout para vistas públicas
    @version 1.0.0
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Control de Asistencias')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="min-h-screen flex items-center justify-center bg-omg-white">

    <div class="w-full max-w-md px-4">
        @yield('content')
    </div>

    <footer class="fixed bottom-0 w-full py-4 text-center">
        <div class="flex justify-center gap-4 mb-1">
            <a href="#" class="text-xs text-omg-nile-light hover:underline">Términos de uso</a>
            <a href="#" class="text-xs text-omg-nile-light hover:underline">Aviso de privacidad</a>
            <a href="#" class="text-xs text-omg-nile-light hover:underline">Soporte técnico</a>
            <a href="#" class="text-xs text-omg-nile-light hover:underline">Preguntas frecuentes</a>
        </div>
        <p class="text-xs text-omg-dark">© 2026 OMEGA – Control de Asistencias</p>
    </footer>

    @livewireScripts
</body>
</html>