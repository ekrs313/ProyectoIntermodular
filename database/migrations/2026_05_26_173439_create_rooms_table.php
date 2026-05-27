<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->string('code', 10)->unique(); 
            $table->enum('status', ['waiting', 'playing', 'finished'])->default('waiting');
            
            // --- NUEVOS CAMPOS ESTILO KAHOOT ---
            $table->integer('round_number')->default(0); // Para saber por qué ronda/restaurante vamos
            $table->string('current_restaurant_id')->nullable(); // El ID de Google del restaurante actual en pantalla
            $table->timestamp('timer_ends_at')->nullable(); // Para la cuenta atrás sincronizada de 10-15 segundos
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};