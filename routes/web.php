<?php

use Illuminate\Support\Facades\Route;

// Ruta principal (Login/Crear)
Route::get('/', function () {
    return view('index');
});

// Ruta del Lobby
Route::get('/lobby', function () {
    return view('lobby');
});

// Ruta de Votación (El juego en sí)
Route::get('/votacion', function () {
    return view('votacion');
});

// Ruta de Victoria (Match)
Route::get('/match', function () {
    return view('match');
});

// Ruta de Ranking (Para un futuro si decides implementarlo)
Route::get('/ranking', function () {
    return view('ranking');
});