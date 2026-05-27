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
            
            // Hemos añadido la ronda en la que se votó para facilitar las estadísticas
            $table->integer('round_number'); 
            
            // Datos del restaurante para poder armar el Podio/Ranking final
            $table->string('restaurant_id'); 
            $table->string('restaurant_name');
            $table->string('restaurant_address')->nullable();
            $table->text('photo_reference')->nullable();
            
            // El voto: true (Verde/Sí), false (Rojo/No)
            $table->boolean('is_like'); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};