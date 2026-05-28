<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('room_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('player_id')->constrained()->cascadeOnDelete();

            // Ronda en la que se votó (facilita estadísticas y el match por ronda)
            $table->integer('round_number');

            // Datos del restaurante para armar el Podio/Ranking final
            $table->string('restaurant_id');
            $table->string('restaurant_name');
            $table->string('restaurant_address')->nullable();
            $table->text('photo_reference')->nullable();

            // El voto: true (Verde/Sí), false (Rojo/No)
            $table->boolean('is_like');

            $table->timestamps();

            // Un jugador NO puede votar dos veces el mismo restaurante en la misma ronda.
            // Esto blinda el "doble tap" y los reenvíos a nivel de base de datos.
            $table->unique(['player_id', 'round_number', 'restaurant_id'], 'votes_unique_per_round');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};