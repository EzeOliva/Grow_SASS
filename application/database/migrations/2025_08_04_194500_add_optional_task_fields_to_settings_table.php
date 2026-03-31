<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOptionalTaskFieldsToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('settings_tasks_short_title', 20)->default('disabled')->after('settings_tasks_kanban_reminder');
            $table->string('settings_tasks_start_end_time', 20)->default('disabled')->after('settings_tasks_short_title');
            $table->string('settings_tasks_estimated_time', 20)->default('disabled')->after('settings_tasks_start_end_time');
            $table->string('settings_tasks_location', 20)->default('disabled')->after('settings_tasks_estimated_time');
            $table->string('settings_tasks_color', 20)->default('disabled')->after('settings_tasks_location');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'settings_tasks_short_title',
                'settings_tasks_start_end_time',
                'settings_tasks_estimated_time',
                'settings_tasks_location',
                'settings_tasks_color'
            ]);
        });
    }
} 