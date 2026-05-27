<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // ¡Importante el Now!
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerJoined implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomId;
    public $playerName;

    // Aquí recibimos los datos que queremos enviar al frontend
    public function __construct($roomId, $playerName)
    {
        $this->roomId = $roomId;
        $this->playerName = $playerName;
    }

    // Le decimos a Reverb por qué "Canal" enviarlo (el canal de la sala)
    public function broadcastOn(): array
    {
        return [
            new Channel('room.' . $this->roomId),
        ];
    }

    // Nombre del evento que escucharemos en JavaScript
    public function broadcastAs()
    {
        return 'player.joined';
    }
}