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
    public $restaurant;
    public $roundNumber;

public function __construct($roomId, $restaurant, $roundNumber)
{
    $this->roomId = $roomId;
    $this->restaurant = $restaurant;
    $this->roundNumber = $roundNumber;
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