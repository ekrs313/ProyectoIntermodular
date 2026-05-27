<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController; // Importamos el controlador
use App\Http\Controllers\GameController;

// Rutas de Votación y Juego
Route::post('/next-round', [GameController::class, 'nextRound']);
Route::post('/submit-vote', [GameController::class, 'submitVote']);
// Rutas para tu Frontend (Matched Foods)
Route::post('/create-room', [RoomController::class, 'createRoom']);
Route::post('/join-room', [RoomController::class, 'joinRoom']);
Route::post('/start-game', [RoomController::class, 'startGame']);
Route::get('/ranking/{roomId}', [GameController::class, 'getRanking']);