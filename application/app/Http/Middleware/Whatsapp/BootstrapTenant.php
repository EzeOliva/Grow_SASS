<?php

namespace App\Http\Middleware\Whatsapp;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


/**
 * @fileoverview Bootstrap tenant context and tenant DB connection for WhatsApp routes
 * @description Binds currentTenant and ensures tenant connection selects the correct database
 */
class BootstrapTenant
{
    public function handle(Request $request, Closure $next)
    {
        try {

            
            // Since we don't have a tenants table, use the default tenant database
            $dbName = env('TENANT_DB', 'growcrm_tenant_1');
            
            // Bind a default tenant instance for compatibility
            $tenant = new \App\Models\CustomTenant();
            app()->instance('currentTenant', $tenant);
            
            // Ensure the tenant database exists and is selected
            $this->ensureTenantDatabase($dbName);
            
            // Ensure minimal WhatsApp tables exist for listing/saving
            $this->ensureWhatsappTables();
            

            
            return $next($request);
            
        } catch (\Exception $e) {
            \Log::error('BootstrapTenant middleware failed: ' . $e->getMessage());
            \Log::error('BootstrapTenant middleware trace: ' . $e->getTraceAsString());
            

            
            // Re-throw the exception so it can be handled by Laravel
            throw $e;
        }
    }

    private function ensureTenantDatabase(string $dbName): void
    {
        try {
            \Log::info("Ensuring tenant database: $dbName");
            
            // First, connect to MySQL without selecting a database
            $connection = DB::connection('landlord');
            
            // Check if the database exists
            $result = $connection->select("SHOW DATABASES LIKE '$dbName'");
            
            if (empty($result)) {
                \Log::info("Database '$dbName' does not exist, creating it...");
                // Create the database if it doesn't exist
                $connection->statement("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                \Log::info("Database '$dbName' created successfully");
            } else {
                \Log::info("Database '$dbName' already exists");
            }
            
            // Now set the tenant connection to use this database
            Config::set('database.connections.tenant.database', $dbName);
            
            // Also set it directly in the connection
            $tenantConnection = DB::connection('tenant');
            $tenantConnection->getPdo()->exec("USE `$dbName`");
            
            // Clear the connection cache to ensure the new database is used
            DB::purge('tenant');
            
            // Verify the database is set
            $verifyConnection = DB::connection('tenant');
            $currentDb = $verifyConnection->getDatabaseName();
            \Log::info("Tenant database verification: $currentDb");
            
            \Log::info("Tenant database setup completed for: $dbName");
            
        } catch (\Exception $e) {
            \Log::error("Failed to ensure tenant database '$dbName': " . $e->getMessage());
            // Fallback: just set the database name
            Config::set('database.connections.tenant.database', $dbName);
        }
    }

    private function ensureWhatsappTables(): void
    {
        try {
            \Log::info("Ensuring WhatsApp tables exist...");
            
            $connection = DB::connection('tenant');
            $schema = Schema::connection('tenant');
            
            if (!$schema->hasTable('whatsapp_tickets')) {
                \Log::info("Creating whatsapp_tickets table...");
                $connection->statement(<<<SQL
CREATE TABLE IF NOT EXISTS `whatsapp_tickets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint(20) unsigned NOT NULL,
  `contact_name` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(255) DEFAULT NULL,
  `subject` varchar(500) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'open',
  `priority` varchar(50) DEFAULT 'medium',
  `channel` varchar(50) DEFAULT 'whatsapp',
  `opened_at` timestamp NULL DEFAULT NULL,
  `first_response_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `ticket_type_id` bigint(20) unsigned DEFAULT NULL,
  `agent_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tenant_id` (`tenant_id`),
  KEY `agent_id` (`agent_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
            } else {
                // Table exists, check if we need to add missing columns
                $this->ensureWhatsappTicketColumns();
            }

            if (!Schema::connection('tenant')->hasTable('whatsapp_messages')) {
                DB::connection('tenant')->statement(<<<SQL
CREATE TABLE IF NOT EXISTS `whatsapp_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint(20) unsigned NOT NULL,
  `ticket_id` bigint(20) unsigned NOT NULL,
  `sender_type` varchar(50) DEFAULT NULL,
  `sender_id` bigint(20) unsigned DEFAULT NULL,
  `sender_name` varchar(255) DEFAULT NULL,
  `channel` varchar(50) DEFAULT 'whatsapp',
  `body` text,
  `status` varchar(50) DEFAULT 'sent',
  `attachments` json DEFAULT NULL,
  `message_id` varchar(255) DEFAULT NULL,
  `reply_to_message_id` bigint(20) unsigned DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tenant_id` (`tenant_id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `created_at` (`created_at`),
  KEY `message_id` (`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
            }

            if (!$schema->hasTable('whatsapp_connections')) {
                $connection->statement(<<<SQL
CREATE TABLE IF NOT EXISTS `whatsapp_connections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint(20) unsigned NOT NULL,
  `connection_name` varchar(255) NOT NULL,
  `connection_type` enum('baileys', 'twilio', '360dialog', 'gupshup') NOT NULL,
  `status` enum('disconnected', 'connecting', 'connected', 'error') DEFAULT 'disconnected',
  `phone_number` varchar(255) DEFAULT NULL,
  `connection_data` text DEFAULT NULL,
  `qr_code` text DEFAULT NULL,
  `last_connected_at` timestamp NULL DEFAULT NULL,
  `last_error_at` timestamp NULL DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `webhook_config` json DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tenant_id` (`tenant_id`),
  KEY `status` (`status`),
  KEY `connection_type` (`connection_type`),
  KEY `phone_number` (`phone_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
            }

            if (!$schema->hasTable('whatsapp_tags')) {
                \Log::info("Creating whatsapp_tags table...");
                $connection->statement(<<<SQL
CREATE TABLE IF NOT EXISTS `whatsapp_tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `color` varchar(7) DEFAULT '#6c757d',
  `description` text DEFAULT NULL,
  `type` enum('contact', 'ticket', 'global') DEFAULT 'global',
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tenant_id` (`tenant_id`),
  KEY `name` (`name`),
  KEY `is_active` (`is_active`),
  KEY `type` (`type`),
  UNIQUE KEY `unique_tenant_name_type` (`tenant_id`, `name`, `type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
                \Log::info("whatsapp_tags table created successfully");
            } else {
                \Log::info("whatsapp_tags table already exists, checking for missing columns...");
                // Check if missing columns exist and add them
                if (!$schema->hasColumn('whatsapp_tags', 'type')) {
                    \Log::info("Adding missing 'type' column to whatsapp_tags table...");
                    $connection->statement('ALTER TABLE `whatsapp_tags` ADD COLUMN `type` enum("contact", "ticket", "global") DEFAULT "global" AFTER `description`');
                }
                
                if (!$schema->hasColumn('whatsapp_tags', 'created_by')) {
                    \Log::info("Adding missing 'created_by' column to whatsapp_tags table...");
                    $connection->statement('ALTER TABLE `whatsapp_tags` ADD COLUMN `created_by` bigint(20) unsigned DEFAULT NULL AFTER `is_active`');
                }
            }

            if (!$schema->hasTable('whatsapp_ticket_types')) {
                $connection->statement(<<<SQL
CREATE TABLE IF NOT EXISTS `whatsapp_ticket_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(7) DEFAULT '#007bff',
  `sort_order` int DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tenant_id` (`tenant_id`),
  KEY `name` (`name`),
  KEY `sort_order` (`sort_order`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
            }

            if (!$schema->hasTable('whatsapp_line_configs')) {
                $connection->statement(<<<SQL
CREATE TABLE IF NOT EXISTS `whatsapp_line_configs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint(20) unsigned NOT NULL,
  `connection_id` bigint(20) unsigned NOT NULL,
  `line_name` varchar(255) NOT NULL,
  `assignment_mode` enum('manual', 'auto_round_robin', 'auto_load_balanced') DEFAULT 'manual',
  `auto_assign_enabled` tinyint(1) DEFAULT 0,
  `welcome_message` text DEFAULT NULL,
  `closure_message` text DEFAULT NULL,
  `inactivity_message` text DEFAULT NULL,
  `inactivity_timeout_minutes` int DEFAULT 1440,
  `auto_assign_agents` json DEFAULT NULL,
  `routing_rules` json DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tenant_id` (`tenant_id`),
  KEY `connection_id` (`connection_id`),
  KEY `is_active` (`is_active`),
  KEY `assignment_mode` (`assignment_mode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
            }

            if (!$schema->hasTable('whatsapp_ticket_tags')) {
                $connection->statement(<<<SQL
CREATE TABLE IF NOT EXISTS `whatsapp_ticket_tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint(20) unsigned NOT NULL,
  `tag_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `tag_id` (`tag_id`),
  KEY `ticket_tag_unique` (`ticket_id`, `tag_id`),
  UNIQUE KEY `ticket_tag_unique` (`ticket_id`, `tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
            }

            if (!$schema->hasTable('whatsapp_quick_templates')) {
                $connection->statement(<<<SQL
CREATE TABLE IF NOT EXISTS `whatsapp_quick_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category` varchar(255) DEFAULT 'general',
  `shortcut` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int DEFAULT 0,
  `usage_count` int DEFAULT 0,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tenant_id` (`tenant_id`),
  KEY `category` (`category`),
  KEY `is_active` (`is_active`),
  KEY `sort_order` (`sort_order`),
  KEY `usage_count` (`usage_count`),
  UNIQUE KEY `tenant_name_unique` (`tenant_id`, `name`),
  UNIQUE KEY `tenant_shortcut_unique` (`tenant_id`, `shortcut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
            }
            
            \Log::info("WhatsApp tables setup completed successfully");
        } catch (\Throwable $e) {
            \Log::error("Failed to ensure WhatsApp tables: " . $e->getMessage());
            // Do not block request if cannot ensure tables
        }
    }
    
    /**
     * @description Ensure all required columns exist in the whatsapp_tickets table
     */
    private function ensureWhatsappTicketColumns(): void
    {
        try {
            $connection = DB::connection('tenant');
            $schema = Schema::connection('tenant');
            
            // Check and add missing columns
            if (!$schema->hasColumn('whatsapp_tickets', 'ticket_type_id')) {
                $connection->statement('ALTER TABLE `whatsapp_tickets` ADD COLUMN `ticket_type_id` bigint(20) unsigned DEFAULT NULL AFTER `closed_at`');
                $connection->statement('ALTER TABLE `whatsapp_tickets` ADD INDEX `ticket_type_id` (`ticket_type_id`)');
            }
            
            if (!$schema->hasColumn('whatsapp_tickets', 'agent_id')) {
                $connection->statement('ALTER TABLE `whatsapp_tickets` ADD COLUMN `agent_id` bigint(20) unsigned DEFAULT NULL AFTER `ticket_type_id`');
                $connection->statement('ALTER TABLE `whatsapp_tickets` ADD INDEX `agent_id` (`agent_id`)');
            }
            
            if (!$schema->hasColumn('whatsapp_tickets', 'channel')) {
                $connection->statement('ALTER TABLE `whatsapp_tickets` ADD COLUMN `channel` varchar(50) DEFAULT "whatsapp" AFTER `priority`');
            }
            
            if (!$schema->hasColumn('whatsapp_tickets', 'tags')) {
                $connection->statement('ALTER TABLE `whatsapp_tickets` ADD COLUMN `tags` text DEFAULT NULL AFTER `subject`');
            }
            
            if (!$schema->hasColumn('whatsapp_tickets', 'whatsapp_number')) {
                $connection->statement('ALTER TABLE `whatsapp_tickets` ADD COLUMN `whatsapp_number` varchar(255) DEFAULT NULL AFTER `internal_notes`');
            }
            
            if (!$schema->hasColumn('whatsapp_tickets', 'internal_notes')) {
                $connection->statement('ALTER TABLE `whatsapp_tickets` ADD COLUMN `internal_notes` text DEFAULT NULL AFTER `closed_at`');
            }
            
            if (!$schema->hasColumn('whatsapp_tickets', 'opened_at')) {
                $connection->statement('ALTER TABLE `whatsapp_tickets` ADD COLUMN `opened_at` timestamp NULL DEFAULT NULL AFTER `channel`');
            }
            
            if (!$schema->hasColumn('whatsapp_tickets', 'first_response_at')) {
                $connection->statement('ALTER TABLE `whatsapp_tickets` ADD COLUMN `first_response_at` timestamp NULL DEFAULT NULL AFTER `opened_at`');
            }
            
            if (!$schema->hasColumn('whatsapp_tickets', 'closed_at')) {
                $connection->statement('ALTER TABLE `whatsapp_tickets` ADD COLUMN `closed_at` timestamp NULL DEFAULT NULL AFTER `first_response_at`');
            }
            
            if (!$schema->hasColumn('whatsapp_tickets', 'category')) {
                $connection->statement('ALTER TABLE `whatsapp_tickets` ADD COLUMN `category` varchar(255) DEFAULT NULL AFTER `priority`');
            }
            
            if (!$schema->hasColumn('whatsapp_tickets', 'company')) {
                $connection->statement('ALTER TABLE `whatsapp_tickets` ADD COLUMN `company` varchar(255) DEFAULT NULL AFTER `category`');
            }
            
            if (!$schema->hasColumn('whatsapp_tickets', 'line_config_id')) {
                $connection->statement('ALTER TABLE `whatsapp_tickets` ADD COLUMN `line_config_id` bigint(20) unsigned DEFAULT NULL AFTER `ticket_type_id`');
            }
            
            if (!$schema->hasColumn('whatsapp_tickets', 'last_activity_at')) {
                $connection->statement('ALTER TABLE `whatsapp_tickets` ADD COLUMN `last_activity_at` timestamp NULL DEFAULT NULL AFTER `closed_at`');
            }
            
            if (!$schema->hasColumn('whatsapp_tickets', 'auto_close_scheduled_at')) {
                $connection->statement('ALTER TABLE `whatsapp_tickets` ADD COLUMN `auto_close_scheduled_at` timestamp NULL DEFAULT NULL AFTER `last_activity_at`');
            }
            
            \Log::info("WhatsApp tables setup completed successfully");
            
        } catch (\Throwable $e) {
            \Log::error("Failed to ensure WhatsApp tables: " . $e->getMessage());
            // Do not block request if cannot ensure columns
        }
    }
}


