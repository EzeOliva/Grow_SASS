<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\WhatsappTicketSeeder;

/**
 * @fileoverview Seed WhatsApp Tickets Command - Populates the database with sample WhatsApp tickets
 * @description Artisan command to quickly populate the whatsapp_tickets table for testing
 */
class SeedWhatsappTickets extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'whatsapp:seed-tickets {--count=15 : Number of tickets to create}';

    /**
     * The console command description.
     */
    protected $description = 'Seed the database with sample WhatsApp tickets for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting WhatsApp tickets seeding...');
        
        try {
            $seeder = new WhatsappTicketSeeder();
            $seeder->run();
            
            $this->info('WhatsApp tickets seeded successfully!');
            $this->info('You can now view tickets at /whatsapp/tickets');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error seeding WhatsApp tickets: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            
            return Command::FAILURE;
        }
    }
}


