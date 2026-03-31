<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/**
 * @fileoverview Installs WhatsApp module DB schema and seeds defaults
 * @description Runs targeted migrations for WhatsApp and seeds default types/tags so pages work out-of-the-box.
 */
class InstallWhatsappModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Options
     * --database=   Database connection/database name to use
     */
    protected $signature = 'whatsapp:install {--database= : Database name (or connection) to use} {--force : Force operations in production}';

    /**
     * The console command description.
     */
    protected $description = 'Install WhatsApp module: run migrations and seed default data';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dbNameOrConnection = $this->option('database');

        // Configure connection for CLI/multitenancy
        try {
            $connections = config('database.connections');
            if ($dbNameOrConnection) {
                if (isset($connections[$dbNameOrConnection])) {
                    // It is a configured connection name
                    config(['database.default' => $dbNameOrConnection]);
                    DB::purge($dbNameOrConnection);
                    DB::reconnect($dbNameOrConnection);
                    $this->info("Using database connection: {$dbNameOrConnection}");
                    // Store which connection to use for migrate
                    $resolvedConnection = $dbNameOrConnection;
                } else {
                    // Treat as database name for the default 'tenant' connection
                    $conn = 'tenant';
                    config(["database.connections.{$conn}.database" => $dbNameOrConnection]);
                    config(['database.default' => $conn]);
                    DB::purge($conn);
                    DB::reconnect($conn);
                    $this->info("Configured tenant connection to use database: {$dbNameOrConnection}");
                    // Use tenant connection name for migrate
                    $resolvedConnection = $conn;
                }
            } else {
                $current = DB::getDatabaseName();
                if (!$current) {
                    $this->error('No database selected. Pass --database=DB_NAME or configure .env');
                    return 1;
                }
                $this->info("Using database: {$current}");
                $resolvedConnection = config('database.default');
            }
        } catch (\Throwable $e) {
            $this->error('Database connection error: '.$e->getMessage());
            return 1;
        }

        // Run WhatsApp-related migrations (whole set, safe and idempotent)
        $this->info('Running migrations...');
        $migrateArgs = ['--force' => (bool) $this->option('force')];
        if (!empty($resolvedConnection)) {
            $migrateArgs['--database'] = $resolvedConnection;
        }
        $code = Artisan::call('migrate', $migrateArgs);
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            $this->error('Migrations failed');
            return $code;
        }

        // Seed defaults (ticket types, etc.)
        $this->info('Seeding WhatsApp defaults...');
        $seedArgs = ['--class' => 'Database\\Seeders\\WhatsappModuleSeeder', '--force' => true];
        if (!empty($resolvedConnection)) {
            $seedArgs['--database'] = $resolvedConnection;
        }
        $code = Artisan::call('db:seed', $seedArgs);
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            $this->error('Seeding failed');
            return $code;
        }

        $this->info('WhatsApp module installed successfully.');
        return 0;
    }
}


