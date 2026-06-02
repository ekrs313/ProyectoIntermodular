@extends('layouts.app')

@section('title', 'Ranking de restaurantes — Matched Foods')

@section('content')
<div class="w-full max-w-md md:max-w-2xl min-h-[85vh] flex flex-col items-center animate-[fadeIn_0.5s_ease-out] pb-10 px-4 md:px-0">

    <header class="w-full flex justify-between items-center mb-6 md:mb-10 mt-4">
        <h1 class="text-3xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500 italic uppercase drop-shadow-md">Ranking</h1>
        <div class="text-[10px] md:text-xs text-gray-300 uppercase tracking-widest bg-gray-800/80 px-3 md:px-4 py-1.5 md:py-2 rounded-full border border-gray-600 shadow-lg">
            Top Favoritos
        </div>
    </header>

    <div id="rankingContainer" class="w-full space-y-4 md:space-y-6" aria-live="polite" aria-label="Clasificación de restaurantes más votados">
        <div class="flex flex-col items-center justify-center py-10 opacity-50 animate-pulse" role="status">
            <span class="text-4xl md:text-5xl mb-3" aria-hidden="true">📊</span>
            <p class="text-sm md:text-base font-medium uppercase tracking-widest text-gray-400">Calculando votos...</p>
        </div>
    </div>

    <div class="mt-10 md:mt-12 w-full max-w-md space-y-4">
        <button onclick="abandonarPartida()" class="w-full py-4 md:py-5 border-2 border-pink-500/50 text-pink-500 hover:bg-pink-500 hover:text-white font-black rounded-xl md:rounded-2xl transition-all uppercase tracking-widest text-sm md:text-base shadow-[0_0_15px_rgba(236,72,153,0.1)] hover:shadow-[0_0_25px_rgba(236,72,153,0.3)] active:scale-95">
            Cerrar y salir
        </button>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const roomId = localStorage.getItem('roomId');
    const isHost = localStorage.getItem('isHost') === 'true';

    if (!roomId) {
        window.location.replace('/');
    }

    window.addEventListener('DOMContentLoaded', () => {
        cargarRanking();
    });

    async function cargarRanking() {
        try {
            const response = await fetch(`/api/ranking/${roomId}`);
            const data = await response.json();

            const container = document.getElementById('rankingContainer');
            container.innerHTML = '';

            if (!data.success || data.ranking.length === 0) {
                container.innerHTML = `
                    <div class="bg-[#1a1a24]/80 backdrop-blur-sm border border-gray-800 rounded-3xl p-8 md:p-12 text-center mt-6 md:mt-10 shadow-2xl">
                        <span class="text-5xl md:text-6xl mb-4 block" aria-hidden="true">🏜️</span>
                        <p class="text-gray-400 text-sm md:text-base">Aún no hay ningún voto positivo registrado en esta sala.</p>
                    </div>`;
                return;
            }

            const lista = document.createElement('ol');
            lista.className = 'space-y-4 md:space-y-6 list-none';

            data.ranking.forEach((rest, index) => {
                // Color de borde por ESTILO DIRECTO (oro/plata/bronce) para que
                // siempre se vea, aunque el CDN de Tailwind no genere la clase.
                let medal = '';
                let medalLabel = `Puesto ${index + 1}`;
                let borderColor = '#1f2937'; // gris por defecto
                let cardShadow = '0 4px 14px rgba(0,0,0,0.3)';

                if (index === 0) { medal = '🥇'; medalLabel = 'Primer puesto'; borderColor = '#facc15'; cardShadow = '0 0 30px rgba(250,204,21,0.18)'; }
                else if (index === 1) { medal = '🥈'; medalLabel = 'Segundo puesto'; borderColor = '#9ca3af'; }
                else if (index === 2) { medal = '🥉'; medalLabel = 'Tercer puesto'; borderColor = '#b45309'; }

                const photoUrl = rest.photo_reference
                    ? `/api/photo/${encodeURIComponent(rest.photo_reference)}`
                    : 'https://via.placeholder.com/400x400/1a1a24/ffffff?text=Sin+Foto';

                const mapsQuery = encodeURIComponent(`${rest.restaurant_name} ${rest.restaurant_address || ''}`);
                const googleMapsUrl = `https://www.google.com/maps/search/?api=1&query=${mapsQuery}`;

                const item = document.createElement('li');
                item.innerHTML = `
                    <div style="border: 1px solid ${borderColor}; box-shadow: ${cardShadow}; background: rgba(26,26,36,0.85);"
                         class="rounded-2xl md:rounded-3xl p-3 md:p-5 flex items-center gap-3 md:gap-6 transition-transform transform hover:scale-[1.02] backdrop-blur-md">

                        <div class="w-16 h-16 md:w-24 md:h-24 bg-gray-800 rounded-xl md:rounded-2xl flex-shrink-0 overflow-hidden relative shadow-inner">
                            <img src="${photoUrl}" alt="Foto de ${rest.restaurant_name}" loading="lazy" decoding="async" class="w-full h-full object-cover">
                            ${medal ? `<div class="absolute -top-2 -left-2 text-2xl md:text-4xl drop-shadow-md" aria-hidden="true">${medal}</div>` : ''}
                            <span class="sr-only">${medalLabel}</span>
                        </div>

                        <div class="flex-grow min-w-0 flex flex-col justify-center">
                            <h2 class="text-base md:text-xl font-black text-white truncate leading-tight">${rest.restaurant_name}</h2>
                            <p class="text-[10px] md:text-sm text-gray-400 truncate mb-1 md:mb-2">${rest.restaurant_address || 'Sin dirección'}</p>
                            <a href="${googleMapsUrl}" target="_blank" rel="noopener noreferrer" class="inline-block text-[10px] md:text-xs text-cyan-400 uppercase font-bold hover:text-cyan-300 w-max bg-cyan-400/10 px-2 py-1 rounded md:px-3 md:py-1.5 transition-colors">
                                📍 Ver en mapa <span class="sr-only">(se abre en una pestaña nueva)</span>
                            </a>
                        </div>

                        <div class="flex flex-col items-center justify-center flex-shrink-0 bg-[#0d0d15] w-12 h-12 md:w-16 md:h-16 rounded-full border border-gray-700 shadow-inner">
                            <span class="text-lg md:text-2xl font-black text-emerald-400 leading-none mt-1">${rest.total_votes}</span>
                            <span class="text-[8px] md:text-[10px] uppercase text-gray-500 font-bold">Votos</span>
                            <span class="sr-only">${rest.total_votes} votos a favor</span>
                        </div>

                    </div>
                `;
                lista.appendChild(item);
            });

            container.appendChild(lista);

        } catch (error) {
            document.getElementById('rankingContainer').innerHTML = '<p class="text-pink-500 mt-10 text-center font-bold">Error al conectar con el servidor.</p>';
        }
    }

    function abandonarPartida() {
        localStorage.clear();
        window.location.replace('/');
    }
</script>
@endpush