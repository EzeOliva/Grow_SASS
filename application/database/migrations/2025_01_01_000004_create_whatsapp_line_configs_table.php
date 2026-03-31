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
        Schema::create('whatsapp_line_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('tenant_id')->comment('Multi-tenant support');
            $table->unsignedBigInteger('connection_id')->comment('Reference to whatsapp_connections table');
            $table->string('line_name')->comment('Friendly name for this line');
            $table->enum('assignment_mode', ['manual', 'auto_round_robin', 'auto_load_balanced'])->default('manual');
            $table->boolean('auto_assign_enabled')->default(false);
            $table->text('welcome_message')->nullable()->comment('Message sent when accepting ticket');
            $table->text('closure_message')->nullable()->comment('Message sent when closing ticket');
            $table->text('inactivity_message')->nullable()->comment('Message sent before auto-close');
            $table->integer('inactivity_timeout_minutes')->default(1440)->comment('Minutes before auto-close (default: 24h)');
            $table->json('auto_assign_agents')->nullable()->comment('Array of agent IDs for auto-assignment');
            $table->json('routing_rules')->nullable()->comment('JSON rules for ticket routing');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index(['tenant_id', 'connection_id']);
            $table->index(['tenant_id', 'is_active']);
            $table->index('assignment_mode');
            
            // Foreign keys
            $table->foreign('connection_id')->references('id')->on('whatsapp_connections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_line_configs');
    }
};

