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
        Schema::create('whatsapp_ticket_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('tenant_id')->comment('Multi-tenant support');
            $table->string('name')->comment('Ticket type name');
            $table->string('color')->default('#6c757d')->comment('Type color in hex format');
            $table->text('description')->nullable()->comment('Type description');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0)->comment('Sort order for display');
            $table->unsignedInteger('created_by')->nullable()->comment('User who created the type');
            $table->timestamps();
            
            // Indexes
            $table->index(['tenant_id', 'is_active']);
            $table->index(['tenant_id', 'sort_order']);
            $table->unique(['tenant_id', 'name']);
            
            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_ticket_types');
    }
};

