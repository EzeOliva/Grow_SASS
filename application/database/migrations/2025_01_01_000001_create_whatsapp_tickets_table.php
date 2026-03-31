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
        Schema::create('whatsapp_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('tenant_id')->comment('Multi-tenant support - references tenants table');
            $table->string('contact_name')->comment('Contact name for this ticket');
            $table->string('contact_email')->nullable()->comment('Contact email');
            $table->string('contact_phone')->nullable()->comment('Contact phone number');
            $table->unsignedInteger('agent_id')->nullable()->comment('Assigned agent from users table');
            $table->enum('status', ['open', 'in_progress', 'closed'])->default('open');
            $table->enum('channel', ['whatsapp', 'email'])->default('whatsapp');
            $table->string('subject')->nullable();
            $table->text('tags')->nullable()->comment('JSON array of tags');
            $table->timestamp('opened_at')->comment('First message timestamp');
            $table->timestamp('first_response_at')->nullable()->comment('Agent first response');
            $table->timestamp('closed_at')->nullable()->comment('Ticket closure timestamp');
            $table->text('internal_notes')->nullable()->comment('Agent notes');
            $table->string('whatsapp_number')->nullable()->comment('WhatsApp number for this ticket');
            $table->string('priority')->default('medium')->comment('low, medium, high, urgent');
            $table->string('category')->nullable()->comment('Ticket category');
            $table->string('company')->nullable()->comment('Contact company');
            $table->unsignedBigInteger('ticket_type_id')->nullable()->comment('Reference to ticket types table');
            $table->unsignedBigInteger('line_config_id')->nullable()->comment('Reference to line configs table');
            $table->timestamp('last_activity_at')->nullable()->comment('Last activity timestamp');
            $table->timestamp('auto_close_scheduled_at')->nullable()->comment('Scheduled auto-close timestamp');
            $table->timestamps();
            
            // Indexes
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'agent_id']);
            $table->index(['tenant_id', 'contact_email']);
            $table->index(['tenant_id', 'ticket_type_id']);
            $table->index(['tenant_id', 'line_config_id']);
            $table->index(['tenant_id', 'last_activity_at']);
            $table->index(['tenant_id', 'priority']);
            $table->index('opened_at');
            $table->index('first_response_at');
            $table->index('closed_at');
            
            // Foreign key to tenants table
            $table->foreign('tenant_id')->references('tenant_id')->on('tenants')->onDelete('cascade');
            // Foreign key to users table (agents)
            $table->foreign('agent_id')->references('id')->on('users')->onDelete('set null');
            // Foreign key to ticket types table
            $table->foreign('ticket_type_id')->references('id')->on('whatsapp_ticket_types')->onDelete('set null');
            // Foreign key to line configs table
            $table->foreign('line_config_id')->references('id')->on('whatsapp_line_configs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_tickets');
    }
};
