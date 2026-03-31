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
        Schema::create('settings', function (Blueprint $table) {
            $table->id('settings_id');
            
            // System settings
            $table->string('settings_system_timezone', 100)->default('UTC');
            $table->string('settings_system_date_format', 50)->default('Y-m-d');
            $table->string('settings_system_datepicker_format', 50)->default('yyyy-mm-dd');
            $table->string('settings_system_currency_symbol', 10)->default('$');
            $table->string('settings_system_currency_position', 10)->default('left');
            $table->string('settings_system_javascript_versioning', 20)->default('1');
            $table->string('settings_theme_name', 100)->default('default');
            
            // Company settings
            $table->string('settings_company_name', 255)->default('Your Company');
            $table->string('settings_company_city', 100)->nullable();
            $table->string('settings_company_address_line_1', 255)->nullable();
            $table->string('settings_company_address_line_2', 255)->nullable();
            $table->string('settings_company_state', 100)->nullable();
            $table->string('settings_company_zipcode', 20)->nullable();
            $table->string('settings_company_country', 100)->nullable();
            $table->string('settings_company_phone', 50)->nullable();
            $table->string('settings_company_email', 255)->nullable();
            $table->string('settings_company_website', 255)->nullable();
            
            // Email settings
            $table->string('settings_email_server_type', 50)->default('smtp');
            $table->string('settings_email_smtp_host', 255)->nullable();
            $table->string('settings_email_smtp_port', 10)->nullable();
            $table->string('settings_email_smtp_encryption', 20)->default('none');
            $table->string('settings_email_smtp_username', 255)->nullable();
            $table->string('settings_email_smtp_password', 255)->nullable();
            $table->string('settings_email_from_address', 255)->nullable();
            $table->string('settings_email_from_name', 255)->nullable();
            
            // SaaS settings
            $table->string('settings_saas_email_server_type', 50)->default('local');
            $table->string('settings_saas_email_local_address', 255)->nullable();
            
            // Purchase code
            $table->string('settings_purchase_code', 255)->nullable();
            
            // Task settings
            $table->string('settings_tasks_kanban_reminder', 20)->default('disabled');
            $table->string('settings_tasks_short_title', 20)->default('disabled');
            $table->string('settings_tasks_start_end_time', 20)->default('disabled');
            $table->string('settings_tasks_estimated_time', 20)->default('disabled');
            $table->string('settings_tasks_location', 20)->default('disabled');
            $table->string('settings_tasks_color', 20)->default('disabled');
            
            $table->timestamp('settings_created')->nullable();
            $table->timestamp('settings_updated')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
