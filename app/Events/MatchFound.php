<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchFound implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomId;
    public $restaurant;

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
        return 'match.found';
    }
}