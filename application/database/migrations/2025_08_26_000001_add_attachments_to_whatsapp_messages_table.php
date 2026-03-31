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
        if (Schema::hasTable('whatsapp_messages') && !Schema::hasColumn('whatsapp_messages', 'attachments')) {
            Schema::table('whatsapp_messages', function (Blueprint $table) {
                $table->json('attachments')->nullable()->after('body');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('whatsapp_messages') && Schema::hasColumn('whatsapp_messages', 'attachments')) {
            Schema::table('whatsapp_messages', function (Blueprint $table) {
                $table->dropColumn('attachments');
            });
        }
    }
};



