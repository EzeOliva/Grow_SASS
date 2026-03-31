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
        Schema::create('whatsapp_tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('tenant_id')->comment('Multi-tenant support');
            $table->string('name')->comment('Tag name');
            $table->string('color')->default('#6c757d')->comment('Tag color in hex format');
            $table->text('description')->nullable()->comment('Tag description');
            $table->enum('type', ['contact', 'ticket', 'global'])->default('global')->comment('Tag type');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('created_by')->nullable()->comment('User who created the tag');
            $table->timestamps();
            
            // Indexes
            $table->index(['tenant_id', 'type']);
            $table->index(['tenant_id', 'is_active']);
            $table->unique(['tenant_id', 'name', 'type']);
            
            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_tags');
    }
};

