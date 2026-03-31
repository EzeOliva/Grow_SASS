<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsappTicketController;
use App\Http\Controllers\WhatsappMessageController;
use App\Http\Controllers\WhatsappContactController;
use App\Http\Controllers\WhatsappConnectionController;
use App\Http\Controllers\WhatsappTagController;
use App\Http\Controllers\WhatsappTicketTypeController;
use App\Http\Controllers\WhatsappLineConfigController;
use App\Http\Controllers\WhatsappReportingController;
use App\Http\Controllers\WhatsappTickListController;
use App\Http\Controllers\WhatsAppTestController;

// (Removed) Test routes for WhatsApp functionality

// Main WhatsApp routes (ensure tenant bootstrap for DB selection)
Route::middleware([\App\Http\Middleware\Whatsapp\BootstrapTenant::class])->group(function () {
    // Health check
    Route::get('/whatsapp/health', function () {
        try {
            $tenant = app()->bound('currentTenant') ? app('currentTenant') : null;
            $db = \Illuminate\Support\Facades\DB::connection('tenant')->getDatabaseName();
            $routes = [
                'dashboard' => route('whatsapp.dashboard', [], false),
                'tickets.index' => route('whatsapp.tickets.index', [], false),
                'tickets.create' => route('whatsapp.tickets.create', [], false),
                'connections' => route('whatsapp.connections.index', [], false),
                'tags' => route('whatsapp.tags.index', [], false),
                'ticket_types' => route('whatsapp.ticket-types.index', [], false),
                'line_configs' => route('whatsapp.line-configs.index', [], false),
            ];
            return response()->json([
                'status' => 'ok',
                'tenant_id' => $tenant->tenant_id ?? ($tenant->id ?? null),
                'tenant_db' => $db,
                'routes' => $routes,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
            ], 500);
        }
    })->name('whatsapp.health');
    
    // Dashboard
    Route::get('/whatsapp/dashboard', [WhatsappTicketController::class, 'dashboard'])->name('whatsapp.dashboard');
    // (Removed) Enhanced and Modern dashboards
    
    // Tickets
    Route::get('/whatsapp/tickets', [WhatsappTicketController::class, 'index'])->name('whatsapp.tickets.index');
    Route::get('/whatsapp/tickets/create', [WhatsappTicketController::class, 'create'])->name('whatsapp.tickets.create');
    Route::post('/whatsapp/tickets', [WhatsappTicketController::class, 'store'])->name('whatsapp.tickets.store');
    Route::get('/whatsapp/tickets/{ticket}', [WhatsappTicketController::class, 'show'])->name('whatsapp.tickets.show');
    Route::get('/whatsapp/tickets/{ticket}/edit', [WhatsappTicketController::class, 'edit'])->name('whatsapp.tickets.edit');
    Route::put('/whatsapp/tickets/{ticket}', [WhatsappTicketController::class, 'update'])->name('whatsapp.tickets.update');
    Route::delete('/whatsapp/tickets/{ticket}', [WhatsappTicketController::class, 'destroy'])->name('whatsapp.tickets.destroy');
    
    // Ticket actions
    Route::post('/whatsapp/tickets/{ticket}/send-message', [WhatsappTicketController::class, 'sendMessage'])->name('whatsapp.tickets.send-message')->middleware('whatsappPermissions:reply_clients');
    Route::post('/whatsapp/tickets/{ticket}/assign', [WhatsappTicketController::class, 'assign'])->name('whatsapp.tickets.assign')->middleware('whatsappPermissions:assign_tickets');
    Route::post('/whatsapp/tickets/{ticket}/close', [WhatsappTicketController::class, 'close'])->name('whatsapp.tickets.close');
    Route::post('/whatsapp/tickets/{ticket}/reopen', [WhatsappTicketController::class, 'reopen'])->name('whatsapp.tickets.reopen');
    Route::post('/whatsapp/tickets/{ticket}/put-on-hold', [WhatsappTicketController::class, 'putOnHold'])->name('whatsapp.tickets.put-on-hold');
    Route::post('/whatsapp/tickets/{ticket}/update-type', [WhatsappTicketController::class, 'updateTicketType'])->name('whatsapp.tickets.update-type');
    Route::post('/whatsapp/tickets/{ticket}/update-tags', [WhatsappTicketController::class, 'updateTags'])->name('whatsapp.tickets.update-tags');
    
    // KPIs
    Route::get('/whatsapp/kpis', [WhatsappTicketController::class, 'getKPIs'])->name('whatsapp.kpis');
    
    // Reporting & Analytics
    Route::get('/whatsapp/reporting', [WhatsappReportingController::class, 'index'])->name('whatsapp.reporting.index');
    Route::get('/whatsapp/reporting/export', [WhatsappReportingController::class, 'export'])->name('whatsapp.reporting.export');
    Route::get('/whatsapp/reporting/real-time-kpis', [WhatsappReportingController::class, 'getRealTimeKPIs'])->name('whatsapp.reporting.real-time-kpis');
    
    // Contacts
    Route::get('/whatsapp/contacts', [WhatsappContactController::class, 'index'])->name('whatsapp.contacts.index');
    Route::get('/whatsapp/contacts/{contact}', [WhatsappContactController::class, 'show'])->name('whatsapp.contacts.show');
    Route::put('/whatsapp/contacts/{contact}', [WhatsappContactController::class, 'update'])->name('whatsapp.contacts.update');
    Route::post('/whatsapp/contacts/{contact}/assign-tags', [WhatsappContactController::class, 'assignTags'])->name('whatsapp.contacts.assign-tags');
    Route::post('/whatsapp/contacts/{contact}/remove-tags', [WhatsappContactController::class, 'removeTags'])->name('whatsapp.contacts.remove-tags');
    Route::get('/whatsapp/contacts/suggestions', [WhatsappContactController::class, 'getSuggestions'])->name('whatsapp.contacts.suggestions');
    Route::get('/whatsapp/contacts/{contact}/timeline', [WhatsappContactController::class, 'getTimeline'])->name('whatsapp.contacts.timeline');
    Route::get('/whatsapp/contacts/export', [WhatsappContactController::class, 'export'])->name('whatsapp.contacts.export');
    
    // Enhanced Messages
    Route::post('/whatsapp/messages/send', [WhatsappMessageController::class, 'sendMessage'])->name('whatsapp.messages.send')->middleware('whatsappPermissions:reply_clients');
    Route::get('/whatsapp/messages/templates', [WhatsappMessageController::class, 'getQuickTemplates'])->name('whatsapp.messages.templates');
    Route::post('/whatsapp/messages/templates', [WhatsappMessageController::class, 'createTemplate'])->name('whatsapp.messages.create-template');
    Route::get('/whatsapp/messages/suggestions', [WhatsappMessageController::class, 'getSuggestions'])->name('whatsapp.messages.suggestions');
    
    // Tick Lists
    Route::get('/whatsapp/tickets/{ticket}/tick-lists', [WhatsappTickListController::class, 'index'])->name('whatsapp.tickets.tick-lists.index');
    Route::get('/whatsapp/tickets/{ticket}/tick-lists/create', [WhatsappTickListController::class, 'create'])->name('whatsapp.tickets.tick-lists.create');
    Route::post('/whatsapp/tickets/{ticket}/tick-lists', [WhatsappTickListController::class, 'store'])->name('whatsapp.tickets.tick-lists.store');
    Route::get('/whatsapp/tickets/{ticket}/tick-lists/{tickList}/edit', [WhatsappTickListController::class, 'edit'])->name('whatsapp.tickets.tick-lists.edit');
    Route::put('/whatsapp/tickets/{ticket}/tick-lists/{tickList}', [WhatsappTickListController::class, 'update'])->name('whatsapp.tickets.tick-lists.update');
    Route::delete('/whatsapp/tickets/{ticket}/tick-lists/{tickList}', [WhatsappTickListController::class, 'destroy'])->name('whatsapp.tickets.tick-lists.destroy');
    Route::post('/whatsapp/tickets/{ticket}/tick-lists/{tickList}/toggle-status', [WhatsappTickListController::class, 'toggleStatus'])->name('whatsapp.tickets.tick-lists.toggle-status');
    Route::post('/whatsapp/tickets/{ticket}/tick-lists/bulk-delete', [WhatsappTickListController::class, 'bulkDestroy'])->name('whatsapp.tickets.tick-lists.bulk-delete');
    
    // Connections
    Route::get('/whatsapp/connections', [WhatsappConnectionController::class, 'index'])->name('whatsapp.connections.index');
    Route::get('/whatsapp/connections/create', [WhatsappConnectionController::class, 'create'])->name('whatsapp.connections.create');
    Route::post('/whatsapp/connections', [WhatsappConnectionController::class, 'store'])->name('whatsapp.connections.store');
    Route::get('/whatsapp/connections/{connection}', [WhatsappConnectionController::class, 'show'])->name('whatsapp.connections.show');
    Route::get('/whatsapp/connections/{connection}/edit', [WhatsappConnectionController::class, 'edit'])->name('whatsapp.connections.edit');
    Route::put('/whatsapp/connections/{connection}', [WhatsappConnectionController::class, 'update'])->name('whatsapp.connections.update');
    Route::delete('/whatsapp/connections/{connection}', [WhatsappConnectionController::class, 'destroy'])->name('whatsapp.connections.destroy');
    
    // Line Configurations
    Route::get('/whatsapp/line-configs', [WhatsappLineConfigController::class, 'index'])->name('whatsapp.line-configs.index');
    Route::get('/whatsapp/line-configs/create', [WhatsappLineConfigController::class, 'create'])->name('whatsapp.line-configs.create');
    Route::post('/whatsapp/line-configs', [WhatsappLineConfigController::class, 'store'])->name('whatsapp.line-configs.store');
    Route::get('/whatsapp/line-configs/{lineConfig}', [WhatsappLineConfigController::class, 'show'])->name('whatsapp.line-configs.show');
    Route::get('/whatsapp/line-configs/{lineConfig}/edit', [WhatsappLineConfigController::class, 'edit'])->name('whatsapp.line-configs.edit');
    Route::put('/whatsapp/line-configs/{lineConfig}', [WhatsappLineConfigController::class, 'update'])->name('whatsapp.line-configs.update');
    Route::delete('/whatsapp/line-configs/{lineConfig}', [WhatsappLineConfigController::class, 'destroy'])->name('whatsapp.line-configs.destroy');
    Route::post('/whatsapp/line-configs/{lineConfig}/toggle-status', [WhatsappLineConfigController::class, 'toggleStatus'])->name('whatsapp.line-configs.toggle-status');
    Route::post('/whatsapp/line-configs/{lineConfig}/test-auto-assignment', [WhatsappLineConfigController::class, 'testAutoAssignment'])->name('whatsapp.line-configs.test-auto-assignment');
    Route::get('/whatsapp/line-configs/{lineConfig}/stats', [WhatsappLineConfigController::class, 'getStats'])->name('whatsapp.line-configs.stats');
    
    // Tags
    Route::get('/whatsapp/tags', [WhatsappTagController::class, 'index'])->name('whatsapp.tags.index');
    Route::get('/whatsapp/tags/create', [WhatsappTagController::class, 'create'])->name('whatsapp.tags.create');
    Route::post('/whatsapp/tags', [WhatsappTagController::class, 'store'])->name('whatsapp.tags.store');
    Route::get('/whatsapp/tags/{tag}', [WhatsappTagController::class, 'show'])->name('whatsapp.tags.show');
    Route::get('/whatsapp/tags/{tag}/edit', [WhatsappTagController::class, 'edit'])->name('whatsapp.tags.edit');
    Route::put('/whatsapp/tags/{tag}', [WhatsappTagController::class, 'update'])->name('whatsapp.tags.update');
    Route::delete('/whatsapp/tags/{tag}', [WhatsappTagController::class, 'destroy'])->name('whatsapp.tags.destroy');
    Route::post('/whatsapp/tags/{tag}/toggle-status', [WhatsappTagController::class, 'toggleStatus'])->name('whatsapp.tags.toggle-status');
    Route::get('/whatsapp/tags/get-tags', [WhatsappTagController::class, 'getTags'])->name('whatsapp.tags.get-tags');
    Route::post('/whatsapp/tags/bulk-action', [WhatsappTagController::class, 'bulkAction'])->name('whatsapp.tags.bulk-action');
    
    // Ticket Types
    Route::get('/whatsapp/ticket-types', [WhatsappTicketTypeController::class, 'index'])->name('whatsapp.ticket-types.index');
    Route::get('/whatsapp/ticket-types/create', [WhatsappTicketController::class, 'create'])->name('whatsapp.ticket-types.create');
    Route::post('/whatsapp/ticket-types', [WhatsappTicketTypeController::class, 'store'])->name('whatsapp.ticket-types.store');
    Route::get('/whatsapp/ticket-types/{ticketType}', [WhatsappTicketTypeController::class, 'show'])->name('whatsapp.ticket-types.show');
    Route::get('/whatsapp/ticket-types/{ticketType}/edit', [WhatsappTicketTypeController::class, 'edit'])->name('whatsapp.ticket-types.edit');
    Route::put('/whatsapp/ticket-types/{ticketType}', [WhatsappTicketTypeController::class, 'update'])->name('whatsapp.ticket-types.update');
    Route::delete('/whatsapp/ticket-types/{ticketType}', [WhatsappTicketTypeController::class, 'destroy'])->name('whatsapp.ticket-types.destroy');
    Route::post('/whatsapp/ticket-types/{ticketType}/toggle-status', [WhatsappTicketTypeController::class, 'toggleStatus'])->name('whatsapp.ticket-types.toggle-status');
    Route::get('/whatsapp/ticket-types/get-ticket-types', [WhatsappTicketTypeController::class, 'getTicketTypes'])->name('whatsapp.ticket-types.get-ticket-types');
    Route::post('/whatsapp/ticket-types/update-sort-order', [WhatsappTicketTypeController::class, 'updateSortOrder'])->name('whatsapp.ticket-types.update-sort-order');
    Route::post('/whatsapp/ticket-types/bulk-action', [WhatsappTicketTypeController::class, 'bulkAction'])->name('whatsapp.ticket-types.bulk-action');
    Route::get('/whatsapp/ticket-types/{ticketType}/stats', [WhatsappTicketTypeController::class, 'getStats'])->name('whatsapp.ticket-types.stats');
    
    // Connection actions
    Route::post('/whatsapp/connections/{connection}/connect', [WhatsappConnectionController::class, 'connect'])->name('whatsapp.connections.connect');
    Route::post('/whatsapp/connections/{connection}/disconnect', [WhatsappConnectionController::class, 'disconnect'])->name('whatsapp.connections.disconnect');
    Route::get('/whatsapp/connections/{connection}/qr-code', [WhatsappConnectionController::class, 'getQRCode'])->name('whatsapp.connections.qr-code');
});

// Webhook for incoming messages (no auth required)
Route::post('/whatsapp/webhook', function () {
    // TODO: Implement webhook handling
    return response()->json(['status' => 'received']);
})->name('whatsapp.webhook');
