<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class TasksTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Get existing data from other seeders - filter out space projects
        $existingProjects = DB::table('projects')
            ->where('project_type', '!=', 'space')
            ->whereNotNull('project_clientid')
            ->get();
        $existingUsers = DB::table('users')->get();
        $existingClients = DB::table('clients')->get();
        $existingMilestones = DB::table('milestones')->get();

        // Debug: Check what we're getting
        echo "Found " . $existingProjects->count() . " real projects (excluding spaces)\n";
        if ($existingProjects->count() > 0) {
            $firstProject = $existingProjects->first();
            echo "First real project: " . json_encode($firstProject) . "\n";
        }

        // Use existing data if available, otherwise create test data
        if ($existingProjects->count() > 0) {
            $firstProject = $existingProjects->first();
            $projectId = $firstProject->project_id;
            $clientId = $firstProject->project_clientid;
            
            // Debug: Check the values
            echo "Using existing project ID: " . $projectId . "\n";
            echo "Using existing client ID: " . $clientId . "\n";
        } else {
            echo "No real projects found, creating test data\n";
            
            // Create a test client if none exists
            $clientId = DB::table('clients')->insertGetId([
                'client_company_name' => 'Test Company',
                'client_status' => 'active',
                'client_created' => now(),
                'client_updated' => now(),
            ]);

            // Create a test project if none exists
            $projectId = DB::table('projects')->insertGetId([
                'project_uniqueid' => $faker->uuid,
                'project_title' => 'Test Project',
                'project_description' => 'A test project for seeding tasks',
                'project_status' => 1,
                'project_clientid' => $clientId,
                'project_creatorid' => 1, // Use first user
                'project_date_start' => now()->format('Y-m-d'),
                'project_date_due' => now()->addDays(30)->format('Y-m-d'),
                'project_progress' => 0,
                'project_active_state' => 'active',
                'project_created' => now(),
                'project_updated' => now(),
            ]);
            
            echo "Created new project ID: " . $projectId . "\n";
        }

        // Get or create user
        if ($existingUsers->count() > 0) {
            $userId = $existingUsers->first()->id;
        } else {
            $userId = DB::table('users')->insertGetId([
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'type' => 'team',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Get or create milestone
        if ($existingMilestones->count() > 0) {
            $milestoneId = $existingMilestones->first()->milestone_id;
        } else {
            $milestoneId = DB::table('milestones')->insertGetId([
                'milestone_title' => 'Test Milestone',
                'milestone_projectid' => $projectId,
                'milestone_created' => now(),
                'milestone_updated' => now(),
            ]);
        }

        // Get or create task statuses
        $statusIds = DB::table('tasks_status')->pluck('taskstatus_id')->toArray();
        if (empty($statusIds)) {
            $statusIds = [
                DB::table('tasks_status')->insertGetId(['taskstatus_title' => 'New', 'taskstatus_position' => 1]),
                DB::table('tasks_status')->insertGetId(['taskstatus_title' => 'In Progress', 'taskstatus_position' => 2]),
                DB::table('tasks_status')->insertGetId(['taskstatus_title' => 'Completed', 'taskstatus_position' => 3]),
            ];
        }

        // Get or create task priorities
        $priorityIds = DB::table('tasks_priority')->pluck('taskpriority_id')->toArray();
        if (empty($priorityIds)) {
            $priorityIds = [
                DB::table('tasks_priority')->insertGetId(['taskpriority_title' => 'Low', 'taskpriority_position' => 1]),
                DB::table('tasks_priority')->insertGetId(['taskpriority_title' => 'Medium', 'taskpriority_position' => 2]),
                DB::table('tasks_priority')->insertGetId(['taskpriority_title' => 'High', 'taskpriority_position' => 3]),
            ];
        }

        echo "About to create tasks with project ID: " . $projectId . "\n";

        // Now create the tasks
        for ($i = 0; $i < 20; $i++) {
            $startDate = $faker->dateTimeBetween('-1 month', '+1 month');
            $dueDate = (clone $startDate)->modify('+'.rand(1,30).' days');

            DB::table('tasks')->insert([
                'task_uniqueid' => $faker->uuid,
                'task_created' => $startDate,
                'task_updated' => $dueDate,
                'task_creatorid' => $userId,
                'task_projectid' => $projectId,
                'task_milestoneid' => $milestoneId,
                'task_clientid' => $clientId,
                'task_title' => $faker->sentence(4),
                'task_description' => $faker->paragraph,
                'task_date_start' => $startDate->format('Y-m-d'),
                'task_date_due' => $dueDate->format('Y-m-d'),
                'task_status' => $faker->randomElement($statusIds),
                'task_priority' => $faker->randomElement($priorityIds),
                'task_client_visibility' => $faker->randomElement(['yes', 'no']),
                'task_billable' => $faker->randomElement(['yes', 'no']),
                'task_active_state' => 'active',
                'task_position' => ($i + 1) * 1000,
                'task_calendar_timezone' => 'UTC',
                
                // New task fields with comprehensive test data
                'task_short_title' => $faker->randomElement(['TM', 'TN', 'T1', 'T2', 'T3', 'T4', 'T5', 'DEV', 'QA', 'DOC', 'MTG', 'REV', 'TEST', 'DEPLOY']),
                'task_start_date' => $faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d'),
                'task_start_time' => $faker->randomElement(['09:00:00', '10:00:00', '11:00:00', '13:00:00', '14:00:00', '15:00:00']),
                'task_end_time' => $faker->randomElement(['17:00:00', '18:00:00', '19:00:00', '20:00:00', '21:00:00']),
                'task_estimated_time' => $faker->randomElement(['0.5h', '1h', '1.5h', '2h', '3h', '4h', '6h', '8h', '1d', '2d', '3d', '1w']),
                'task_location' => $faker->randomElement([
                    'Office - Conference Room A',
                    'Office - Meeting Room B', 
                    'Client Office - Downtown',
                    'Remote - Zoom Meeting',
                    'Coffee Shop - Main Street',
                    'Co-working Space - Tech Hub',
                    'Home Office',
                    'Client Site - Project Location',
                    'Training Center - Learning Lab',
                    'Conference Center - Event Hall'
                ]),
                'task_color' => $faker->randomElement([
                    '#007bff', // Blue
                    '#28a745', // Green
                    '#ffc107', // Yellow
                    '#dc3545', // Red
                    '#6f42c1', // Purple
                    '#fd7e14', // Orange
                    '#20c997', // Teal
                    '#6c757d', // Gray
                    '#e83e8c', // Pink
                    '#17a2b8'  // Cyan
                ]),
            ]);
        }
        
        echo "Created 20 tasks successfully\n";
    }
} 