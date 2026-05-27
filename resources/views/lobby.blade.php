@extends('layouts.app')

@section('content')
<div class="w-full max-w-4xl flex flex-col items-center justify-between min-h-[80vh] animate-[fadeIn_0.5s_ease-out] px-4 md:px-0">
    
    <header class="w-full flex flex-row justify-between items-center mb-6 border-b border-gray-800/60 pb-4 mt-4 md:mt-0 gap-2">
        <div class="flex items-center gap-2 md:gap-3">
            <div class="w-3 h-3 bg-emerald-500 rounded-full animate-ping flex-shrink-0"></div>
            <span class="text-[10px] md:text-xs uppercase tracking-widest text-gray-400 font-bold truncate">Sala Conectada</span>
        </div>
        <button onclick="abandonarSala()" class="text-[10px] md:text-xs font-bold uppercase tracking-wider text-pink-500 hover:text-pink-400 border border-pink-500/30 hover:border-pink-500 px-3 md:px-4 py-2 rounded-xl transition-all bg-pink-500/5 whitespace-nowrap flex-shrink-0">
            Salir de la sala
        </button>
    </header>

    <div id="hostView" class="w-full flex flex-col items-center text-center hidden space-y-6 md:space-y-8">
        <div class="px-2">
            <p class="text-xs md:text-sm uppercase tracking-widest text-gray-400 font-black mb-2 md:mb-4">Entra en MATCHED FOODS e introduce el código:</p>
            <div onclick="copiarCodigo()" class="cursor-pointer inline-block bg-[#1a1a24] border-2 border-cyan-500 px-8 py-4 md:px-12 md:py-6 rounded-3xl shadow-[0_0_40px_rgba(34,211,238,0.25)] hover:shadow-[0_0_50px_rgba(34,211,238,0.4)] transition-all transform hover:scale-105 group active:scale-95">
                <span id="hostCodeDisplay" class="text-5xl md:text-7xl font-black tracking-widest text-cyan-400 font-mono">----</span>
                <p class="text-[10px] md:text-xs text-gray-500 uppercase mt-2 tracking-wider group-hover:text-cyan-300 transition-all">📋 Haz clic para copiar enlace</p>
            </div>
        </div>

        <div class="bg-[#14141d] border border-gray-800 rounded-2xl px-4 py-2 md:px-6 md:py-3 flex items-center gap-3 shadow-inner">
            <span class="text-gray-400 font-bold uppercase tracking-wider text-xs md:text-sm">Participantes:</span>
            <span id="playerCount" class="bg-pink-500 text-white font-black text-base md:text-lg px-3 py-0.5 rounded-lg shadow-[0_0_10px_rgba(236,72,153,0.4)]">1</span>
        </div>

        <div class="w-full max-w-2xl bg-[#161622]/50 border border-gray-800/80 backdrop-blur-sm rounded-3xl p-4 md:p-8 min-h-[150px] md:min-h-[250px] flex flex-wrap justify-center items-center gap-3 md:gap-4 shadow-2xl" id="playersGrid">
            <div class="text-gray-600 font-medium italic animate-pulse text-sm md:text-base">Esperando a que se unan tus amigos...</div>
        </div>

        <div class="w-full max-w-sm pt-2 md:pt-4">
            <button onclick="iniciarPartida()" class="w-full py-4 md:py-5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-[#0d0d15] font-black text-lg md:text-xl rounded-2xl transition-all transform hover:scale-[1.03] active:scale-[0.97] uppercase tracking-widest shadow-[0_0_30px_rgba(16,185,129,0.3)]">
                ¡Empezar Juego!
            </button>
        </div>
    </div>

    <div id="guestView" class="w-full max-w-md flex flex-col items-center text-center hidden space-y-8 py-10 px-4">
        <div class="relative w-28 h-28 md:w-32 md:h-32 flex items-center justify-center">
            <div class="absolute inset-0 border-4 border-pink-500/20 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-t-pink-500 rounded-full animate-spin"></div>
            <span class="text-3xl md:text-4xl">🍔</span>
        </div>

        <div>
            <h2 class="text-2xl md:text-3xl font-black tracking-tight mb-2">¡Estás dentro, <span id="guestNameDisplay" class="text-pink-400">---</span>!</h2>
            <p class="text-gray-400 text-xs md:text-sm max-w-[250px] md:max-w-xs mx-auto">Mira la pantalla principal. La partida comenzará en cuanto el organizador lo decida.</p>
        </div>

        <div class="w-full bg-[#1a1a24]/90 border border-gray-800 p-4 md:p-5 rounded-2xl shadow-xl flex justify-between items-center font-semibold">
            <span class="text-gray-400 uppercase tracking-wider text-[10px] md:text-xs">Código de Sala:</span>
            <span id="guestCodeDisplay" class="text-lg md:text-xl font-black text-cyan-400 font-mono tracking-widest">----</span>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Recuperamos datos antes de ejecutar nada
    const roomCode = localStorage.getItem('roomCode');
    const roomId = localStorage.getItem('roomId');
    const userName = localStorage.getItem('userName');
    const isHost = localStorage.getItem('isHost') === 'true';
    const userId = localStorage.getItem('userId');

    // VARIABLE GLOBAL: Lista de jugadores actuales
    let jugadoresEnSala = []; 

    // Verificación de seguridad: si no hay sesión, al index.
    if (!roomCode || !roomId || !userName) {
        window.location.replace('/');
    } else {
        // Solo ejecutamos esto si hay sesión válida
        jugadoresEnSala = [userName];

        window.addEventListener('DOMContentLoaded', () => {
            // 1. Mostrar la vista correcta
            if (isHost) {
                document.getElementById('hostView').classList.remove('hidden');
                document.getElementById('hostCodeDisplay').innerText = roomCode;
                actualizarListaDePrueba(jugadoresEnSala); 
            } else {
                document.getElementById('guestView').classList.remove('hidden');
                document.getElementById('guestNameDisplay').innerText = userName;
                document.getElementById('guestCodeDisplay').innerText = roomCode;
            }

            // ==========================================
            // 2. MAGIA DE LARAVEL ECHO (WEBSOCKETS)
            // ==========================================
            if (window.Echo) {
                window.Echo.channel(`room.${roomId}`)
                    .listen('.player.joined', (e) => {
                        // Solo el Host necesita actualizar la pantalla gigante
                        if (isHost) {
                            jugadoresEnSala.push(e.playerName);
                            actualizarListaDePrueba(jugadoresEnSala);
                        }
                    })
                    .listen('.game.started', (e) => {
                        // Redirige a todos (Host y Guests) al mismo tiempo a la votación
                        window.location.href = '/votacion';
                    });
            }
        });
    }

    function actualizarListaDePrueba(listaDeNombres) {
        const grid = document.getElementById('playersGrid');
        const counter = document.getElementById('playerCount');
        
        grid.innerHTML = '';
        counter.innerText = listaDeNombres.length;

        listaDeNombres.forEach((name, index) => {
            const playerCard = document.createElement('div');
            const colors = [
                'from-pink-500 to-purple-600 shadow-pink-500/20',
                'from-cyan-500 to-blue-600 shadow-cyan-500/20',
                'from-amber-500 to-orange-600 shadow-amber-500/20',
                'from-emerald-500 to-teal-600 shadow-emerald-500/20'
            ];
            const chosenColor = colors[index % colors.length];

            // Ajustado el padding y tamaño de texto para móviles (px-4 py-2 md:px-6 md:py-3)
            playerCard.className = `bg-gradient-to-br ${chosenColor} px-4 py-2 md:px-6 md:py-3 rounded-2xl font-black text-sm md:text-lg tracking-wide text-white shadow-lg animate-[pop_0.3s_cubic-bezier(0.175,0.885,0.32,1.275)_filled] transform hover:scale-105 transition-all`;
            playerCard.innerText = name;
            grid.appendChild(playerCard);
        });
    }

    function copiarCodigo() {
        navigator.clipboard.writeText(roomCode);
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '¡Código copiado al portapapeles!',
            showConfirmButton: false,
            timer: 2000,
            background: '#1a1a24',
            color: '#fff'
        });
    }

    function abandonarSala() {
        localStorage.clear();
        window.location.replace('/');
    }

    // ==========================================
    // LÓGICA PARA INICIAR PARTIDA
    // ==========================================
    async function iniciarPartida() {
        try {
            const response = await fetch('/api/start-game', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ roomId: roomId })
            });
            
            const data = await response.json();
            
            if (!data.success) {
                Swal.fire('Error', data.message || 'No se pudo iniciar la partida', 'error');
            }
            // Si funciona, Echo escucha .game.started y redirige a todos.
        } catch (error) {
            Swal.fire('Error', 'Error de conexión al iniciar la partida', 'error');
        }
    }
</script>

<style>
    @keyframes pop {
        0% { transform: scale(0); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>
@endpush