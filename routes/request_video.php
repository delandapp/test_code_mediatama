<?php

use App\Http\Controllers\Dashboard\RequestVideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RequestVideoController::class, 'index']);
Route::get('/get_requestvideo', [RequestVideoController::class, 'getRequestVideo']);
Route::get('/approve/{id}', [RequestVideoController::class, 'approveRequestVideo']);
Route::get('/cancel/{id}', [RequestVideoController::class, 'cancelRequestVideo']);
Route::post('/edit/{id}', [RequestVideoController::class, 'edit']);
Route::post('/hentikan/{idUser}/{idMateri}', [RequestVideoController::class, 'hentikanMenonton']);
Route::get('/{id}', [RequestVideoController::class, 'show']);
Route::delete('/{id}', [RequestVideoController::class, 'destroy']);
Route::post('/tambah', [RequestVideoController::class, 'create']);
