<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use App\Models\Tenant;

/**
 * @fileoverview Fix WhatsApp Database Connection Issues
 * @description Resolves database connection and table creation issues
 */
class FixWhatsAppDatabase extends Command
{
    protected $signature = 'whatsapp:fix-database {--tenant= : Specific tenant ID to fix}';
    protected $description = 'Fix WhatsApp database connection and table issues';

    public function handle()
    {
        $this->info('🔧 Fixing WhatsApp Database Issues...');
        $this->newLine();

        // Step 1: Check current database connections
        $this->info('📊 Checking Database Connections...');
        $this->checkDatabaseConnections();

        // Step 2: Set up tenant context
        $this->info('🏢 Setting Up Tenant Context...');
        $this->setupTenantContext();

        // Step 3: Create missing tables
        $this->info('🗃️ Creating Missing Tables...');
        $this->createMissingTables();

        // Step 4: Test connections
        $this->info('🧪 Testing Database Connections...');
        $this->testConnections();

        $this->info('✅ Database fixes completed!');
        return 0;
    }

    /**
     * @description Check current database connections
     */
    private function checkDatabaseConnections()
    {
        $connections = ['tenant', 'landlord', 'mysql'];
        
        foreach ($connections as $connection) {
            try {
                $dbName = DB::connection($connection)->getDatabaseName();
                $this->info("✅ {$connection}: " . ($dbName ?: 'No database selected'));
            } catch (\Exception $e) {
                $this->error("❌ {$connection}: " . $e->getMessage());
            }
        }
    }

    /**
     * @description Set up tenant context
     */
    private function setupTenantContext()
    {
        try {
            // Get available tenants
            $tenants = Tenant::all();
            
            if ($tenants->isEmpty()) {
                $this->warn('⚠️ No tenants found. Creating default tenant...');
                $this->createDefaultTenant();
                $tenants = Tenant::all();
            }

            // Use specified tenant or first available
            $tenantId = $this->option('tenant') ?: $tenants->first()->tenant_id;
            $tenant = Tenant::find($tenantId);
            
            if (!$tenant) {
                $this->error("❌ Tenant with ID {$tenantId} not found");
                return;
            }

            $this->info("✅ Using tenant: {$tenant->tenant_name} (ID: {$tenant->tenant_id})");

            // Set tenant context
            app()->instance('currentTenant', $tenant);

            // Configure tenant database connection
            $this->configureTenantDatabase($tenant);

        } catch (\Exception $e) {
            $this->error('❌ Error setting up tenant context: ' . $e->getMessage());
        }
    }

    /**
     * @description Create default tenant if none exist
     */
    private function createDefaultTenant()
    {
        try {
            $tenant = Tenant::create([
                'tenant_name' => 'Default Tenant',
                'domain' => 'localhost',
                'database' => 'growcrm_tenant_1'
            ]);
            
            $this->info("✅ Created default tenant: {$tenant->tenant_name}");
        } catch (\Exception $e) {
            $this->error('❌ Error creating default tenant: ' . $e->getMessage());
        }
    }

    /**
     * @description Configure tenant database connection
     */
    private function configureTenantDatabase($tenant)
    {
        try {
            // Get tenant database name
            $dbName = $tenant->database ?: "growcrm_tenant_{$tenant->tenant_id}";
            
            // Ensure database exists before connecting
            $this->ensureTenantDatabaseExists($dbName);

            // Update tenant connection configuration
            Config::set("database.connections.tenant.database", $dbName);
            
            // Purge and reconnect
            DB::purge('tenant');
            DB::reconnect('tenant');
            
            $this->info("✅ Configured tenant database: {$dbName}");
            
            // Test connection
            $testDb = DB::connection('tenant')->getDatabaseName();
            if ($testDb === $dbName) {
                $this->info("✅ Tenant database connection verified");
            } else {
                $this->warn("⚠️ Tenant database connection issue: expected {$dbName}, got {$testDb}");
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error configuring tenant database: ' . $e->getMessage());
        }
    }

    /**
     * @description Ensure the tenant database exists (create if missing)
     */
    private function ensureTenantDatabaseExists($dbName)
    {
        try {
            // Try default mysql connection first
            DB::connection('mysql')->statement(
                "CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            );
            $this->info("✅ Ensured database exists (mysql): {$dbName}");
            return;
        } catch (\Exception $e) {
            $this->warn('⚠️ Could not create database via mysql connection: ' . $e->getMessage());
        }

        try {
            // Fallback to landlord connection if available
            DB::connection('landlord')->statement(
                "CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            );
            $this->info("✅ Ensured database exists (landlord): {$dbName}");
        } catch (\Exception $e) {
            $this->error('❌ Could not ensure tenant database exists: ' . $e->getMessage());
        }
    }

    /**
     * @description Create missing WhatsApp tables
     */
    private function createMissingTables()
    {
        $requiredTables = [
            'whatsapp_tickets' => $this->getWhatsappTicketsSchema(),
            'whatsapp_messages' => $this->getWhatsappMessagesSchema(),
            'whatsapp_connections' => $this->getWhatsappConnectionsSchema(),
            'whatsapp_contacts' => $this->getWhatsappContactsSchema(),
            'whatsapp_tags' => $this->getWhatsappTagsSchema(),
            'whatsapp_ticket_types' => $this->getWhatsappTicketTypesSchema(),
            'whatsapp_line_configs' => $this->getWhatsappLineConfigsSchema(),
        ];

        foreach ($requiredTables as $tableName => $schema) {
            if (!Schema::connection('tenant')->hasTable($tableName)) {
                $this->info("📋 Creating table: {$tableName}");
                $this->createTable($tableName, $schema);
            } else {
                $this->info("✅ Table '{$tableName}' already exists");
            }
        }
    }

    /**
     * @description Create a table with the given schema
     */
    private function createTable($tableName, $schema)
    {
        try {
            DB::connection('tenant')->statement($schema);
            $this->info("✅ Table '{$tableName}' created successfully");
        } catch (\Exception $e) {
            $this->error("❌ Error creating table '{$tableName}': " . $e->getMessage());
        }
    }

    /**
     * @description Get WhatsApp tickets table schema
     */
    private function getWhatsappTicketsSchema()
    {
        return "
            CREATE TABLE `whatsapp_tickets` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `tenant_id` bigint(20) unsigned NOT NULL,
                `contact_name` varchar(255) NOT NULL,
                `contact_email` varchar(255) NOT NULL,
                `contact_phone` varchar(20) NOT NULL,
                `subject` varchar(500) NOT NULL,
                `description` text NOT NULL,
                `status` enum('open','in_progress','closed','on_hold') DEFAULT 'open',
                `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
                `channel` enum('whatsapp','email','phone') DEFAULT 'whatsapp',
                `agent_id` bigint(20) unsigned DEFAULT NULL,
                `ticket_type_id` bigint(20) unsigned DEFAULT NULL,
                `category` varchar(255) DEFAULT NULL,
                `internal_notes` text DEFAULT NULL,
                `first_response_at` timestamp NULL DEFAULT NULL,
                `closed_at` timestamp NULL DEFAULT NULL,
                `assigned_at` timestamp NULL DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `tenant_id` (`tenant_id`),
                KEY `status` (`status`),
                KEY `agent_id` (`agent_id`),
                KEY `created_at` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
    }

    /**
     * @description Get WhatsApp messages table schema
     */
    private function getWhatsappMessagesSchema()
    {
        return "
            CREATE TABLE `whatsapp_messages` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `tenant_id` bigint(20) unsigned NOT NULL,
                `ticket_id` bigint(20) unsigned NOT NULL,
                `sender_type` enum('agent','customer') NOT NULL,
                `sender_id` bigint(20) unsigned DEFAULT NULL,
                `sender_name` varchar(255) NOT NULL,
                `channel` enum('whatsapp','email') NOT NULL,
                `body` text NOT NULL,
                `status` enum('sent','delivered','read','failed') DEFAULT 'sent',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `tenant_id` (`tenant_id`),
                KEY `ticket_id` (`ticket_id`),
                KEY `sender_type` (`sender_type`),
                KEY `created_at` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
    }

    /**
     * @description Get WhatsApp connections table schema
     */
    private function getWhatsappConnectionsSchema()
    {
        return "
            CREATE TABLE `whatsapp_connections` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `tenant_id` bigint(20) unsigned NOT NULL,
                `name` varchar(255) NOT NULL,
                `phone_number` varchar(20) NOT NULL,
                `connection_type` enum('whatsapp_business','whatsapp_personal') DEFAULT 'whatsapp_business',
                `status` enum('connected','disconnected','connecting','error') DEFAULT 'disconnected',
                `api_key` varchar(255) DEFAULT NULL,
                `webhook_url` varchar(500) DEFAULT NULL,
                `qr_code_data` text DEFAULT NULL,
                `qr_code_generated_at` timestamp NULL DEFAULT NULL,
                `connected_at` timestamp NULL DEFAULT NULL,
                `last_activity_at` timestamp NULL DEFAULT NULL,
                `last_status_update` timestamp NULL DEFAULT NULL,
                `last_test_at` timestamp NULL DEFAULT NULL,
                `test_results` json DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `tenant_id` (`tenant_id`),
                KEY `status` (`status`),
                KEY `phone_number` (`phone_number`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
    }

    /**
     * @description Get WhatsApp contacts table schema
     */
    private function getWhatsappContactsSchema()
    {
        return "
            CREATE TABLE `whatsapp_contacts` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `tenant_id` bigint(20) unsigned NOT NULL,
                `name` varchar(255) NOT NULL,
                `email` varchar(255) DEFAULT NULL,
                `phone` varchar(20) NOT NULL,
                `whatsapp_id` varchar(255) DEFAULT NULL,
                `status` enum('active','inactive','blocked') DEFAULT 'active',
                `tags` json DEFAULT NULL,
                `notes` text DEFAULT NULL,
                `last_contact_at` timestamp NULL DEFAULT NULL,
                `total_tickets` int(11) DEFAULT 0,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `tenant_id` (`tenant_id`),
                KEY `phone` (`phone`),
                KEY `whatsapp_id` (`whatsapp_id`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
    }

    /**
     * @description Get WhatsApp tags table schema
     */
    private function getWhatsappTagsSchema()
    {
        return "
            CREATE TABLE `whatsapp_tags` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `tenant_id` bigint(20) unsigned NOT NULL,
                `name` varchar(100) NOT NULL,
                `color` varchar(7) DEFAULT '#3B82F6',
                `description` text DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `tenant_name_unique` (`tenant_id`,`name`),
                KEY `tenant_id` (`tenant_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
    }

    /**
     * @description Get WhatsApp ticket types table schema
     */
    private function getWhatsappTicketTypesSchema()
    {
        return "
            CREATE TABLE `whatsapp_ticket_types` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `tenant_id` bigint(20) unsigned NOT NULL,
                `name` varchar(100) NOT NULL,
                `description` text DEFAULT NULL,
                `color` varchar(7) DEFAULT '#10B981',
                `sort_order` int(11) DEFAULT 0,
                `is_active` tinyint(1) DEFAULT 1,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `tenant_name_unique` (`tenant_id`,`name`),
                KEY `tenant_id` (`tenant_id`),
                KEY `sort_order` (`sort_order`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
    }

    /**
     * @description Get WhatsApp line configs table schema
     */
    private function getWhatsappLineConfigsSchema()
    {
        return "
            CREATE TABLE `whatsapp_line_configs` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `tenant_id` bigint(20) unsigned NOT NULL,
                `name` varchar(255) NOT NULL,
                `connection_id` bigint(20) unsigned DEFAULT NULL,
                `assignment_mode` enum('round_robin','least_busy','specific_agent') DEFAULT 'round_robin',
                `default_agent_id` bigint(20) unsigned DEFAULT NULL,
                `auto_assign` tinyint(1) DEFAULT 1,
                `working_hours` json DEFAULT NULL,
                `timezone` varchar(50) DEFAULT 'UTC',
                `is_active` tinyint(1) DEFAULT 1,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `tenant_id` (`tenant_id`),
                KEY `connection_id` (`connection_id`),
                KEY `is_active` (`is_active`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
    }

    /**
     * @description Test database connections
     */
    private function testConnections()
    {
        $connections = ['tenant', 'landlord'];
        
        foreach ($connections as $connection) {
            try {
                $dbName = DB::connection($connection)->getDatabaseName();
                $this->info("✅ {$connection}: " . ($dbName ?: 'No database selected'));
                
                if ($dbName) {
                    // Test basic query
                    $result = DB::connection($connection)->select('SELECT 1 as test');
                    if ($result) {
                        $this->info("   ✅ Query test successful");
                    }
                }
                
            } catch (\Exception $e) {
                $this->error("❌ {$connection}: " . $e->getMessage());
            }
        }
    }
}
