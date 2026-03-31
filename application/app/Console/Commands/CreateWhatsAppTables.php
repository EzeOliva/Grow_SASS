<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class CreateWhatsAppTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:create-tables {--force : Force creation even if tables exist} {--database= : Specify database name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create WhatsApp tables if they do not exist';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking WhatsApp tables...');

        // Get database name from option or try to detect it
        $databaseName = $this->option('database');
        
        if (!$databaseName) {
            // Try to get database name from current connection
            try {
                $databaseName = DB::connection()->getDatabaseName();
                if (!$databaseName) {
                    $this->error('No database selected. Please specify database with --database option or ensure proper connection.');
                    $this->info('Example: php artisan whatsapp:create-tables --database=crm_fajl3a_1755880023');
                    return 1;
                }
            } catch (\Exception $e) {
                $this->error('Database connection error: ' . $e->getMessage());
                return 1;
            }
        }

        $this->info("Using database: {$databaseName}");

        // Ensure we're using the correct database
        try {
            DB::statement("USE `{$databaseName}`");
            $this->info("✓ Switched to database: {$databaseName}");
        } catch (\Exception $e) {
            $this->error("Failed to switch to database {$databaseName}: " . $e->getMessage());
            return 1;
        }

        // Check if tables already exist
        $tablesExist = Schema::hasTable('whatsapp_tickets') && 
                      Schema::hasTable('whatsapp_messages') && 
                      Schema::hasTable('whatsapp_connections');

        if ($tablesExist && !$this->option('force')) {
            $this->warn('WhatsApp tables already exist. Use --force to recreate them.');
            return 0;
        }

        if ($tablesExist && $this->option('force')) {
            $this->info('Dropping existing WhatsApp tables...');
            Schema::dropIfExists('whatsapp_messages');
            Schema::dropIfExists('whatsapp_tickets');
            Schema::dropIfExists('whatsapp_connections');
        }

        $this->info('Creating WhatsApp tables...');

        try {
            // Create whatsapp_tickets table (skip if exists)
            if (!Schema::hasTable('whatsapp_tickets')) {
                $this->createTicketsTable();
                $this->info('✓ Created whatsapp_tickets table');
            } else {
                $this->info('↷ whatsapp_tickets already exists (skipped)');
            }

            // Create whatsapp_messages table (skip if exists)
            if (!Schema::hasTable('whatsapp_messages')) {
                $this->createMessagesTable();
                $this->info('✓ Created whatsapp_messages table');
            } else {
                $this->info('↷ whatsapp_messages already exists (skipped)');
            }

            // Create whatsapp_connections table (skip if exists)
            if (!Schema::hasTable('whatsapp_connections')) {
                $this->createConnectionsTable();
                $this->info('✓ Created whatsapp_connections table');
            } else {
                $this->info('↷ whatsapp_connections already exists (skipped)');
            }

            // Create whatsapp_ticket_types table if missing
            if (!Schema::hasTable('whatsapp_ticket_types')) {
                $this->createTicketTypesTable();
                $this->info('✓ Created whatsapp_ticket_types table');
            } else {
                $this->info('↷ whatsapp_ticket_types already exists (skipped)');
            }

            // Insert sample data
            $this->insertSampleData();
            $this->info('✓ Inserted sample data');

            // Seed default ticket types (idempotent)
            $this->seedDefaultTicketTypes();
            $this->info('✓ Seeded default ticket types');

            $this->info('WhatsApp tables created successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error('Error creating tables: ' . $e->getMessage());
            return 1;
        }
    }

    private function createTicketTypesTable()
    {
        DB::statement("
            CREATE TABLE `whatsapp_ticket_types` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `tenant_id` int(10) unsigned NOT NULL,
                `name` varchar(255) NOT NULL,
                `color` varchar(32) NOT NULL DEFAULT '#6c757d',
                `description` text DEFAULT NULL,
                `is_active` tinyint(1) NOT NULL DEFAULT 1,
                `sort_order` int(11) NOT NULL DEFAULT 0,
                `created_by` int(11) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uniq_tenant_name` (`tenant_id`,`name`),
                KEY `idx_tenant_active` (`tenant_id`,`is_active`),
                KEY `idx_tenant_sort` (`tenant_id`,`sort_order`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private function seedDefaultTicketTypes(): void
    {
        try {
            if (!Schema::hasTable('whatsapp_ticket_types')) {
                return;
            }
            $defaults = [
                ['tenant_id' => 1, 'name' => 'Bug', 'color' => '#e03131', 'description' => 'Bug report'],
                ['tenant_id' => 1, 'name' => 'Inquiry', 'color' => '#1971c2', 'description' => 'General question'],
                ['tenant_id' => 1, 'name' => 'Feature', 'color' => '#2f9e44', 'description' => 'Feature request'],
            ];
            foreach ($defaults as $row) {
                $exists = DB::table('whatsapp_ticket_types')
                    ->where('tenant_id', $row['tenant_id'])
                    ->where('name', $row['name'])
                    ->exists();
                if (!$exists) {
                    DB::table('whatsapp_ticket_types')->insert(array_merge($row, [
                        'is_active' => 1,
                        'sort_order' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]));
                }
            }
        } catch (\Throwable $e) {
            $this->warn('Seeding default ticket types failed: '.$e->getMessage());
        }
    }

    private function createTicketsTable()
    {
        DB::statement("
            CREATE TABLE `whatsapp_tickets` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `tenant_id` int(10) unsigned NOT NULL COMMENT 'Multi-tenant support - references tenants table',
                `contact_name` varchar(255) NOT NULL COMMENT 'Contact name for this ticket',
                `contact_email` varchar(255) DEFAULT NULL COMMENT 'Contact email',
                `contact_phone` varchar(255) DEFAULT NULL COMMENT 'Contact phone number',
                `agent_id` int(11) DEFAULT NULL COMMENT 'Assigned agent from users table',
                `status` enum('open','in_progress','closed') NOT NULL DEFAULT 'open',
                `channel` enum('whatsapp','email') NOT NULL DEFAULT 'whatsapp',
                `subject` varchar(255) DEFAULT NULL,
                `tags` text DEFAULT NULL COMMENT 'JSON array of tags',
                `opened_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'First message timestamp',
                `first_response_at` timestamp NULL DEFAULT NULL COMMENT 'Agent first response',
                `closed_at` timestamp NULL DEFAULT NULL COMMENT 'Ticket closure timestamp',
                `internal_notes` text DEFAULT NULL COMMENT 'Agent notes',
                `whatsapp_number` varchar(255) DEFAULT NULL COMMENT 'WhatsApp number for this ticket',
                `priority` varchar(255) NOT NULL DEFAULT 'medium' COMMENT 'low, medium, high, urgent',
                `category` varchar(255) DEFAULT NULL COMMENT 'Ticket category',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_tenant_status` (`tenant_id`, `status`),
                KEY `idx_tenant_agent` (`tenant_id`, `agent_id`),
                KEY `idx_tenant_email` (`tenant_id`, `contact_email`),
                KEY `idx_tenant_channel` (`tenant_id`, `channel`),
                KEY `idx_opened_at` (`opened_at`),
                KEY `idx_first_response_at` (`first_response_at`),
                KEY `idx_closed_at` (`closed_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private function createMessagesTable()
    {
        DB::statement("
            CREATE TABLE `whatsapp_messages` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `ticket_id` bigint(20) unsigned NOT NULL COMMENT 'Reference to whatsapp_tickets table',
                `sender_type` enum('client','agent') NOT NULL COMMENT 'Who sent the message',
                `sender_id` int(11) DEFAULT NULL COMMENT 'User ID if agent, null if client',
                `sender_name` varchar(255) NOT NULL COMMENT 'Name of the sender',
                `channel` enum('whatsapp','email') NOT NULL DEFAULT 'whatsapp',
                `body` text NOT NULL COMMENT 'Message content',
                `attachments` json DEFAULT NULL COMMENT 'JSON array of attachment data',
                `message_id` varchar(255) DEFAULT NULL COMMENT 'External message ID (WhatsApp/Email)',
                `status` enum('sent','delivered','read','failed') NOT NULL DEFAULT 'sent',
                `read_at` timestamp NULL DEFAULT NULL COMMENT 'When message was read',
                `metadata` json DEFAULT NULL COMMENT 'Additional message metadata',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_ticket_id` (`ticket_id`),
                KEY `idx_ticket_created` (`ticket_id`, `created_at`),
                KEY `idx_channel_status` (`channel`, `status`),
                KEY `idx_message_id` (`message_id`),
                CONSTRAINT `fk_whatsapp_messages_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `whatsapp_tickets` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private function createConnectionsTable()
    {
        DB::statement("
            CREATE TABLE `whatsapp_connections` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `tenant_id` int(10) unsigned NOT NULL COMMENT 'Multi-tenant support - references tenants table',
                `connection_name` varchar(255) NOT NULL COMMENT 'Friendly name for this connection',
                `connection_type` enum('baileys','twilio','360dialog','gupshup') NOT NULL COMMENT 'WhatsApp connection method',
                `status` enum('disconnected','connecting','connected','error') NOT NULL DEFAULT 'disconnected',
                `phone_number` varchar(255) DEFAULT NULL COMMENT 'WhatsApp phone number',
                `connection_data` text DEFAULT NULL COMMENT 'JSON data for connection (API keys, tokens, etc.)',
                `qr_code` text DEFAULT NULL COMMENT 'QR code data for Baileys',
                `last_connected_at` timestamp NULL DEFAULT NULL COMMENT 'Last successful connection',
                `last_error_at` timestamp NULL DEFAULT NULL COMMENT 'Last connection error',
                `error_message` text DEFAULT NULL COMMENT 'Last error message',
                `webhook_config` json DEFAULT NULL COMMENT 'Webhook configuration',
                `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Whether this connection is active',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_tenant_status` (`tenant_id`, `status`),
                KEY `idx_tenant_type` (`tenant_id`, `connection_type`),
                KEY `idx_phone_number` (`phone_number`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private function insertSampleData()
    {
        // Insert sample connection
        DB::table('whatsapp_connections')->insert([
            'tenant_id' => 1,
            'connection_name' => 'Default WhatsApp Connection',
            'connection_type' => 'baileys',
            'status' => 'disconnected',
            'phone_number' => '+1234567890',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
