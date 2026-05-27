<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matched Foods</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</head>
<body class="bg-[#0d0d15] text-white font-sans selection:bg-cyan-500/30 overflow-hidden relative min-h-screen">
    
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none z-0">
        <div class="absolute top-1/4 -left-20 w-72 h-72 bg-cyan-500/20 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-1/4 -right-20 w-72 h-72 bg-pink-500/20 rounded-full blur-[120px]"></div>
    </div>

    <main class="relative z-10 w-full h-full min-h-screen flex flex-col items-center justify-center p-6">
        @yield('content')
    </main>

</body>
</html>