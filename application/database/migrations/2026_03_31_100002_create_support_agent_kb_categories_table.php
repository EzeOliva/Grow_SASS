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
        Schema::create('support_agent_kb_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id');
            $table->unsignedInteger('kbcategory_id');
            $table->timestamps();

            $table->unique(['agent_id', 'kbcategory_id'], 'support_agent_kb_unique');
            $table->index('kbcategory_id');

            $table->foreign('agent_id')
                ->references('id')
                ->on('support_agents')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_agent_kb_categories');
    }
};
