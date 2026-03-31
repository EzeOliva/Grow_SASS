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
        Schema::create('whatsapp_quick_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('tenant_id')->comment('Multi-tenant support');
            $table->string('name')->comment('Template name');
            $table->text('content')->comment('Template content');
            $table->string('category')->default('general')->comment('Template category');
            $table->string('shortcut')->nullable()->comment('Keyboard shortcut for quick access');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0)->comment('Sort order for display');
            $table->integer('usage_count')->default(0)->comment('Number of times template used');
            $table->unsignedInteger('created_by')->nullable()->comment('User who created the template');
            $table->timestamps();
            
            // Indexes
            $table->index(['tenant_id', 'category']);
            $table->index(['tenant_id', 'is_active']);
            $table->index(['tenant_id', 'sort_order']);
            $table->index(['tenant_id', 'usage_count']);
            $table->unique(['tenant_id', 'name']);
            $table->unique(['tenant_id', 'shortcut']);
            
            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_quick_templates');
    }
};

