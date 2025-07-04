<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;
use App\Enums\TaskStatus;

class BacklogTasksSeeder extends Seeder
{
    public function run(): void
    {
        // Find an admin or staff user
        $admin = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'staff']);
        })->first();

        if (!$admin) {
            $admin = User::first();
        }

        if (!$admin) {
            $this->command->error('No users found to assign backlog tasks to.');
            return;
        }

        $backlogs = [
            [
                'title' => 'User Profile Enhancement',
                'description' => 'Add ability to customize user profiles with avatars and bio sections'
            ],
            [
                'title' => 'Email Notifications System',
                'description' => 'Implement email notifications for task assignments, updates, and deadlines'
            ],
            [
                'title' => 'API Documentation',
                'description' => 'Create comprehensive API documentation for external integrations'
            ],
            [
                'title' => 'Mobile App Development',
                'description' => 'Develop mobile application for iOS and Android platforms'
            ],
            [
                'title' => 'Advanced Search & Filters',
                'description' => 'Add advanced filtering and search options for tasks and projects'
            ],
            [
                'title' => 'Task Templates',
                'description' => 'Create reusable task templates for common project workflows'
            ],
            [
                'title' => 'Team Collaboration Features',
                'description' => 'Add team chat, file sharing, and collaborative editing features'
            ],
            [
                'title' => 'Performance Optimization',
                'description' => 'Optimize database queries and implement caching for better performance'
            ]
        ];

        foreach ($backlogs as $backlog) {
            Task::create([
                'title' => $backlog['title'],
                'description' => $backlog['description'],
                'status' => TaskStatus::Backlog,
                'user_id' => $admin->id,
                'created_by' => $admin->id,
            ]);
        }

        $this->command->info('Created ' . count($backlogs) . ' backlog tasks assigned to ' . $admin->name);
    }
}