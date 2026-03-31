<?php

// Separate test routes for messages functionality
// This bypasses the main web.php authentication issues

Route::get('/test-messages-direct', function() {
    try {
        // Mock data for testing
        $data = [
            'users' => collect([
                (object)['id' => 1, 'name' => 'Test User 1', 'email' => 'test1@example.com', 'full_name' => 'Test User 1', 'is_online' => true],
                (object)['id' => 2, 'name' => 'Test User 2', 'email' => 'test2@example.com', 'full_name' => 'Test User 2', 'is_online' => false],
            ]),
            'whatsapp_connections' => collect([
                (object)['id' => 1, 'connection_name' => 'Test WhatsApp', 'phone_number' => '+1234567890', 'status' => 'connected'],
            ]),
            'thread' => null
        ];
        
        return view('pages.messages.wrapper', $data);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

Route::get('/test-messages-ajax', function() {
    return response()->json([
        'success' => true,
        'message' => 'Messages AJAX endpoint working',
        'data' => [
            'users' => [
                ['id' => 1, 'name' => 'Test User 1', 'email' => 'test1@example.com'],
                ['id' => 2, 'name' => 'Test User 2', 'email' => 'test2@example.com'],
            ]
        ]
    ]);
});
