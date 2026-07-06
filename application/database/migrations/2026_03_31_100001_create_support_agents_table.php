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
        Schema::create('support_agents', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('tenant_id')->nullable()->index();
            $table->unsignedInteger('agent_creatorid')->nullable()->index();
            $table->string('agent_name');
            $table->text('agent_identity_prompt');
            $table->enum('agent_visibility', ['team', 'client', 'everyone'])->default('team')->index();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('allow_client_chat')->default(false);
            $table->boolean('allow_ticket_suggestions')->default(false);
            $table->boolean('allow_document_sources')->default(true);
            $table->json('agent_settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_agents');
    }
};
