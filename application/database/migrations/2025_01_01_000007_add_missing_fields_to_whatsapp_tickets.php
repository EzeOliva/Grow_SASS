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
        Schema::table('whatsapp_tickets', function (Blueprint $table) {
            // Add missing status
            $table->enum('status', ['open', 'on_hold', 'in_progress', 'closed'])->default('open')->change();
            
            // Add ticket type reference
            $table->unsignedBigInteger('ticket_type_id')->nullable()->after('category')->comment('Reference to ticket type');
            
            // Add line configuration reference
            $table->unsignedBigInteger('line_config_id')->nullable()->after('ticket_type_id')->comment('Reference to line configuration');
            
            // Add last activity timestamp for inactivity tracking
            $table->timestamp('last_activity_at')->nullable()->after('closed_at')->comment('Last activity timestamp');
            
            // Add auto-close scheduled timestamp
            $table->timestamp('auto_close_scheduled_at')->nullable()->after('last_activity_at')->comment('When auto-close is scheduled');
            
            // Add indexes for new fields
            $table->index('ticket_type_id');
            $table->index('line_config_id');
            $table->index('last_activity_at');
            $table->index('auto_close_scheduled_at');
            
            // Add foreign key constraints
            $table->foreign('ticket_type_id')->references('id')->on('whatsapp_ticket_types')->onDelete('set null');
            $table->foreign('line_config_id')->references('id')->on('whatsapp_line_configs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_tickets', function (Blueprint $table) {
            // Remove foreign keys
            $table->dropForeign(['ticket_type_id']);
            $table->dropForeign(['line_config_id']);
            
            // Remove indexes
            $table->dropIndex(['ticket_type_id']);
            $table->dropIndex(['line_config_id']);
            $table->dropIndex(['last_activity_at']);
            $table->dropIndex(['auto_close_scheduled_at']);
            
            // Remove columns
            $table->dropColumn([
                'ticket_type_id',
                'line_config_id', 
                'last_activity_at',
                'auto_close_scheduled_at'
            ]);
            
            // Revert status enum
            $table->enum('status', ['open', 'in_progress', 'closed'])->default('open')->change();
        });
    }
};

