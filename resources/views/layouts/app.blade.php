<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- SEO --}}
    <title>@yield('title', 'Matched Foods — Decidid juntos dónde comer')</title>
    <meta name="description" content="@yield('description', 'Crea una sala, invita a tus amigos y votad restaurantes al estilo Tinder. El que gusta a todos, gana. Decidir dónde comer nunca fue tan rápido.')">
    <meta name="theme-color" content="#0d0d15">

    {{-- Open Graph (vista previa al compartir por WhatsApp, redes, etc.) --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Matched Foods">
    <meta property="og:title" content="@yield('title', 'Matched Foods — Decidid juntos dónde comer')">
    <meta property="og:description" content="@yield('description', 'Crea una sala, invita a tus amigos y votad restaurantes al estilo Tinder. El que gusta a todos, gana.')">
    <meta property="og:locale" content="es_ES">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

    <style>
        /* Accesibilidad: foco visible para quien navega con teclado (WCAG 2.4.7) */
        a:focus-visible,
        button:focus-visible,
        input:focus-visible {
            outline: 3px solid #22d3ee;
            outline-offset: 2px;
            border-radius: 0.5rem;
        }

        /* Accesibilidad: respeta a quien pide reducir el movimiento (WCAG 2.3.3) */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>

    @stack('head')
</head>
<body class="bg-[#0d0d15] text-white font-sans selection:bg-cyan-500/30 relative min-h-screen overflow-x-hidden">

    {{-- Enlace para saltar al contenido (lectores de pantalla / teclado) --}}
    <a href="#contenido-principal" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:bg-cyan-500 focus:text-[#0d0d15] focus:px-4 focus:py-2 focus:rounded-xl focus:font-black">
        Saltar al contenido
    </a>

    {{-- Fondos decorativos: ocultos a lectores de pantalla porque no aportan información --}}
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none z-0" aria-hidden="true">
        <div class="absolute top-1/4 -left-20 w-72 h-72 bg-cyan-500/20 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-1/4 -right-20 w-72 h-72 bg-pink-500/20 rounded-full blur-[120px]"></div>
    </div>

    <main id="contenido-principal" class="relative z-10 w-full min-h-screen flex flex-col items-center justify-center p-6">
        @yield('content')
    </main>

    {{-- AQUÍ van los scripts de cada vista (@push('scripts')). Sin esto, nada de JS funciona. --}}
    @stack('scripts')

</body>
</html>