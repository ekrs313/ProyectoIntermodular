<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB; 
use App\Models\Room;
use App\Models\Vote;
use App\Events\NewRound;
use App\Events\MatchFound;

class GameController extends Controller
{
    /**
     * Obtener el siguiente restaurante y enviarlo a todos 
     */
    public function nextRound(Request $request)
    {
        $room = Room::find($request->roomId);
        if (!$room) return response()->json(['success' => false]);

        // Aumentamos la ronda
        $room->increment('round_number');

        // 1. Conectamos con Google Maps API para buscar restaurantes
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $response = Http::get("https://maps.googleapis.com/maps/api/place/textsearch/json", [
            'query' => 'restaurantes populares',
            'key' => $apiKey,
            'language' => 'es'
        ]);

        $results = $response->json()['results'] ?? [];

        // ==========================================
        //  PARCHE DE SEGURIDAD: EVITAR DIVISIÓN POR CERO
        // ==========================================
        if (count($results) === 0) {
            // Si Google devuelve error o 0 resultados, bajamos la ronda para no saltarnos turnos
            $room->decrement('round_number');
            return response()->json([
                'success' => false, 
                'message' => 'No se encontraron restaurantes. Revisa que tu API Key de Google Maps tenga activada la "Places API".'
            ]);
        }

        // Tomamos el restaurante correspondiente a la ronda actual
        $index = ($room->round_number - 1) % count($results);
        $place = $results[$index] ?? null;

        if (!$place) {
            return response()->json(['success' => false, 'message' => 'No hay más restaurantes']);
        }

        // Preparamos los datos limpios para el Frontend
        $restaurantData = [
            'id' => $place['place_id'],
            'name' => $place['name'],
            'address' => $place['formatted_address'] ?? '',
            'photo_reference' => $place['photos'][0]['photo_reference'] ?? null,
            'rating' => $place['rating'] ?? 'N/A',
        ];

        // Guardamos el restaurante actual en la sala
        $room->update(['current_restaurant_id' => $restaurantData['id']]);

        // 2. Disparamos el evento a los móviles
        event(new NewRound($room->id, $restaurantData));

        return response()->json(['success' => true, 'restaurant' => $restaurantData]);
    }

    /**
     * Registrar el voto de un jugador
     */
    public function submitVote(Request $request)
    {
        $room = Room::find($request->roomId);
        
        // 1. Guardamos el voto en la base de datos
        $vote = Vote::create([
            'room_id' => $room->id,
            'player_id' => $request->userId,
            'round_number' => $room->round_number,
            'restaurant_id' => $request->restaurant['id'],
            'restaurant_name' => $request->restaurant['name'],
            'restaurant_address' => $request->restaurant['address'],
            'photo_reference' => $request->restaurant['photo_reference'],
            'is_like' => $request->isLike, // true o false
        ]);

        // 2. Comprobar si TODOS los jugadores han votado en esta ronda
        $totalPlayers = $room->players()->count();
        $votesInThisRound = $room->votes()->where('round_number', $room->round_number)->count();

        if ($votesInThisRound >= $totalPlayers) {
            // ¡Todos han votado! Vamos a ver si hay unanimidad (Match)
            $likes = $room->votes()
                        ->where('round_number', $room->round_number)
                        ->where('is_like', true)
                        ->count();

            if ($likes === $totalPlayers && $totalPlayers > 0) {
                // ¡HAY MATCH! Disparamos el evento de victoria
                $room->update(['status' => 'finished']);
                event(new MatchFound($room->id, $request->restaurant));
                return response()->json(['success' => true, 'match' => true]);
            } else {
                // No hay match unánime. Aquí el Host (frontend) decidirá llamar a nextRound()
                return response()->json(['success' => true, 'match' => false, 'all_voted' => true]);
            }
        }

        return response()->json(['success' => true, 'match' => false, 'all_voted' => false]);
    }

    /**
     * Obtener el Ranking de votos de la sala
     */
    public function getRanking($roomId)
    {
        $room = Room::find($roomId);
        if (!$room) {
            return response()->json(['success' => false, 'message' => 'Sala no encontrada']);
        }

        $ranking = Vote::where('room_id', $roomId)
            ->where('is_like', true)
            ->select('restaurant_id', 'restaurant_name', 'restaurant_address', 'photo_reference', DB::raw('count(*) as total_votes'))
            ->groupBy('restaurant_id', 'restaurant_name', 'restaurant_address', 'photo_reference')
            ->orderByDesc('total_votes')
            ->get();

        return response()->json(['success' => true, 'ranking' => $ranking]);
    }
}