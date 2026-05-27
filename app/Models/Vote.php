<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Vote extends Model
{
    use HasUuids;

    protected $fillable = [
        'room_id',
        'player_id',
        'round_number', // Crucial para saber a qué ronda/restaurante pertenece el voto
        'restaurant_id',
        'restaurant_name',
        'restaurant_address',
        'photo_reference',
        'is_like', // true = Sí/Verde, false = No/Rojo
    ];

    /**
     * Relación: El voto pertenece a una sala.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Relación: El voto fue emitido por un jugador específico.
     */
    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}