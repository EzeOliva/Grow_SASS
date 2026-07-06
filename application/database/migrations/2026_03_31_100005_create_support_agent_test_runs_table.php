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
        Schema::create('support_agent_test_runs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id')->nullable()->index();
            $table->unsignedInteger('test_creatorid')->nullable()->index();
            $table->enum('test_audience', ['team', 'client'])->default('team')->index();
            $table->longText('test_question');
            $table->longText('test_answer')->nullable();
            $table->longText('test_sources')->nullable();
            $table->string('response_status', 40)->default('answered')->index();
            $table->text('unanswered_reasons')->nullable();
            $table->string('model_name', 120)->nullable();
            $table->integer('model_tokens_prompt')->nullable();
            $table->integer('model_tokens_completion')->nullable();
            $table->integer('model_tokens_total')->nullable();
            $table->text('error_message')->nullable();
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
        Schema::dropIfExists('support_agent_test_runs');
    }
};
