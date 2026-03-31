<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WhatsappTickList;
use App\Models\WhatsappTicket;
use App\Models\User;
use Carbon\Carbon;

class WhatsappTickListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if we have any WhatsApp tickets
        $tickets = WhatsappTicket::all();
        if ($tickets->isEmpty()) {
            $this->command->info('No WhatsApp tickets found. Please create tickets first.');
            return;
        }

        // Check if we have any users
        $users = User::where('type', 'team')->get();
        if ($users->isEmpty()) {
            $this->command->info('No team users found. Please create users first.');
            return;
        }

        $sampleTickLists = [
            [
                'title' => 'Verify customer contact information',
                'description' => 'Check if phone number and email are correct and active',
                'priority' => 2,
                'status' => 'pending',
                'due_date' => Carbon::now()->addDays(2),
            ],
            [
                'title' => 'Research customer issue history',
                'description' => 'Look up previous tickets and interactions with this customer',
                'priority' => 3,
                'status' => 'pending',
                'due_date' => Carbon::now()->addDays(1),
            ],
            [
                'title' => 'Prepare response template',
                'description' => 'Create a professional response template for this type of inquiry',
                'priority' => 1,
                'status' => 'completed',
                'due_date' => Carbon::now()->subDays(1),
            ],
            [
                'title' => 'Escalate to senior support',
                'description' => 'Forward complex technical issue to senior support team',
                'priority' => 4,
                'status' => 'pending',
                'due_date' => Carbon::now()->addHours(4),
            ],
            [
                'title' => 'Update customer on progress',
                'description' => 'Send status update message to customer',
                'priority' => 2,
                'status' => 'pending',
                'due_date' => Carbon::now()->addDays(1),
            ],
            [
                'title' => 'Review solution documentation',
                'description' => 'Check if solution is documented for future reference',
                'priority' => 1,
                'status' => 'pending',
                'due_date' => Carbon::now()->addDays(3),
            ],
            [
                'title' => 'Schedule follow-up call',
                'description' => 'Arrange follow-up call to ensure issue is resolved',
                'priority' => 3,
                'status' => 'pending',
                'due_date' => Carbon::now()->addDays(5),
            ],
            [
                'title' => 'Update internal knowledge base',
                'description' => 'Add new solution to internal knowledge base',
                'priority' => 2,
                'status' => 'completed',
                'due_date' => Carbon::now()->subDays(2),
            ],
        ];

        foreach ($tickets as $ticket) {
            // Add 3-5 tick list items per ticket
            $ticketTickLists = array_rand(array_flip($sampleTickLists), rand(3, 5));
            
            foreach ($ticketTickLists as $tickListData) {
                WhatsappTickList::create([
                    'tenant_id' => $ticket->tenant_id,
                    'ticket_id' => $ticket->id,
                    'title' => $tickListData['title'],
                    'description' => $tickListData['description'],
                    'priority' => $tickListData['priority'],
                    'status' => $tickListData['status'],
                    'assigned_to' => $users->random()->id,
                    'due_date' => $tickListData['due_date'],
                    'created_by' => $users->random()->id,
                ]);
            }
        }

        $this->command->info('WhatsApp tick lists seeded successfully!');
    }
}



