<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewTaskFieldsToTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('task_short_title', 50)->nullable()->after('task_title');
            $table->date('task_start_date')->nullable()->after('task_date_start');
            $table->time('task_start_time')->nullable()->after('task_start_date');
            $table->time('task_end_time')->nullable()->after('task_start_time');
            $table->string('task_estimated_time', 20)->nullable()->after('task_end_time');
            $table->text('task_location')->nullable()->after('task_estimated_time');
            $table->string('task_color', 7)->nullable()->after('task_location'); // Hex color code
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn([
                'task_short_title',
                'task_start_date', 
                'task_start_time',
                'task_end_time',
                'task_estimated_time',
                'task_location',
                'task_color'
            ]);
        });
    }
} 