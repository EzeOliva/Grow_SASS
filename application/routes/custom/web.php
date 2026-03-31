<?php

/**----------------------------------------------------------------------------------------------------------------
 * [GROWCRM - CUSTOM ROUTES]
 * Place your custom routes or overides in this file. This file is not updated with Grow CRM updates
 * ---------------------------------------------------------------------------------------------------------------*/

// Test route to verify custom routes are loaded
Route::get('/custom-test', function () {
    return response()->json(['status' => 'success', 'message' => 'Custom routes are working!']);
})->name('custom.test');

// Messages - quick update endpoint
Route::post('/messages/thread/update', [\App\Http\Controllers\Messages::class, 'updateThread'])
    ->name('messages.thread.update');

// Messages - thread meta
Route::get('/messages/thread/meta', [\App\Http\Controllers\Messages::class, 'getThreadMeta'])
    ->name('messages.thread.meta');
