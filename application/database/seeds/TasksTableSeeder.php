<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TasksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        
        // Get existing data for relationships
        $projectIds = DB::table('projects')->pluck('project_id')->toArray();
        $userIds = DB::table('users')->pluck('id')->toArray();
        $milestoneIds = DB::table('milestones')->pluck('milestone_id')->toArray();
        $statusIds = DB::table('tasks_status')->pluck('taskstatus_id')->toArray();
        $priorityIds = DB::table('tasks_priority')->pluck('taskpriority_id')->toArray();

        if (empty($projectIds) || empty($userIds)) {
            return;
        }

        for ($i = 0; $i < 20; $i++) {
            $projectId = $faker->randomElement($projectIds);
            $milestoneId = $faker->randomElement($milestoneIds);
            
            // Get client ID from project
            $clientId = DB::table('projects')->where('project_id', $projectId)->value('project_clientid');
            
            // Get milestone for this project
            $projectMilestone = DB::table('milestones')
                ->where('milestone_projectid', $projectId)
                ->inRandomOrder()
                ->first();
            
            $milestoneId = $projectMilestone ? $projectMilestone->milestone_id : $milestoneIds[0];

            DB::table('tasks')->insert([
                'task_creatorid' => $faker->randomElement($userIds),
                'task_uniqueid' => 'TASK-' . strtoupper($faker->bothify('??????')),
                'task_projectid' => $projectId,
                'task_milestoneid' => $milestoneId,
                'task_clientid' => $clientId,
                'task_title' => $faker->sentence(4),
                'task_description' => $faker->paragraph(2),
                'task_status' => $faker->randomElement($statusIds),
                'task_priority' => $faker->randomElement($priorityIds),
                'task_date_start' => $faker->dateTimeBetween('-30 days', 'now'),
                'task_date_due' => $faker->dateTimeBetween('now', '+30 days'),
                'task_client_visibility' => $faker->randomElement(['yes', 'no']),
                'task_billable' => $faker->randomElement(['yes', 'no']),
                'task_position' => ($i + 1) * 1000,
                'task_calendar_timezone' => 'UTC',
                'task_created' => $faker->dateTimeBetween('-60 days', 'now'),
                'task_updated' => $faker->dateTimeBetween('-30 days', 'now'),
                
                // New task fields with more realistic data
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
    }
} 