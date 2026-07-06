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
        Schema::create('support_agent_ticket_suggestions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id')->nullable()->index();
            $table->unsignedInteger('ticket_id')->nullable()->index();
            $table->unsignedInteger('suggestion_creatorid')->nullable()->index();
            $table->string('suggestion_status', 40)->default('proposed')->index();
            $table->string('model_name', 120)->nullable();
            $table->string('prompt_version', 60)->nullable();
            $table->integer('model_tokens_prompt')->nullable();
            $table->integer('model_tokens_completion')->nullable();
            $table->longText('suggestion_text');
            $table->longText('suggestion_sources')->nullable();
            $table->timestamp('suggestion_used_at')->nullable();
            $table->timestamps();

            $table->foreign('agent_id')
                ->references('id')
                ->on('support_agents')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_agent_ticket_suggestions');
    }
};
