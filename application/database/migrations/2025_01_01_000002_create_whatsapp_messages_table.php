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
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('ticket_id')->comment('Reference to whatsapp_tickets table');
            $table->enum('sender_type', ['client', 'agent', 'system'])->comment('Who sent the message');
            $table->unsignedInteger('sender_id')->nullable()->comment('User ID if agent, null if client');
            $table->string('sender_name')->comment('Name of the sender');
            $table->enum('channel', ['whatsapp', 'email'])->default('whatsapp');
            $table->text('body')->comment('Message content');
            $table->json('attachments')->nullable()->comment('JSON array of attachment data');
            $table->string('message_id')->nullable()->comment('External message ID (WhatsApp/Email)');
            $table->enum('status', ['sending', 'sent', 'delivered', 'read', 'failed'])->default('sending');
            $table->unsignedBigInteger('reply_to_message_id')->nullable()->comment('Reply to specific message');
            $table->timestamp('read_at')->nullable()->comment('When message was read');
            $table->json('metadata')->nullable()->comment('Additional message metadata');
            $table->timestamps();
            
            // Indexes
            $table->index('ticket_id');
            $table->index(['ticket_id', 'created_at']);
            $table->index(['channel', 'status']);
            $table->index('message_id');
            
            // Foreign keys
            $table->foreign('ticket_id')->references('id')->on('whatsapp_tickets')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
