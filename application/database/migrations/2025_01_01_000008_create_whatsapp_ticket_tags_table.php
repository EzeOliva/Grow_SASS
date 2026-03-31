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
        Schema::create('whatsapp_ticket_tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id')->comment('Reference to whatsapp_tickets table');
            $table->unsignedBigInteger('tag_id')->comment('Reference to whatsapp_tags table');
            $table->timestamps();
            
            // Indexes
            $table->index(['ticket_id', 'tag_id']);
            $table->index('ticket_id');
            $table->index('tag_id');
            
            // Foreign keys
            $table->foreign('ticket_id')->references('id')->on('whatsapp_tickets')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('whatsapp_tags')->onDelete('cascade');
            
            // Unique constraint to prevent duplicate tag assignments
            $table->unique(['ticket_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_ticket_tags');
    }
};

