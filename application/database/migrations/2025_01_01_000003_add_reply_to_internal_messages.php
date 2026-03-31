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
        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                if (!Schema::hasColumn('messages', 'message_reply_to_id')) {
                    $table->unsignedBigInteger('message_reply_to_id')->nullable()->after('message_target');
                    $table->index('message_reply_to_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                if (Schema::hasColumn('messages', 'message_reply_to_id')) {
                    $table->dropIndex(['message_reply_to_id']);
                    $table->dropColumn('message_reply_to_id');
                }
            });
        }
    }
};


