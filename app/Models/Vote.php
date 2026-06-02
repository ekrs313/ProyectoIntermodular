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
     * Conversión de tipos.
     * is_like se trata como booleano real para que las comparaciones en PHP
     * sean fiables (MySQL lo devuelve como 0/1 y "0" es truthy en PHP).
     */
    protected $casts = [
        'is_like' => 'boolean',
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
