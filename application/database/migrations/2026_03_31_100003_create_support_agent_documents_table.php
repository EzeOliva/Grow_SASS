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
        Schema::create('support_agent_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id');
            $table->string('agent_document_name');
            $table->string('agent_document_original_name')->nullable();
            $table->string('agent_document_mime', 120)->nullable();
            $table->unsignedBigInteger('agent_document_size')->nullable();
            $table->string('agent_document_disk', 60)->nullable();
            $table->string('agent_document_path')->nullable();
            $table->enum('agent_document_visibility', ['team', 'client', 'everyone'])->default('team')->index();
            $table->enum('agent_document_status', ['pending', 'processing', 'ready', 'failed'])->default('pending')->index();
            $table->longText('agent_document_extracted_text')->nullable();
            $table->unsignedInteger('agent_document_chunks')->default(0);
            $table->timestamp('agent_document_last_indexed_at')->nullable();
            $table->text('agent_document_error')->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('support_agent_documents');
    }
};
