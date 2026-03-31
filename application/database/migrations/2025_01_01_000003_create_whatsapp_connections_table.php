<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('whatsapp_connections', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('tenant_id')->comment('Multi-tenant support - references tenants table');
            $table->string('connection_name')->comment('Friendly name for this connection');
            $table->enum('connection_type', ['baileys', 'twilio', '360dialog', 'gupshup'])->comment('WhatsApp connection method');
            $table->enum('status', ['disconnected', 'connecting', 'connected', 'error'])->default('disconnected');
            $table->string('phone_number')->nullable()->comment('WhatsApp phone number');
            $table->text('connection_data')->nullable()->comment('JSON data for connection (API keys, tokens, etc.)');
            $table->text('qr_code')->nullable()->comment('QR code data for Baileys');
            $table->timestamp('last_connected_at')->nullable()->comment('Last successful connection');
            $table->timestamp('last_error_at')->nullable()->comment('Last connection error');
            $table->text('error_message')->nullable()->comment('Last error message');
            $table->json('webhook_config')->nullable()->comment('Webhook configuration');
            $table->boolean('is_active')->default(true)->comment('Whether this connection is active');
            $table->timestamps();
            
            // Indexes
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'connection_type']);
            $table->index('phone_number');
            
            // Foreign keys
            $table->foreign('tenant_id')->references('tenant_id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_connections');
    }
};
