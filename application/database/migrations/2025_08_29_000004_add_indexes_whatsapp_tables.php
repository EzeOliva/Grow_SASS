<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('whatsapp_tickets')) {
            Schema::table('whatsapp_tickets', function (Blueprint $table) {
                $table->index(['tenant_id', 'status', 'created_at'], 'idx_tenant_status_created');
                $table->index(['tenant_id', 'agent_id', 'status'], 'idx_tenant_agent_status');
            });
        }

        if (Schema::hasTable('whatsapp_messages')) {
            Schema::table('whatsapp_messages', function (Blueprint $table) {
                $table->index(['ticket_id', 'created_at'], 'idx_ticket_created');
                $table->index(['tenant_id', 'status'], 'idx_tenant_status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('whatsapp_tickets')) {
            Schema::table('whatsapp_tickets', function (Blueprint $table) {
                $table->dropIndex('idx_tenant_status_created');
                $table->dropIndex('idx_tenant_agent_status');
            });
        }

        if (Schema::hasTable('whatsapp_messages')) {
            Schema::table('whatsapp_messages', function (Blueprint $table) {
                $table->dropIndex('idx_ticket_created');
                $table->dropIndex('idx_tenant_status');
            });
        }
    }
};


