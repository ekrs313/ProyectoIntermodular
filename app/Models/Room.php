<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Room extends Model
{
    use HasUuids;

    // Campos que se pueden rellenar de forma masiva
    protected $fillable = [
        'code',
        'status',
        'round_number',
        'current_restaurant_id',
        'timer_ends_at',
    ];

    // Indica a Laravel que trate 'timer_ends_at' como un objeto Carbon (Fecha/Hora)
    protected $casts = [
        'timer_ends_at' => 'datetime',
    ];

    /**
     * Relación: Una sala tiene muchos jugadores unidos.
     */
    public function players()
    {
        return $this->hasMany(Player::class);
    }

    /**
     * Relación: Una sala acumula todos los votos de todas las rondas.
     */
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}