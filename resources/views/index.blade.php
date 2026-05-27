@extends('layouts.app')

@section('content')
<div class="text-center w-full max-w-md px-4 md:px-0 animate-[fadeIn_0.5s_ease-out]">
    
    <div class="relative inline-block mb-6 md:mb-8">
        <div class="absolute inset-0 bg-cyan-500/40 rounded-full blur-2xl md:blur-3xl animate-pulse"></div>
        <h1 class="text-4xl md:text-5xl font-black italic tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-pink-500 relative z-10 drop-shadow-lg">
            MATCHED FOODS
        </h1>
        <p class="text-gray-300 text-xs md:text-sm mt-1 md:mt-2 uppercase tracking-widest font-bold">Sincroniza tu hambre</p>
    </div>

    <div class="bg-[#1a1a24]/80 backdrop-blur-md p-6 md:p-8 rounded-[2rem] border border-gray-800 shadow-[0_0_50px_rgba(0,0,0,0.5)]">
        
        <div class="flex gap-2 md:gap-4 mb-6">
            <button id="btnTabGuest" onclick="switchTab('guest')" class="flex-1 py-3 font-black text-xs md:text-sm uppercase tracking-wider rounded-xl transition-all bg-cyan-500 text-[#0d0d15] shadow-[0_0_15px_rgba(34,211,238,0.4)] active:scale-95">
                Unirse
            </button>
            <button id="btnTabHost" onclick="switchTab('host')" class="flex-1 py-3 font-bold text-xs md:text-sm uppercase tracking-wider rounded-xl transition-all bg-transparent text-gray-400 hover:text-white border border-gray-700 active:scale-95">
                Crear Sala
            </button>
        </div>

        <div id="formGuest" class="space-y-3 md:space-y-4">
            <input type="text" id="inputNombreGuest" placeholder="Tu Nombre..." onkeypress="checkEnter(event, 'guest')" class="w-full bg-[#0d0d15] border border-gray-700 rounded-xl px-4 py-3 md:py-4 text-white placeholder-gray-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all text-sm md:text-base shadow-inner">
            
            <input type="text" id="inputCodigo" placeholder="CÓDIGO (Ej: A4F9)" maxlength="4" oninput="this.value = this.value.toUpperCase()" onkeypress="checkEnter(event, 'guest')" class="w-full bg-[#0d0d15] border border-gray-700 rounded-xl px-4 py-3 md:py-4 text-white text-center font-black tracking-[0.4em] md:tracking-[0.5em] uppercase placeholder-gray-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all text-sm md:text-base shadow-inner">
            
            <button id="btnJoin" onclick="unirseASala()" class="w-full py-3.5 md:py-4 mt-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-black rounded-xl transition-all transform hover:scale-[1.02] active:scale-95 uppercase tracking-widest shadow-[0_0_20px_rgba(34,211,238,0.3)] text-sm md:text-base">
                Entrar al Juego
            </button>
        </div>

        <div id="formHost" class="space-y-3 md:space-y-4 hidden">
            <input type="text" id="inputNombreHost" placeholder="Tu Nombre (Anfitrión)..." onkeypress="checkEnter(event, 'host')" class="w-full bg-[#0d0d15] border border-gray-700 rounded-xl px-4 py-3 md:py-4 text-white placeholder-gray-500 focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500 transition-all text-sm md:text-base shadow-inner">
            
            <button id="btnCreate" onclick="crearSala()" class="w-full py-3.5 md:py-4 mt-2 bg-gradient-to-r from-pink-500 to-purple-600 hover:from-pink-400 hover:to-purple-500 text-white font-black rounded-xl transition-all transform hover:scale-[1.02] active:scale-95 uppercase tracking-widest shadow-[0_0_20px_rgba(236,72,153,0.3)] text-sm md:text-base">
                Generar Código
            </button>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const API_URL = '/api';

    // 1. Limpieza de seguridad al cargar la página (Borrar partidas antiguas)
    window.addEventListener('DOMContentLoaded', () => {
        localStorage.clear();
    });

    // 2. Función para que el botón "Enter" del teclado funcione
    function checkEnter(e, type) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (type === 'host') crearSala();
            else unirseASala();
        }
    }

    // 3. Cambio de Pestañas
    function switchTab(tab) {
        const guestTab = document.getElementById('btnTabGuest');
        const hostTab = document.getElementById('btnTabHost');
        const formGuest = document.getElementById('formGuest');
        const formHost = document.getElementById('formHost');

        if (tab === 'guest') {
            guestTab.className = "flex-1 py-3 font-black text-xs md:text-sm uppercase tracking-wider rounded-xl transition-all bg-cyan-500 text-[#0d0d15] shadow-[0_0_15px_rgba(34,211,238,0.4)] active:scale-95";
            hostTab.className = "flex-1 py-3 font-bold text-xs md:text-sm uppercase tracking-wider rounded-xl transition-all bg-transparent text-gray-400 hover:text-white border border-gray-700 active:scale-95";
            formGuest.classList.remove('hidden');
            formHost.classList.add('hidden');
        } else {
            hostTab.className = "flex-1 py-3 font-black text-xs md:text-sm uppercase tracking-wider rounded-xl transition-all bg-pink-500 text-white shadow-[0_0_15px_rgba(236,72,153,0.4)] active:scale-95";
            guestTab.className = "flex-1 py-3 font-bold text-xs md:text-sm uppercase tracking-wider rounded-xl transition-all bg-transparent text-gray-400 hover:text-white border border-gray-700 active:scale-95";
            formHost.classList.remove('hidden');
            formGuest.classList.add('hidden');
        }
    }

    // Configuración base de SweetAlert para mantener el modo oscuro
    const swalDark = { background: '#1a1a24', color: '#fff', confirmButtonColor: '#06b6d4' };

    // 4. Crear Sala
    async function crearSala() {
        const nombre = document.getElementById('inputNombreHost').value.trim();
        const btn = document.getElementById('btnCreate');

        if (!nombre) return Swal.fire({ ...swalDark, title: 'Oops!', text: 'Introduce tu nombre para crear la sala', icon: 'warning' });

        btn.disabled = true;
        btn.innerText = "Creando...";

        try {
            const response = await fetch(`${API_URL}/create-room`, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json' // <-- AÑADIDO
                },
                body: JSON.stringify({ userName: nombre })
            });
            const data = await response.json();

            if (data.success) {
                localStorage.setItem('roomCode', data.roomCode);
                localStorage.setItem('roomId', data.roomId);
                localStorage.setItem('userName', nombre);
                localStorage.setItem('isHost', 'true');
                localStorage.setItem('userId', data.userId);

                window.location.replace('/lobby'); 
            } else {
                Swal.fire({ ...swalDark, title: 'Error', text: 'Error al crear la sala', icon: 'error' });
                btn.disabled = false;
                btn.innerText = "Generar Código";
            }
        } catch (error) {
            Swal.fire({ ...swalDark, title: 'Error', text: 'No se pudo conectar con el servidor', icon: 'error' });
            btn.disabled = false;
            btn.innerText = "Generar Código";
        }
    }

    // 5. Unirse a Sala
    async function unirseASala() {
        const nombre = document.getElementById('inputNombreGuest').value.trim();
        const codigo = document.getElementById('inputCodigo').value.trim().toUpperCase();
        const btn = document.getElementById('btnJoin');

        if (!nombre || !codigo) return Swal.fire({ ...swalDark, title: 'Oops!', text: 'Rellena todos los campos', icon: 'warning' });

        btn.disabled = true;
        btn.innerText = "Entrando...";

        try {
            const response = await fetch(`${API_URL}/join-room`, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json' // <-- AÑADIDO
                },
                body: JSON.stringify({ roomCode: codigo, userName: nombre })
            });
            const data = await response.json();

            if (data.success) {
                localStorage.setItem('roomCode', codigo);
                localStorage.setItem('roomId', data.roomId);
                localStorage.setItem('userName', nombre);
                localStorage.setItem('isHost', 'false');
                localStorage.setItem('userId', data.userId);

                window.location.replace('/lobby');
            } else {
                Swal.fire({ ...swalDark, title: 'Error', text: data.message || 'La sala no existe', icon: 'error' });
                btn.disabled = false;
                btn.innerText = "Entrar al Juego";
            }
        } catch (error) {
            Swal.fire({ ...swalDark, title: 'Error', text: 'No se pudo conectar con el servidor', icon: 'error' });
            btn.disabled = false;
            btn.innerText = "Entrar al Juego";
        }
    }
</script>
@endpush