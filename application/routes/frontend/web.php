<?php

/** -------------------------------------------------------------------------
 * SaaS Frontend Routes
 * -------------------------------------------------------------------------*/
Route::any('/', 'Frontend\Home@index');

//FIX - redirect [admin] who is already logged in
Route::get('/home', function () {
    return redirect('/app-admin/home');
});

//PRICING
Route::any('pricing', 'Frontend\Pricing@index');

//CONATCT US
Route::get('contact', 'Frontend\Contact@index');
Route::post('contact', 'Frontend\Contact@submitForm');

//FAQ
Route::get('faq', 'Frontend\Faq@index');

//PAGES
Route::get('page/{slug}', 'Frontend\Pages@show');

//ACCOUNT - SIGNUP
Route::group(['prefix' => 'account'], function () {
    Route::any("/signup", "Frontend\Signup@index");
    Route::post("/signup", "Frontend\Signup@createAccount");
    Route::any("/login", "Frontend\Login@index");
    Route::post("/login", "Frontend\Login@getAccount");
});

// (Removed) Temporary WhatsApp test routes

// Main WhatsApp routes (working around tenant middleware issue)
Route::get('/whatsapp', function () {
    // Get basic KPIs for the dashboard
    $kpis = [
        'total_tickets' => 0,
        'open_tickets' => 0,
        'in_progress_tickets' => 0,
        'closed_tickets' => 0,
        'avg_first_response' => 0,
        'avg_resolution' => 0
    ];
    return view('whatsapp.dashboard.index', compact('kpis'));
})->name('whatsapp');

Route::get('/whatsapp/dashboard', function () {
    // Get basic KPIs for the dashboard
    $kpis = [
        'total_tickets' => 0,
        'open_tickets' => 0,
        'in_progress_tickets' => 0,
        'closed_tickets' => 0,
        'avg_first_response' => 0,
        'avg_resolution' => 0
    ];
    return view('whatsapp.dashboard.index', compact('kpis'));
})->name('whatsapp.dashboard');

Route::get('/whatsapp/tickets', function () {
    // Get basic data for tickets index
    $tickets = collect([]);
    $kpis = [
        'total_tickets' => 0,
        'open_tickets' => 0,
        'in_progress_tickets' => 0,
        'closed_tickets' => 0,
        'avg_first_response_time' => 0,
        'avg_resolution_time' => 0
    ];
    $agents = collect([]);
    return view('whatsapp.tickets.index', compact('tickets', 'kpis', 'agents'));
})->name('whatsapp.tickets.index');

Route::get('/whatsapp/connections', function () {
    // Get basic data for connections index
    $connections = collect([]);
    return view('whatsapp.connections.index', compact('connections'));
})->name('whatsapp.connections.index');
