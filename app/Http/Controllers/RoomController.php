<?php

namespace App\Http\Controllers;

use App\Events\PlayerJoined;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Player;
use Illuminate\Support\Str;
use App\Events\GameStarted;

class RoomController extends Controller
{
    /**
     * CREAR SALA (Para el Host)
     */
    public function createRoom(Request $request)
    {
        // Validamos que nos llegue el nombre
        $request->validate([
            'userName' => 'required|string|max:255',
        ]);

        // 1. Generar un código único de 4 letras
        do {
            $code = strtoupper(Str::random(4));
        } while (Room::where('code', $code)->exists());

        // 2. Crear la sala en estado 'waiting' (esperando jugadores)
        $room = Room::create([
            'code' => $code,
            'status' => 'waiting',
            'round_number' => 0,
        ]);

        // 3. Registrar al creador como 'Host'
        $player = Player::create([
            'room_id' => $room->id,
            'name' => $request->userName,
            'is_host' => true,
        ]);

        // 4. Devolver los datos al Frontend en formato JSON
        return response()->json([
            'success' => true,
            'roomCode' => $room->code,
            'roomId' => $room->id,
            'userId' => $player->id,
            'isHost' => true,
        ]);
    }

    /**
     * UNIRSE A SALA (Para los Guests/Invitados)
     */
    public function joinRoom(Request $request)
    {
        // Validamos que nos llegue código y nombre
        $request->validate([
            'roomCode' => 'required|string|max:10',
            'userName' => 'required|string|max:255',
        ]);

        // 1. Buscar si la sala existe
        $room = Room::where('code', strtoupper($request->roomCode))->first();

        if (!$room) {
            return response()->json(['success' => false, 'message' => 'La sala no existe.'], 404);
        }

        // 2. Comprobar que la partida no haya empezado ya
        if ($room->status !== 'waiting') {
            return response()->json(['success' => false, 'message' => 'La partida ya ha comenzado.'], 403);
        }

        // 3. Registrar al jugador como 'Guest'
        $player = Player::create([
            'room_id' => $room->id,
            'name' => $request->userName,
            'is_host' => false,
        ]);

        // ============================================
        // DISPARAMOS EL EVENTO WEBSOCKET A LA SALA
        // ============================================
        event(new PlayerJoined($room->id, $player->name));

        // 4. Devolver los datos
        return response()->json([
            'success' => true,
            'roomId' => $room->id,
            'userId' => $player->id,
            'isHost' => false,
        ]);
    }
    /**
     * INICIAR PARTIDA (Solo el Host)
     */
    public function startGame(Request $request)
    {
        $request->validate([
            'roomId' => 'required|string',
        ]);

        $room = Room::find($request->roomId);

        if (!$room) {
            return response()->json(['success' => false, 'message' => 'Sala no encontrada'], 404);
        }

        $room->update(['status' => 'playing']);

        event(new GameStarted($room->id));

        return response()->json(['success' => true]);
    }
}