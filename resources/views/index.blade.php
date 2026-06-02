@extends('layouts.app')

@section('title', 'Matched Foods — Crea una sala y decidid dónde comer')
@section('description', 'Crea una sala o únete con un código de 4 letras y votad restaurantes al estilo Tinder. El que gusta a todo el grupo, gana.')

@section('content')
<div class="text-center w-full max-w-md px-4 md:px-0 animate-[fadeIn_0.5s_ease-out]">

    <header class="mb-6 md:mb-8">
        {{-- El h1 con el texto se mantiene oculto para SEO y lectores de pantalla; visualmente se muestra el logo --}}
        <h1 class="sr-only">Matched Foods</h1>
        <img src="{{ asset('images/logo.png') }}" alt="Matched Foods" class="w-56 md:w-72 mx-auto drop-shadow-[0_0_25px_rgba(34,211,238,0.35)]">
        <p class="text-gray-300 text-xs md:text-sm mt-1 md:mt-2 uppercase tracking-widest font-bold">Sincroniza tu hambre</p>
    </header>

    <div class="bg-[#1a1a24]/80 backdrop-blur-md p-6 md:p-8 rounded-[2rem] border border-gray-800 shadow-[0_0_50px_rgba(0,0,0,0.5)]">

        {{-- Pestañas accesibles --}}
        <div class="flex gap-2 md:gap-4 mb-6" role="tablist" aria-label="Elegir modo de juego">
            <button id="btnTabGuest" onclick="switchTab('guest')" role="tab" aria-selected="true" aria-controls="formGuest" class="flex-1 py-3 font-black text-xs md:text-sm uppercase tracking-wider rounded-xl transition-all bg-cyan-500 text-[#0d0d15] shadow-[0_0_15px_rgba(34,211,238,0.4)] active:scale-95">
                Unirse
            </button>
            <button id="btnTabHost" onclick="switchTab('host')" role="tab" aria-selected="false" aria-controls="formHost" class="flex-1 py-3 font-bold text-xs md:text-sm uppercase tracking-wider rounded-xl transition-all bg-transparent text-gray-400 hover:text-white border border-gray-700 active:scale-95">
                Crear Sala
            </button>
        </div>

        {{-- Formulario UNIRSE --}}
        <div id="formGuest" role="tabpanel" aria-labelledby="btnTabGuest" class="space-y-3 md:space-y-4 text-left">
            <div>
                <label for="inputNombreGuest" class="block text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-1 ml-1">Tu nombre</label>
                <input type="text" id="inputNombreGuest" placeholder="Escribe tu nombre" autocomplete="nickname" onkeypress="checkEnter(event, 'guest')" class="w-full bg-[#0d0d15] border border-gray-700 rounded-xl px-4 py-3 md:py-4 text-white placeholder-gray-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all text-sm md:text-base shadow-inner">
            </div>

            <div>
                <label for="inputCodigo" class="block text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-1 ml-1">Código de sala</label>
                <input type="text" id="inputCodigo" placeholder="A4F9" maxlength="4" inputmode="text" autocomplete="off" oninput="this.value = this.value.toUpperCase()" onkeypress="checkEnter(event, 'guest')" class="w-full bg-[#0d0d15] border border-gray-700 rounded-xl px-4 py-3 md:py-4 text-white text-center font-black tracking-[0.4em] md:tracking-[0.5em] uppercase placeholder-gray-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all text-sm md:text-base shadow-inner">
            </div>

            <button id="btnJoin" onclick="unirseASala()" class="w-full py-3.5 md:py-4 mt-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-black rounded-xl transition-all transform hover:scale-[1.02] active:scale-95 uppercase tracking-widest shadow-[0_0_20px_rgba(34,211,238,0.3)] text-sm md:text-base">
                Entrar al Juego
            </button>
        </div>

        {{-- Formulario CREAR --}}
        <div id="formHost" role="tabpanel" aria-labelledby="btnTabHost" class="space-y-3 md:space-y-4 hidden text-left" aria-hidden="true">
            <div>
                <label for="inputNombreHost" class="block text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-1 ml-1">Tu nombre (anfitrión)</label>
                <input type="text" id="inputNombreHost" placeholder="Escribe tu nombre" autocomplete="nickname" onkeypress="checkEnter(event, 'host')" class="w-full bg-[#0d0d15] border border-gray-700 rounded-xl px-4 py-3 md:py-4 text-white placeholder-gray-500 focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500 transition-all text-sm md:text-base shadow-inner">
            </div>

            <button id="btnCreate" onclick="crearSala()" class="w-full py-3.5 md:py-4 mt-2 bg-gradient-to-r from-pink-500 to-purple-600 hover:from-pink-400 hover:to-purple-500 text-white font-black rounded-xl transition-all transform hover:scale-[1.02] active:scale-95 uppercase tracking-widest shadow-[0_0_20px_rgba(236,72,153,0.3)] text-sm md:text-base">
                Generar Código
            </button>
            <p class="text-[11px] text-gray-500 leading-snug px-1">Te pediremos tu ubicación para encontrar restaurantes cerca de ti.</p>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const API_URL = '/api';

    window.addEventListener('DOMContentLoaded', () => {
        localStorage.clear();
    });

    function checkEnter(e, type) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (type === 'host') crearSala();
            else unirseASala();
        }
    }

    function switchTab(tab) {
        const guestTab = document.getElementById('btnTabGuest');
        const hostTab = document.getElementById('btnTabHost');
        const formGuest = document.getElementById('formGuest');
        const formHost = document.getElementById('formHost');

        if (tab === 'guest') {
            guestTab.className = "flex-1 py-3 font-black text-xs md:text-sm uppercase tracking-wider rounded-xl transition-all bg-cyan-500 text-[#0d0d15] shadow-[0_0_15px_rgba(34,211,238,0.4)] active:scale-95";
            hostTab.className = "flex-1 py-3 font-bold text-xs md:text-sm uppercase tracking-wider rounded-xl transition-all bg-transparent text-gray-400 hover:text-white border border-gray-700 active:scale-95";
            guestTab.setAttribute('aria-selected', 'true');
            hostTab.setAttribute('aria-selected', 'false');
            formGuest.classList.remove('hidden');
            formGuest.setAttribute('aria-hidden', 'false');
            formHost.classList.add('hidden');
            formHost.setAttribute('aria-hidden', 'true');
        } else {
            hostTab.className = "flex-1 py-3 font-black text-xs md:text-sm uppercase tracking-wider rounded-xl transition-all bg-pink-500 text-white shadow-[0_0_15px_rgba(236,72,153,0.4)] active:scale-95";
            guestTab.className = "flex-1 py-3 font-bold text-xs md:text-sm uppercase tracking-wider rounded-xl transition-all bg-transparent text-gray-400 hover:text-white border border-gray-700 active:scale-95";
            hostTab.setAttribute('aria-selected', 'true');
            guestTab.setAttribute('aria-selected', 'false');
            formHost.classList.remove('hidden');
            formHost.setAttribute('aria-hidden', 'false');
            formGuest.classList.add('hidden');
            formGuest.setAttribute('aria-hidden', 'true');
        }
    }

    const swalDark = { background: '#1a1a24', color: '#fff', confirmButtonColor: '#06b6d4' };

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function obtenerUbicacion() {
        return new Promise((resolve) => {
            if (!navigator.geolocation) {
                return resolve({ lat: null, lng: null });
            }
            navigator.geolocation.getCurrentPosition(
                (pos) => resolve({ lat: pos.coords.latitude, lng: pos.coords.longitude }),
                () => resolve({ lat: null, lng: null }),
                { timeout: 8000 }
            );
        });
    }

    async function crearSala() {
        const nombre = document.getElementById('inputNombreHost').value.trim();
        const btn = document.getElementById('btnCreate');

        if (!nombre) return Swal.fire({ ...swalDark, title: 'Oops!', text: 'Introduce tu nombre para crear la sala', icon: 'warning' });

        btn.disabled = true;
        btn.innerText = "Buscando ubicación...";

        const coords = await obtenerUbicacion();

        btn.innerText = "Creando...";

        try {
            const response = await fetch(`${API_URL}/create-room`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    userName: nombre,
                    latitude: coords.lat,
                    longitude: coords.lng
                })
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
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
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