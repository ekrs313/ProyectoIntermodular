@extends('layouts.app')

@section('content')
<div class="w-full max-w-md md:max-w-lg h-[85vh] md:h-[90vh] flex flex-col justify-between items-center relative animate-[fadeIn_0.5s_ease-out] mx-auto px-2 md:px-0">

    <header class="w-full flex justify-between items-center z-20 px-2 md:px-4 mt-4 md:mt-6">
        <div class="flex flex-col">
            <span class="text-[10px] md:text-xs uppercase tracking-widest text-cyan-400 font-bold drop-shadow-md">Sala: <span id="roomCodeDisplay" class="text-white">--</span></span>
            <span class="text-xs md:text-sm font-medium text-gray-400" id="roundDisplay">Buscando restaurante...</span>
        </div>
        <div class="bg-gray-800/80 px-4 py-2 rounded-full border border-gray-700 shadow-lg backdrop-blur-sm">
            <span class="text-xs md:text-sm font-black text-white" id="playerNameDisplay">--</span>
        </div>
    </header>

    <div class="w-full px-2 md:px-4 mb-2 flex justify-end z-20 relative mt-2">
        <a href="/ranking" class="text-[10px] md:text-xs font-bold uppercase text-cyan-400 bg-cyan-400/10 border border-cyan-400/30 px-3 py-1.5 md:py-2 rounded-lg md:rounded-xl hover:bg-cyan-400 hover:text-[#0d0d15] transition-all shadow-md active:scale-95">
            🏆 Ver Ranking Actual
        </a>
    </div>

    <div class="relative w-full h-[60vh] md:h-[65vh] flex items-center justify-center mt-2 md:mt-4 perspective-1000">
        
        <div id="loadingState" class="absolute flex flex-col items-center justify-center w-full h-full z-10 transition-all duration-300">
            <div class="w-14 h-14 md:w-16 md:h-16 border-4 border-cyan-500/30 border-t-cyan-500 rounded-full animate-spin mb-4 shadow-[0_0_15px_rgba(34,211,238,0.2)]"></div>
            <p class="text-sm md:text-base text-gray-400 font-medium animate-pulse tracking-wide" id="loadingText">Preparando el menú...</p>
        </div>

        <div id="waitingState" class="absolute flex flex-col items-center justify-center w-[95%] md:w-full h-full z-10 hidden bg-[#0d0d15]/95 backdrop-blur-md rounded-[2rem] border border-gray-800 shadow-2xl transition-all duration-300">
            <span class="text-5xl md:text-6xl mb-4 animate-bounce">⏱️</span>
            <h3 class="text-xl md:text-2xl font-black tracking-wide text-white mb-2">¡Voto registrado!</h3>
            <p class="text-gray-400 text-xs md:text-sm text-center px-6">Esperando a que el resto del grupo decida...</p>
        </div>

        <div id="restaurantCard" class="absolute w-[95%] md:w-full h-full bg-[#1a1a24] rounded-[2rem] shadow-[0_0_50px_rgba(0,0,0,0.5)] border border-gray-700 overflow-hidden hidden z-20 transform transition-transform duration-300 cursor-grab active:cursor-grabbing touch-none select-none">
            
            <div class="w-full h-[65%] bg-gray-800 relative shadow-inner">
                <div class="absolute inset-0 bg-gradient-to-t from-[#1a1a24] via-transparent to-black/20 z-10 pointer-events-none"></div>
                <img id="restImage" src="" alt="Restaurante" class="w-full h-full object-cover pointer-events-none">
                
                <div id="stampLike" class="absolute top-8 left-6 md:top-12 md:left-8 border-4 md:border-[5px] border-emerald-500 text-emerald-500 font-black text-3xl md:text-5xl px-4 py-2 rounded-xl transform -rotate-12 opacity-0 z-20 transition-opacity uppercase tracking-widest bg-black/40 backdrop-blur-sm pointer-events-none">¡SÍ!</div>
                <div id="stampNope" class="absolute top-8 right-6 md:top-12 md:right-8 border-4 md:border-[5px] border-pink-500 text-pink-500 font-black text-3xl md:text-5xl px-4 py-2 rounded-xl transform rotate-12 opacity-0 z-20 transition-opacity uppercase tracking-widest bg-black/40 backdrop-blur-sm pointer-events-none">NO</div>
            </div>
            
            <div class="w-full h-[35%] p-5 md:p-6 flex flex-col justify-end relative z-20 bg-[#1a1a24]">
                <div class="flex justify-between items-end mb-2 gap-2">
                    <h2 id="restName" class="text-2xl md:text-3xl font-black leading-tight truncate text-white shadow-black drop-shadow-md">--</h2>
                    <div class="flex items-center bg-yellow-500/20 px-3 py-1.5 rounded-full border border-yellow-500/30 flex-shrink-0">
                        <span class="text-yellow-400 text-xs md:text-sm font-bold mr-1">★</span>
                        <span id="restRating" class="text-yellow-400 font-black text-xs md:text-sm">--</span>
                    </div>
                </div>
                <p id="restAddress" class="text-gray-400 text-xs md:text-sm truncate">--</p>
            </div>
        </div>

    </div>

    <div id="actionButtons" class="w-full flex justify-center gap-10 md:gap-14 mb-8 md:mb-10 z-20 hidden opacity-0 transition-opacity duration-500">
        <button onclick="handleVote(false)" class="w-16 h-16 md:w-20 md:h-20 bg-[#1a1a24] rounded-full flex items-center justify-center border-2 border-pink-500/50 text-pink-500 text-3xl md:text-4xl hover:bg-pink-500 hover:text-white transition-all transform hover:scale-110 shadow-[0_0_20px_rgba(236,72,153,0.2)] hover:shadow-[0_0_30px_rgba(236,72,153,0.4)] active:scale-95">
            ✕
        </button>
        <button onclick="handleVote(true)" class="w-16 h-16 md:w-20 md:h-20 bg-[#1a1a24] rounded-full flex items-center justify-center border-2 border-emerald-500/50 text-emerald-500 text-3xl md:text-4xl hover:bg-emerald-500 hover:text-white transition-all transform hover:scale-110 shadow-[0_0_20px_rgba(16,185,129,0.2)] hover:shadow-[0_0_30px_rgba(16,185,129,0.4)] active:scale-95">
            ♥
        </button>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Recuperar datos de sesión
    const roomCode = localStorage.getItem('roomCode');
    const roomId = localStorage.getItem('roomId');
    const userName = localStorage.getItem('userName');
    const isHost = localStorage.getItem('isHost') === 'true';
    const userId = localStorage.getItem('userId');

    // Seguridad: Si alguien entra a la URL sin sala, lo expulsamos limpio
    if (!roomId || !userName) {
        window.location.replace('/');
    }

    let currentRestaurant = null;
    let roundNumber = 0;

    // Pintar info inicial
    document.getElementById('roomCodeDisplay').innerText = roomCode;
    document.getElementById('playerNameDisplay').innerText = userName;

    window.addEventListener('DOMContentLoaded', () => {
        
        // ==========================================
        // 1. ESCUCHAR LOS EVENTOS DEL JUEGO (ECHO)
        // ==========================================
        if (window.Echo) {
            window.Echo.channel(`room.${roomId}`)
                
                // EVENTO: NUEVA RONDA (Nuevo restaurante)
                // El servidor manda el número de ronda real, así no dependemos
                // de un contador local que se puede desincronizar.
                .listen('.new.round', (e) => {
                    currentRestaurant = e.restaurant;
                    roundNumber = e.roundNumber;
                    mostrarRestaurante(currentRestaurant, roundNumber);
                })
                
                // EVENTO: ¡MATCH ENCONTRADO!
                .listen('.match.found', (e) => {
                    localStorage.setItem('winnerRestaurant', JSON.stringify(e.restaurant));
                    window.location.replace('/match');
                });
        }

        // ==========================================
        // 2. EL HOST INICIA SOLO LA PRIMERA RONDA
        // ==========================================
        if (isHost) {
            pedirSiguienteRonda();
        } else {
            document.getElementById('loadingText').innerText = "El anfitrión está buscando restaurantes...";
        }
    });

    // --- LÓGICA DE COMUNICACIÓN CON EL SERVIDOR ---
    // Esto solo lo usa el host UNA vez para arrancar la partida.
    // El avance de las siguientes rondas lo decide el servidor.
    async function pedirSiguienteRonda() {
        try {
            await fetch('/api/next-round', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ roomId: roomId })
            });
        } catch (error) {
            console.error("Error al pedir ronda", error);
        }
    }

    async function handleVote(isLike) {
        // Bloquear doble click
        if (document.getElementById('restaurantCard').classList.contains('hidden')) return;

        // 1. Efectos visuales de salida
        animarSalidaTarjeta(isLike);
        document.getElementById('actionButtons').classList.replace('opacity-100', 'opacity-0');
        
        setTimeout(() => {
            document.getElementById('restaurantCard').classList.add('hidden');
            document.getElementById('actionButtons').classList.add('hidden');
            document.getElementById('waitingState').classList.remove('hidden');
        }, 300);

        // 2. Enviar voto al servidor.
        //    El servidor avanza la ronda y emite '.new.round' a todos
        //    en cuanto detecta que el grupo ha votado. No hay que hacer
        //    nada más desde aquí, da igual quién sea el host.
        try {
            await fetch('/api/submit-vote', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    roomId: roomId,
                    userId: userId,
                    restaurant: currentRestaurant,
                    isLike: isLike
                })
            });
        } catch (error) {
            Swal.fire({
                title: 'Error',
                text: 'Fallo al enviar el voto. Revisa tu conexión.',
                icon: 'error',
                background: '#1a1a24',
                color: '#fff'
            });
        }
    }

    // --- LÓGICA DE INTERFAZ Y ANIMACIONES ---
    function mostrarRestaurante(restaurant, round) {
        document.getElementById('roundDisplay').innerText = `Ronda ${round}`;
        
        document.getElementById('restName').innerText = restaurant.name;
        document.getElementById('restAddress').innerText = restaurant.address || 'Sin dirección';
        document.getElementById('restRating').innerText = restaurant.rating;
        
        if (restaurant.photo_reference) {
            // La foto se pide a NUESTRO backend, que es quien tiene la API key.
            document.getElementById('restImage').src = `/api/photo/${encodeURIComponent(restaurant.photo_reference)}`;
        } else {
            document.getElementById('restImage').src = 'https://via.placeholder.com/800x600/1a1a24/ffffff?text=Sin+Imagen';
        }

        const card = document.getElementById('restaurantCard');
        card.style.transform = '';
        card.className = "absolute w-[95%] md:w-full h-full bg-[#1a1a24] rounded-[2rem] shadow-[0_0_50px_rgba(0,0,0,0.5)] border border-gray-700 overflow-hidden z-20 cursor-grab active:cursor-grabbing touch-none select-none";
        
        document.getElementById('stampLike').style.opacity = '0';
        document.getElementById('stampNope').style.opacity = '0';

        document.getElementById('loadingState').classList.add('hidden');
        document.getElementById('waitingState').classList.add('hidden');
        
        card.classList.remove('hidden');
        
        const buttons = document.getElementById('actionButtons');
        buttons.classList.remove('hidden');
        setTimeout(() => buttons.classList.replace('opacity-0', 'opacity-100'), 100);
    }

    function animarSalidaTarjeta(isLike) {
        const card = document.getElementById('restaurantCard');
        card.style.transition = 'transform 0.5s ease-out, opacity 0.3s ease-out';
        
        if (isLike) {
            card.style.transform = 'translateX(100vw) rotate(30deg)';
        } else {
            card.style.transform = 'translateX(-100vw) rotate(-30deg)';
        }
        card.style.opacity = '0';
    }

    // --- LÓGICA DE SWIPE (ARRASTRE TÁCTIL) ESTILO TINDER ---
    const card = document.getElementById('restaurantCard');
    let startX = 0, currentX = 0, isDragging = false;

    card.addEventListener('touchstart', dragStart, {passive: true});
    card.addEventListener('mousedown', dragStart);

    function dragStart(e) {
        isDragging = true;
        startX = e.type.includes('mouse') ? e.pageX : e.touches[0].clientX;
        card.style.transition = 'none'; 
    }

    window.addEventListener('touchmove', dragMove, {passive: false});
    window.addEventListener('mousemove', dragMove);

    function dragMove(e) {
        if (!isDragging) return;
        const x = e.type.includes('mouse') ? e.pageX : e.touches[0].clientX;
        currentX = x - startX;
        
        const rotate = currentX * 0.05;
        card.style.transform = `translateX(${currentX}px) rotate(${rotate}deg)`;

        if (currentX > 50) {
            document.getElementById('stampLike').style.opacity = Math.min(currentX / 100, 1);
            document.getElementById('stampNope').style.opacity = '0';
        } else if (currentX < -50) {
            document.getElementById('stampNope').style.opacity = Math.min(Math.abs(currentX) / 100, 1);
            document.getElementById('stampLike').style.opacity = '0';
        }
    }

    window.addEventListener('touchend', dragEnd);
    window.addEventListener('mouseup', dragEnd);

    function dragEnd() {
        if (!isDragging) return;
        isDragging = false;

        if (currentX > 100) {
            handleVote(true);
        } else if (currentX < -100) {
            handleVote(false);
        } else {
            card.style.transition = 'transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            card.style.transform = 'translateX(0px) rotate(0deg)';
            document.getElementById('stampLike').style.opacity = '0';
            document.getElementById('stampNope').style.opacity = '0';
        }
        currentX = 0;
    }
</script>
@endpush