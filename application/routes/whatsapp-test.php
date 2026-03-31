<?php

use Illuminate\Support\Facades\Route;

// Simple test route
Route::get('/whatsapp/test-simple', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Simple test route working!',
        'timestamp' => now()
    ]);
});

// Test route with controller
Route::get('/whatsapp/test-controller', [App\Http\Controllers\WhatsappTicketController::class, 'dashboard']);

