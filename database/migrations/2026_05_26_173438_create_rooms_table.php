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

            // --- CAMPOS ESTILO KAHOOT ---
            $table->integer('round_number')->default(0); // Ronda/restaurante actual
            $table->string('current_restaurant_id')->nullable(); // ID de Google del restaurante en pantalla
            $table->timestamp('timer_ends_at')->nullable(); // Cuenta atrás sincronizada
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
