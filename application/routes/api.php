<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Polling endpoints for messages system
Route::get('/polling/general', function () {
    return response()->json([
        'status' => 'success',
        'data' => [
            'online_users' => 0,
            'unread_messages' => 0,
            'system_status' => 'operational'
        ]
    ]);
});

Route::post('/polling/timers', function (Request $request) {
    return response()->json([
        'status' => 'success',
        'data' => [
            'timers' => [],
            'ref' => $request->get('ref', 'list')
        ]
    ]);
});
