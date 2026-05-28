@extends('layouts.app')

@section('content')
<div class="relative z-10 text-center max-w-md w-full px-4 md:px-0 animate-[fadeIn_0.5s_ease-out]">
    
    <div class="animate-bounce mb-4 md:mb-6">
        <h1 class="text-5xl md:text-6xl font-black italic tracking-tighter text-white drop-shadow-[0_0_15px_rgba(57,255,20,0.8)]">¡BINGO!</h1>
        <p class="text-lg md:text-xl text-gray-300 font-bold tracking-widest uppercase mt-2">¡Tenemos un Match!</p>
    </div>

    <div class="bg-[#1a1a24]/90 backdrop-blur-md p-5 md:p-8 rounded-3xl border-2 border-[#39ff14]/50 shadow-[0_0_50px_rgba(57,255,20,0.2)] mt-6 transform transition-all hover:scale-105">
        
        <div class="w-full h-40 md:h-52 bg-gray-800 rounded-xl overflow-hidden relative mb-4 md:mb-6 shadow-inner">
            <img id="winnerImage" src="" alt="Restaurante Ganador" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-[#1a1a24] via-transparent to-transparent"></div>
        </div>
        
        <h2 id="winnerName" class="text-2xl md:text-3xl font-black text-white mb-2 leading-tight">Cargando...</h2>
        <p id="winnerAddress" class="text-gray-400 text-xs md:text-sm mb-6 md:mb-8 px-2">Ubicación...</p>

        <a id="btnMaps" href="#" target="_blank" class="block w-full py-4 md:py-5 bg-gradient-to-r from-[#39ff14] to-emerald-500 hover:from-emerald-400 hover:to-green-500 text-[#0d0d15] font-black text-base md:text-lg rounded-xl transition-all shadow-[0_0_20px_rgba(57,255,20,0.4)] hover:shadow-[0_0_30px_rgba(57,255,20,0.6)] uppercase tracking-widest active:scale-95">
            📍 Cómo llegar
        </a>
    </div>

    <button onclick="volverAlInicio()" class="mt-8 md:mt-10 text-xs md:text-sm font-bold uppercase tracking-wider text-gray-500 hover:text-white transition-all underline decoration-gray-500 hover:decoration-white underline-offset-4">
        Volver al inicio (Nueva Partida)
    </button>
</div>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
@endsection

@push('scripts')
<script>
    window.addEventListener('DOMContentLoaded', () => {
        
        // Recuperar el restaurante ganador de la sesión
        const winnerData = localStorage.getItem('winnerRestaurant');
        
        // Si no hay ganador guardado (alguien entró directo a la URL), lo echamos al inicio
        if (!winnerData) {
            window.location.replace('/');
            return;
        }

        // 1. ¡Disparar confeti! (Solo si hay un ganador real)
        confetti({
            particleCount: 150,
            spread: 80,
            origin: { y: 0.6 },
            colors: ['#39ff14', '#22d3ee', '#ec4899']
        });
        
        const restaurant = JSON.parse(winnerData);
        
        document.getElementById('winnerName').innerText = restaurant.name;
        document.getElementById('winnerAddress').innerText = restaurant.address || 'Sin dirección';
        
        if (restaurant.photo_reference) {
            // La foto se pide a NUESTRO backend, que es quien tiene la API key.
            document.getElementById('winnerImage').src = `/api/photo/${encodeURIComponent(restaurant.photo_reference)}`;
        } else {
            document.getElementById('winnerImage').src = 'https://via.placeholder.com/800x400/1a1a24/ffffff?text=Sin+Imagen';
        }

        // Crear enlace OFICIAL para abrir en la app de Google Maps o en la web
        const query = encodeURIComponent(`${restaurant.name} ${restaurant.address || ''}`);
        document.getElementById('btnMaps').href = `https://www.google.com/maps/search/?api=1&query=${query}`;
        
    });

    function volverAlInicio() {
        // Limpiamos los datos del juego actual para que la próxima partida esté limpia
        localStorage.clear();
        window.location.href = '/';
    }
</script>
@endpush