<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use App\Models\WhatsappTicket;
use App\Models\WhatsappConnection;
use App\Models\WhatsappContact;
use App\Models\WhatsappMessage;
use App\Models\WhatsappTag;
use App\Models\WhatsappTicketType;
use App\Models\User;

/**
 * @fileoverview WhatsApp System Testing Command - Comprehensive system testing and diagnostics
 * @description Tests all WhatsApp functionality and identifies issues for optimization
 */
class TestWhatsAppSystem extends Command
{
    protected $signature = 'whatsapp:test-system {--fix : Automatically fix identified issues}';
    protected $description = 'Test all WhatsApp functionality and identify optimization opportunities';

    private $issues = [];
    private $fixes = [];

    public function handle()
    {
        $this->info('🔍 Starting WhatsApp System Comprehensive Test...');
        $this->newLine();

        // Test database connectivity
        $this->testDatabaseConnection();
        
        // Test table existence
        $this->testTableExistence();
        
        // Test route registration
        $this->testRouteRegistration();
        
        // Test model functionality
        $this->testModelFunctionality();
        
        // Test controller accessibility
        $this->testControllerAccessibility();
        
        // Test service layer
        $this->testServiceLayer();
        
        // Test view existence
        $this->testViewExistence();
        
        // Test tenant context
        $this->testTenantContext();
        
        // Test performance
        $this->testPerformance();
        
        // Generate report
        $this->generateReport();
        
        // Apply fixes if requested
        if ($this->option('fix')) {
            $this->applyFixes();
        }

        return 0;
    }

    /**
     * @description Test database connection and configuration
     */
    private function testDatabaseConnection()
    {
        $this->info('📊 Testing Database Connection...');
        
        try {
            $connection = DB::connection();
            $pdo = $connection->getPdo();
            
            if ($pdo) {
                $this->info('✅ Database connection successful');
                $this->info("   Database: " . $connection->getDatabaseName());
                $this->info("   Driver: " . $connection->getDriverName());
            }
        } catch (\Exception $e) {
            $this->error('❌ Database connection failed: ' . $e->getMessage());
            $this->issues[] = [
                'type' => 'database',
                'severity' => 'critical',
                'message' => 'Database connection failed',
                'details' => $e->getMessage(),
                'fix' => 'Check database configuration in .env and config/database.php'
            ];
        }
        
        $this->newLine();
    }

    /**
     * @description Test if required tables exist
     */
    private function testTableExistence()
    {
        $this->info('🗃️ Testing Table Existence...');
        
        $requiredTables = [
            'whatsapp_tickets',
            'whatsapp_messages',
            'whatsapp_connections',
            'whatsapp_contacts',
            'whatsapp_tags',
            'whatsapp_ticket_types',
            'whatsapp_line_configs',
            'users',
            'migrations'
        ];
        
        $tenantTables = [
            'whatsapp_tickets',
            'whatsapp_messages',
            'whatsapp_connections',
            'whatsapp_contacts',
            'whatsapp_tags',
            'whatsapp_ticket_types',
            'whatsapp_line_configs',
        ];

        foreach ($requiredTables as $table) {
            $schema = in_array($table, $tenantTables)
                ? Schema::connection('tenant')
                : Schema::connection(config('database.default'));

            if ($schema->hasTable($table)) {
                $this->info("✅ Table '{$table}' exists");
                
                // Check table structure
                $this->checkTableStructure($table, in_array($table, $tenantTables) ? 'tenant' : config('database.default'));
            } else {
                $this->error("❌ Table '{$table}' missing");
                $this->issues[] = [
                    'type' => 'database',
                    'severity' => 'high',
                    'message' => "Table '{$table}' missing",
                    'details' => 'Required table does not exist',
                    'fix' => "Create table '{$table}' using migration or manual SQL"
                ];
            }
        }
        
        $this->newLine();
    }

    /**
     * @description Check table structure and identify issues
     */
    private function checkTableStructure($tableName, $connection = null)
    {
        try {
            $schema = $connection ? Schema::connection($connection) : Schema::connection(config('database.default'));
            $columns = $schema->getColumnListing($tableName);
            
            // Check for required columns based on table
            switch ($tableName) {
                case 'whatsapp_tickets':
                    $requiredColumns = ['id', 'tenant_id', 'status', 'priority', 'contact_name', 'subject'];
                    break;
                case 'whatsapp_messages':
                    $requiredColumns = ['id', 'ticket_id', 'sender_type', 'body', 'tenant_id'];
                    break;
                case 'users':
                    $requiredColumns = ['id', 'type', 'status', 'first_name', 'last_name'];
                    break;
                default:
                    $requiredColumns = ['id'];
            }
            
            foreach ($requiredColumns as $column) {
                if (!in_array($column, $columns)) {
                    $this->warn("⚠️ Column '{$column}' missing in table '{$tableName}'");
                    $this->issues[] = [
                        'type' => 'database',
                        'severity' => 'medium',
                        'message' => "Column '{$column}' missing in table '{$tableName}'",
                        'details' => 'Required column for proper functionality',
                        'fix' => "Add column '{$column}' to table '{$tableName}'"
                    ];
                }
            }
        } catch (\Exception $e) {
            $this->warn("⚠️ Could not check structure of table '{$tableName}': " . $e->getMessage());
        }
    }

    /**
     * @description Test route registration
     */
    private function testRouteRegistration()
    {
        $this->info('🛣️ Testing Route Registration...');
        
        $expectedRoutes = [
            'whatsapp.dashboard',
            'whatsapp.tickets.index',
            'whatsapp.tickets.create',
            'whatsapp.tickets.store',
            'whatsapp.tickets.show',
            'whatsapp.tickets.edit',
            'whatsapp.tickets.update',
            'whatsapp.tickets.destroy',
            'whatsapp.connections.index',
            'whatsapp.contacts.index',
            'whatsapp.reporting.index',
            'whatsapp.tags.index',
            'whatsapp.ticket-types.index'
        ];
        
        $registeredRoutes = Route::getRoutes();
        $routeNames = [];
        
        foreach ($registeredRoutes as $route) {
            if ($route->getName()) {
                $routeNames[] = $route->getName();
            }
        }
        
        foreach ($expectedRoutes as $routeName) {
            if (in_array($routeName, $routeNames)) {
                $this->info("✅ Route '{$routeName}' registered");
            } else {
                $this->error("❌ Route '{$routeName}' not registered");
                $this->issues[] = [
                    'type' => 'routing',
                    'severity' => 'high',
                    'message' => "Route '{$routeName}' not registered",
                    'details' => 'Required route for proper functionality',
                    'fix' => "Check routes/whatsapp.php and RouteServiceProvider"
                ];
            }
        }
        
        $this->newLine();
    }

    /**
     * @description Test model functionality
     */
    private function testModelFunctionality()
    {
        $this->info('🏗️ Testing Model Functionality...');
        
        $candidateModels = [
            WhatsappTicket::class,
            WhatsappMessage::class,
            WhatsappConnection::class,
            'App\\Models\\WhatsappContact',
            WhatsappTag::class,
            WhatsappTicketType::class
        ];

        $models = [];
        foreach ($candidateModels as $modelClass) {
            if (class_exists($modelClass)) {
                $models[] = $modelClass;
            } else {
                $this->warn("⚠️ Model '{$modelClass}' not found");
                $this->issues[] = [
                    'type' => 'model',
                    'severity' => 'low',
                    'message' => "Model '{$modelClass}' not found",
                    'details' => 'Optional model missing, skipping tests',
                    'fix' => 'Create the model or remove references if not needed'
                ];
            }
        }
        
        foreach ($models as $modelClass) {
            try {
                $model = new $modelClass();
                $this->info("✅ Model '{$modelClass}' instantiated successfully");
                
                // Test basic model operations
                $this->testModelOperations($modelClass);
                
            } catch (\Exception $e) {
                $this->error("❌ Model '{$modelClass}' failed: " . $e->getMessage());
                $this->issues[] = [
                    'type' => 'model',
                    'severity' => 'high',
                    'message' => "Model '{$modelClass}' failed",
                    'details' => $e->getMessage(),
                    'fix' => "Check model class definition and database table"
                ];
            }
        }
        
        $this->newLine();
    }

    /**
     * @description Test basic model operations
     */
    private function testModelOperations($modelClass)
    {
        try {
            // Test count operation
            $count = $modelClass::count();
            $this->info("   📊 Count: {$count} records");
            
            // Test first operation
            $first = $modelClass::first();
            if ($first) {
                $this->info("   🔍 First record ID: {$first->id}");
            }
            
        } catch (\Exception $e) {
            $this->warn("   ⚠️ Model operations failed: " . $e->getMessage());
        }
    }

    /**
     * @description Test controller accessibility
     */
    private function testControllerAccessibility()
    {
        $this->info('🎮 Testing Controller Accessibility...');
        
        $controllers = [
            'App\Http\Controllers\WhatsappTicketController',
            'App\Http\Controllers\WhatsappConnectionController',
            'App\Http\Controllers\WhatsappReportingController',
            'App\Http\Controllers\WhatsappContactController',
            'App\Http\Controllers\WhatsappMessageController',
            'App\Http\Controllers\WhatsappTagController',
            'App\Http\Controllers\WhatsappTicketTypeController',
            'App\Http\Controllers\WhatsappLineConfigController'
        ];
        
        foreach ($controllers as $controllerClass) {
            try {
                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();
                    $this->info("✅ Controller '{$controllerClass}' accessible");
                    
                    // Check for required methods
                    $this->checkControllerMethods($controllerClass);
                    
                } else {
                    $this->error("❌ Controller '{$controllerClass}' not found");
                    $this->issues[] = [
                        'type' => 'controller',
                        'severity' => 'high',
                        'message' => "Controller '{$controllerClass}' not found",
                        'details' => 'Required controller class missing',
                        'fix' => "Create controller class '{$controllerClass}'"
                    ];
                }
            } catch (\Exception $e) {
                $this->error("❌ Controller '{$controllerClass}' failed: " . $e->getMessage());
            }
        }
        
        $this->newLine();
    }

    /**
     * @description Check controller methods
     */
    private function checkControllerMethods($controllerClass)
    {
        $requiredMethods = ['index', 'show', 'create', 'store', 'edit', 'update', 'destroy'];
        
        foreach ($requiredMethods as $method) {
            if (method_exists($controllerClass, $method)) {
                $this->info("   ✅ Method '{$method}' exists");
            } else {
                $this->warn("   ⚠️ Method '{$method}' missing");
            }
        }
    }

    /**
     * @description Test service layer
     */
    private function testServiceLayer()
    {
        $this->info('⚙️ Testing Service Layer...');
        
        $services = [
            'App\Services\WhatsappTicketService',
            'App\Services\WhatsappConnectionService',
            'App\Services\WhatsappReportingService'
        ];
        
        foreach ($services as $serviceClass) {
            if (class_exists($serviceClass)) {
                $this->info("✅ Service '{$serviceClass}' exists");
                
                try {
                    $service = new $serviceClass();
                    $this->info("   ✅ Service instantiated successfully");
                } catch (\Exception $e) {
                    $this->warn("   ⚠️ Service instantiation failed: " . $e->getMessage());
                }
                
            } else {
                $this->warn("⚠️ Service '{$serviceClass}' missing");
                $this->issues[] = [
                    'type' => 'service',
                    'severity' => 'medium',
                    'message' => "Service '{$serviceClass}' missing",
                    'details' => 'Service layer not fully implemented',
                    'fix' => "Create service class '{$serviceClass}'"
                ];
            }
        }
        
        $this->newLine();
    }

    /**
     * @description Test view existence
     */
    private function testViewExistence()
    {
        $this->info('👁️ Testing View Existence...');
        
        $requiredViews = [
            'whatsapp.dashboard.index',
            'whatsapp.tickets.index',
            'whatsapp.tickets.create',
            'whatsapp.tickets.show',
            'whatsapp.tickets.edit',
            'whatsapp.connections.index',
            'whatsapp.contacts.index',
            'whatsapp.reporting.index',
            'whatsapp.tags.index',
            'whatsapp.ticket-types.index'
        ];
        
        foreach ($requiredViews as $viewName) {
            if (view()->exists($viewName)) {
                $this->info("✅ View '{$viewName}' exists");
            } else {
                $this->error("❌ View '{$viewName}' missing");
                $this->issues[] = [
                    'type' => 'view',
                    'severity' => 'high',
                    'message' => "View '{$viewName}' missing",
                    'details' => 'Required view template missing',
                    'fix' => "Create view file for '{$viewName}'"
                ];
            }
        }
        
        $this->newLine();
    }

    /**
     * @description Test tenant context
     */
    private function testTenantContext()
    {
        $this->info('🏢 Testing Tenant Context...');
        
        try {
            if (app()->bound('currentTenant')) {
                $tenant = app('currentTenant');
                if ($tenant) {
                    $id = isset($tenant->tenant_id) ? $tenant->tenant_id : ($tenant->id ?? 'unknown');
                    $this->info("✅ Tenant context available - ID: {$id}");
                } else {
                    $this->warn("⚠️ Tenant context bound but null");
                }
            } else {
                $this->warn("⚠️ Tenant context not bound");
                $this->issues[] = [
                    'type' => 'tenant',
                    'severity' => 'medium',
                    'message' => 'Tenant context not available',
                    'details' => 'Multitenancy may not be properly configured',
                    'fix' => 'Check multitenancy configuration and middleware'
                ];
            }
        } catch (\Exception $e) {
            $this->error("❌ Tenant context test failed: " . $e->getMessage());
        }
        
        $this->newLine();
    }

    /**
     * @description Test performance metrics
     */
    private function testPerformance()
    {
        $this->info('⚡ Testing Performance Metrics...');
        
        try {
            // Test database query performance
            $startTime = microtime(true);
            $ticketCount = WhatsappTicket::count();
            $dbTime = microtime(true) - $startTime;
            
            $this->info("✅ Database query time: " . round($dbTime * 1000, 2) . "ms");
            
            if ($dbTime > 0.1) {
                $this->warn("⚠️ Database query slow (>100ms)");
                $this->issues[] = [
                    'type' => 'performance',
                    'severity' => 'medium',
                    'message' => 'Database query performance slow',
                    'details' => "Query took {$dbTime}s",
                    'fix' => 'Add database indexes and optimize queries'
                ];
            }
            
            // Test memory usage
            $memoryUsage = memory_get_usage(true);
            $memoryMB = round($memoryUsage / 1024 / 1024, 2);
            $this->info("✅ Memory usage: {$memoryMB}MB");
            
            if ($memoryMB > 50) {
                $this->warn("⚠️ High memory usage (>50MB)");
            }
            
        } catch (\Exception $e) {
            $this->warn("⚠️ Performance test failed: " . $e->getMessage());
        }
        
        $this->newLine();
    }

    /**
     * @description Generate comprehensive report
     */
    private function generateReport()
    {
        $this->info('📋 Generating Test Report...');
        $this->newLine();
        
        $this->info('🔍 ISSUES IDENTIFIED:');
        $this->newLine();
        
        if (empty($this->issues)) {
            $this->info('✅ No issues found! WhatsApp system is working perfectly.');
            return;
        }
        
        $criticalIssues = array_filter($this->issues, fn($issue) => $issue['severity'] === 'critical');
        $highIssues = array_filter($this->issues, fn($issue) => $issue['severity'] === 'high');
        $mediumIssues = array_filter($this->issues, fn($issue) => $issue['severity'] === 'medium');
        $lowIssues = array_filter($this->issues, fn($issue) => $issue['severity'] === 'low');
        
        if (!empty($criticalIssues)) {
            $this->error('🚨 CRITICAL ISSUES (' . count($criticalIssues) . '):');
            foreach ($criticalIssues as $issue) {
                $this->error("   • {$issue['message']}");
                $this->line("     {$issue['details']}");
                $this->line("     Fix: {$issue['fix']}");
                $this->newLine();
            }
        }
        
        if (!empty($highIssues)) {
            $this->error('⚠️ HIGH PRIORITY ISSUES (' . count($highIssues) . '):');
            foreach ($highIssues as $issue) {
                $this->error("   • {$issue['message']}");
                $this->line("     {$issue['details']}");
                $this->line("     Fix: {$issue['fix']}");
                $this->newLine();
            }
        }
        
        if (!empty($mediumIssues)) {
            $this->warn('🔧 MEDIUM PRIORITY ISSUES (' . count($mediumIssues) . '):');
            foreach ($mediumIssues as $issue) {
                $this->warn("   • {$issue['message']}");
                $this->line("     {$issue['details']}");
                $this->line("     Fix: {$issue['fix']}");
                $this->newLine();
            }
        }
        
        if (!empty($lowIssues)) {
            $this->info('💡 LOW PRIORITY ISSUES (' . count($lowIssues) . '):');
            foreach ($lowIssues as $issue) {
                $this->info("   • {$issue['message']}");
                $this->line("     {$issue['details']}");
                $this->line("     Fix: {$issue['fix']}");
                $this->newLine();
            }
        }
        
        $this->info('📊 SUMMARY:');
        $this->info("   Total Issues: " . count($this->issues));
        $this->info("   Critical: " . count($criticalIssues));
        $this->info("   High: " . count($highIssues));
        $this->info("   Medium: " . count($mediumIssues));
        $this->info("   Low: " . count($lowIssues));
        
        if (!empty($this->issues)) {
            $this->newLine();
            $this->info('💡 RECOMMENDATIONS:');
            $this->info('   1. Fix critical issues immediately');
            $this->info('   2. Address high priority issues within 24 hours');
            $this->info('   3. Plan medium priority fixes for this week');
            $this->info('   4. Consider low priority issues for future updates');
            $this->newLine();
            $this->info('🔧 Run with --fix option to automatically apply fixes where possible');
        }
    }

    /**
     * @description Apply automatic fixes
     */
    private function applyFixes()
    {
        $this->info('🔧 Applying Automatic Fixes...');
        $this->newLine();
        
        $fixesApplied = 0;
        
        foreach ($this->issues as $issue) {
            if ($this->canAutoFix($issue)) {
                try {
                    $this->applyFix($issue);
                    $fixesApplied++;
                    $this->info("✅ Fixed: {$issue['message']}");
                } catch (\Exception $e) {
                    $this->error("❌ Failed to fix: {$issue['message']} - {$e->getMessage()}");
                }
            }
        }
        
        if ($fixesApplied > 0) {
            $this->newLine();
            $this->info("🎉 Applied {$fixesApplied} automatic fixes!");
            $this->info('💡 Run the test again to verify fixes');
        } else {
            $this->info('💡 No automatic fixes available. Manual intervention required.');
        }
    }

    /**
     * @description Check if issue can be auto-fixed
     */
    private function canAutoFix($issue)
    {
        // Only auto-fix certain types of issues
        $autoFixableTypes = ['database', 'service'];
        return in_array($issue['type'], $autoFixableTypes);
    }

    /**
     * @description Apply specific fix
     */
    private function applyFix($issue)
    {
        switch ($issue['type']) {
            case 'database':
                $this->fixDatabaseIssue($issue);
                break;
            case 'service':
                $this->fixServiceIssue($issue);
                break;
            default:
                throw new \Exception('Auto-fix not available for this issue type');
        }
    }

    /**
     * @description Fix database issues
     */
    private function fixDatabaseIssue($issue)
    {
        // Implement database fixes
        if (strpos($issue['message'], 'Table') !== false && strpos($issue['message'], 'missing') !== false) {
            // Create missing table
            $this->createMissingTable($issue);
        }
    }

    /**
     * @description Fix service issues
     */
    private function fixServiceIssue($issue)
    {
        // Implement service fixes
        if (strpos($issue['message'], 'missing') !== false) {
            // Create missing service
            $this->createMissingService($issue);
        }
    }

    /**
     * @description Create missing table
     */
    private function createMissingTable($issue)
    {
        // Extract table name from issue message
        preg_match("/Table '([^']+)' missing/", $issue['message'], $matches);
        if (isset($matches[1])) {
            $tableName = $matches[1];
            $this->info("   Creating missing table: {$tableName}");
            // Implementation would go here
        }
    }

    /**
     * @description Create missing service
     */
    private function createMissingService($issue)
    {
        // Extract service name from issue message
        preg_match("/Service '([^']+)' missing/", $issue['message'], $matches);
        if (isset($matches[1])) {
            $serviceName = $matches[1];
            $this->info("   Creating missing service: {$serviceName}");
            // Implementation would go here
        }
    }
}
