<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Player extends Model
{
    use HasUuids;

    protected $fillable = [
        'room_id',
        'name',
        'is_host',
    ];

    /**
     * Relación: El jugador pertenece a una sala específica.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Relación: Un jugador emite muchos votos a lo largo de la partida.
     */
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}