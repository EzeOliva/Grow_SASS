<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsappAutomationService;

/**
 * @fileoverview Process WhatsApp Automations Command
 * @description Processes automated WhatsApp ticket operations including inactivity checks and auto-close scheduling
 */
class ProcessWhatsappAutomations extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'whatsapp:process-automations';

    /**
     * The console command description.
     */
    protected $description = 'Process WhatsApp ticket automations including inactivity checks and auto-close scheduling';

    /**
     * The automation service instance.
     */
    protected $automationService;

    /**
     * Create a new command instance.
     */
    public function __construct(WhatsappAutomationService $automationService)
    {
        parent::__construct();
        $this->automationService = $automationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting WhatsApp automation processing...');

        try {
            // Check for inactive tickets
            $this->info('Checking for inactive tickets...');
            $this->automationService->checkInactiveTickets();
            $this->info('Inactive ticket check completed.');

            // Process auto-close scheduled tickets
            $this->info('Processing auto-close scheduled tickets...');
            $this->automationService->processAutoCloseScheduledTickets();
            $this->info('Auto-close processing completed.');

            $this->info('WhatsApp automation processing completed successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error processing WhatsApp automations: ' . $e->getMessage());
            return 1;
        }
    }
}

