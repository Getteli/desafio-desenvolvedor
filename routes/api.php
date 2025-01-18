<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;

// Middleware de autenticação
Route::group([
    'prefix' => 'auth'
], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});

Route::middleware('auth:api')->group(function () {
    // Endpoint para upload de arquivo
    Route::post('/upload', [UploadController::class, 'store']);

    // Endpoint para histórico de upload de arquivo
    Route::get('/uploads', [UploadController::class, 'index']);

    // Endpoint para buscar conteúdo do arquivo
    Route::get('/upload/{id}', [UploadController::class, 'show']);
});
