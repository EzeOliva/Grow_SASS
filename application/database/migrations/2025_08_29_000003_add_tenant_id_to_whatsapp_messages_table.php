<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add nullable tenant_id first
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('whatsapp_messages', 'tenant_id')) {
                $table->unsignedInteger('tenant_id')->nullable()->after('id')->comment('Multi-tenant support - references tenants table');
                $table->index(['tenant_id', 'status']);
            }
        });

        // Backfill tenant_id from whatsapp_tickets
        try {
            DB::statement('UPDATE whatsapp_messages m JOIN whatsapp_tickets t ON m.ticket_id = t.id SET m.tenant_id = t.tenant_id WHERE m.tenant_id IS NULL');
        } catch (\Throwable $e) {
            // ignore if tables missing in some environments
        }

        // Add foreign key (kept nullable for legacy rows)
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->foreign('tenant_id')->references('tenant_id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            if (Schema::hasColumn('whatsapp_messages', 'tenant_id')) {
                $table->dropForeign(['tenant_id']);
                $table->dropIndex(['tenant_id', 'status']);
                $table->dropColumn('tenant_id');
            }
        });
    }
};


