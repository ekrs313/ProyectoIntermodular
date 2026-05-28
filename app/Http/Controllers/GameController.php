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
     * Arranca la PRIMERA ronda (la dispara el host al entrar a votación).
     */
    public function nextRound(Request $request)
    {
        $request->validate(['roomId' => 'required|string']);

        $room = Room::find($request->roomId);
        if (!$room) {
            return response()->json(['success' => false, 'message' => 'Sala no encontrada'], 404);
        }

        return $this->avanzarRonda($room);
    }

    /**
     * Registrar el voto de un jugador.
     */
    public function submitVote(Request $request)
    {
        $request->validate([
            'roomId'          => 'required|string',
            'userId'          => 'required|string',
            'restaurant.id'   => 'required|string',
            'restaurant.name' => 'required|string',
            'isLike'          => 'required|boolean',
        ]);

        $room = Room::find($request->roomId);
        if (!$room) {
            return response()->json(['success' => false, 'message' => 'Sala no encontrada'], 404);
        }

        // updateOrCreate evita el voto duplicado en la misma ronda/restaurante
        Vote::updateOrCreate(
            [
                'room_id'       => $room->id,
                'player_id'     => $request->userId,
                'round_number'  => $room->round_number,
                'restaurant_id' => $request->restaurant['id'],
            ],
            [
                'restaurant_name'    => $request->restaurant['name'],
                'restaurant_address' => $request->restaurant['address'] ?? null,
                'photo_reference'    => $request->restaurant['photo_reference'] ?? null,
                'is_like'            => $request->isLike,
            ]
        );

        // ¿Han votado TODOS los jugadores en esta ronda? (contamos jugadores distintos)
        $totalPlayers = $room->players()->count();
        $votosRonda = $room->votes()
            ->where('round_number', $room->round_number)
            ->distinct('player_id')
            ->count('player_id');

        if ($votosRonda < $totalPlayers) {
            return response()->json(['success' => true, 'match' => false, 'all_voted' => false]);
        }

        // Todos han votado: ¿unanimidad de "me gusta"?
        $likes = $room->votes()
            ->where('round_number', $room->round_number)
            ->where('is_like', true)
            ->distinct('player_id')
            ->count('player_id');

        if ($likes === $totalPlayers && $totalPlayers > 0) {
            // Reconstruimos el restaurante desde el voto guardado (no desde el cliente)
            $voto = $room->votes()
                ->where('round_number', $room->round_number)
                ->where('is_like', true)
                ->first();

            $ganador = [
                'id'              => $voto->restaurant_id,
                'name'            => $voto->restaurant_name,
                'address'         => $voto->restaurant_address,
                'photo_reference' => $voto->photo_reference,
            ];

            $room->update(['status' => 'finished']);
            event(new MatchFound($room->id, $ganador));

            return response()->json(['success' => true, 'match' => true]);
        }

        // No hay match: EL SERVIDOR avanza la ronda solo, sin depender de quién sea host
        $this->avanzarRonda($room);

        return response()->json(['success' => true, 'match' => false, 'all_voted' => true]);
    }

    /**
     * Busca el siguiente restaurante y lo emite por WebSocket.
     */
    private function avanzarRonda(Room $room)
{
    $room->increment('round_number');

    $apiKey = config('services.google_maps.key');

    if ($room->latitude && $room->longitude) {
        // Búsqueda por cercanía a la ubicación del host
        $response = Http::get('https://maps.googleapis.com/maps/api/place/nearbysearch/json', [
            'location' => $room->latitude . ',' . $room->longitude,
            'radius'   => 1500, // metros
            'type'     => 'restaurant',
            'key'      => $apiKey,
            'language' => 'es',
        ]);
    } else {
        // Fallback genérico si el host no compartió ubicación
        $response = Http::get('https://maps.googleapis.com/maps/api/place/textsearch/json', [
            'query'    => 'restaurantes populares',
            'key'      => $apiKey,
            'language' => 'es',
        ]);
    }

    $results = $response->json()['results'] ?? [];

    if (count($results) === 0) {
        $room->decrement('round_number');
        return response()->json([
            'success' => false,
            'message' => 'No se encontraron restaurantes cerca. Revisa la Places API de tu clave de Google.',
        ]);
    }

    $index = ($room->round_number - 1) % count($results);
    $place = $results[$index];

    $restaurantData = [
        'id'              => $place['place_id'],
        'name'            => $place['name'],
        // Nearby Search usa 'vicinity'; Text Search usa 'formatted_address'
        'address'         => $place['vicinity'] ?? $place['formatted_address'] ?? '',
        'photo_reference' => $place['photos'][0]['photo_reference'] ?? null,
        'rating'          => $place['rating'] ?? 'N/A',
    ];

    $room->update(['current_restaurant_id' => $restaurantData['id']]);

    event(new NewRound($room->id, $restaurantData, $room->round_number));

    return response()->json(['success' => true, 'restaurant' => $restaurantData]);
}
    public function getRanking($roomId)
    {
        $room = Room::find($roomId);
        if (!$room) {
            return response()->json(['success' => false, 'message' => 'Sala no encontrada'], 404);
        }

        $ranking = Vote::where('room_id', $roomId)
            ->where('is_like', true)
            ->select('restaurant_id', 'restaurant_name', 'restaurant_address', 'photo_reference', DB::raw('count(*) as total_votes'))
            ->groupBy('restaurant_id', 'restaurant_name', 'restaurant_address', 'photo_reference')
            ->orderByDesc('total_votes')
            ->get();

        return response()->json(['success' => true, 'ranking' => $ranking]);
    }
    /**
 * Proxy de fotos de Google Places: el cliente NUNCA ve la API key.
 * El front pide /api/photo/{reference} y el backend trae la imagen.
 */
public function photo(string $reference)
{
    $response = Http::get('https://maps.googleapis.com/maps/api/place/photo', [
        'maxwidth'       => 800,
        'photoreference' => $reference,
        'key'            => config('services.google_maps.key'),
    ]);

    if (!$response->successful()) {
        abort(404);
    }

    return response($response->body(), 200)
        ->header('Content-Type', $response->header('Content-Type') ?: 'image/jpeg')
        ->header('Cache-Control', 'public, max-age=86400'); // cachea 1 día
}

}