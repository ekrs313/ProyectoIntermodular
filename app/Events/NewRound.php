<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewRound implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomId;
    public $restaurant; // Datos del restaurante a mostrar

    public function __construct($roomId, $restaurant)
    {
        $this->roomId = $roomId;
        $this->restaurant = $restaurant;
    }

    public function broadcastOn(): array
    {
        return [new Channel('room.' . $this->roomId)];
    }

    public function broadcastAs()
    {
        return 'new.round';
    }
}