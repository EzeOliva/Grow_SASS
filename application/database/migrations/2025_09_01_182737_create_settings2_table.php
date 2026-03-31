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
        Schema::create('settings2', function (Blueprint $table) {
            $table->id('settings2_id');
            
            // Captcha settings
            $table->string('settings2_captcha_status', 20)->default('disabled');
            $table->string('settings2_captcha_api_site_key', 255)->nullable();
            $table->string('settings2_captcha_api_secret_key', 255)->nullable();
            
            // Theme CSS
            $table->text('settings2_theme_css')->nullable();
            
            // Calendar settings
            $table->string('settings2_calendar_events_colour', 7)->default('#007bff');
            $table->string('settings2_calendar_projects_colour', 7)->default('#28a745');
            $table->string('settings2_calendar_tasks_colour', 7)->default('#ffc107');
            
            // Spaces settings
            $table->string('settings2_spaces_user_space_status', 20)->default('disabled');
            $table->string('settings2_spaces_user_space_menu_name', 100)->default('My Space');
            $table->string('settings2_spaces_team_space_status', 20)->default('disabled');
            $table->string('settings2_spaces_team_space_menu_name', 100)->default('Team Space');
            $table->string('settings2_spaces_team_space_id', 100)->nullable();
            
            // Extras settings
            $table->string('settings2_extras_dimensions_default_unit', 20)->default('cm');
            
            // Tasks settings
            $table->string('settings2_tasks_manage_dependencies', 50)->default('admin-users');
            
            // Projects automation settings
            $table->string('settings2_projects_automation_default_status', 20)->default('disabled');
            $table->string('settings2_projects_automation_create_invoices', 20)->default('disabled');
            $table->string('settings2_projects_automation_convert_estimates_to_invoices', 20)->default('disabled');
            $table->string('settings2_projects_automation_invoice_unbilled_hours', 20)->default('disabled');
            $table->string('settings2_projects_automation_invoice_hourly_rate', 20)->default('0');
            $table->string('settings2_projects_automation_invoice_hourly_tax_1', 20)->default('0');
            $table->string('settings2_projects_automation_invoice_email_client', 20)->default('disabled');
            $table->string('settings2_projects_automation_invoice_due_date', 20)->default('30');
            
            // Estimates automation settings
            $table->string('settings2_estimates_automation_create_project', 20)->default('disabled');
            $table->string('settings2_estimates_automation_project_title', 255)->nullable();
            $table->string('settings2_estimates_automation_create_invoice', 20)->default('disabled');
            $table->string('settings2_estimates_automation_invoice_due_date', 20)->default('30');
            
            // Reports settings
            $table->string('settings2_tweak_reports_truncate_long_text', 20)->default('no');
            $table->string('settings2_search_category_limit', 20)->default('10');
            
            $table->timestamp('settings2_created')->nullable();
            $table->timestamp('settings2_updated')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings2');
    }
};
