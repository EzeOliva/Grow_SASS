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
        Schema::create('support_agent_unanswered_queries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id')->nullable()->index();
            $table->unsignedBigInteger('test_run_id')->nullable()->index();
            $table->unsignedInteger('unanswered_creatorid')->nullable()->index();
            $table->enum('unanswered_audience', ['team', 'client'])->default('team')->index();
            $table->longText('unanswered_question');
            $table->string('unanswered_reason', 120)->nullable()->index();
            $table->text('unanswered_reason_details')->nullable();
            $table->string('unanswered_status', 40)->default('open')->index();
            $table->unsignedInteger('resolved_by')->nullable()->index();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            $table->foreign('agent_id')
                ->references('id')
                ->on('support_agents')
                ->onDelete('set null');

            $table->foreign('test_run_id')
                ->references('id')
                ->on('support_agent_test_runs')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_agent_unanswered_queries');
    }
};
