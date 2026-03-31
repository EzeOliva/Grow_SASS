<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('whatsapp_tickets') && Schema::hasColumn('whatsapp_tickets', 'contact_phone')) {
            Schema::table('whatsapp_tickets', function (Blueprint $table) {
                $table->string('contact_phone')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // No-op: reverting to NOT NULL could fail on existing nulls; leave as nullable
    }
};




