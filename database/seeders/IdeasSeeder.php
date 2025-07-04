<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;
use App\Enums\TaskStatus;

class IdeasSeeder extends Seeder
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
            $this->command->error('No users found to assign ideas to.');
            return;
        }

        $ideas = [
            [
                'title' => 'AI-Powered Task Prioritization',
                'description' => 'Use machine learning to automatically prioritize tasks based on deadlines, dependencies, and team capacity'
            ],
            [
                'title' => 'Dark Mode & Custom Themes',
                'description' => 'Allow users to switch between light/dark modes and create custom color themes for personalization'
            ],
            [
                'title' => 'Time Tracking Integration',
                'description' => 'Built-in time tracking with reports, estimates vs actual time, and productivity analytics'
            ],
            [
                'title' => 'Voice-to-Task Creation',
                'description' => 'Add tasks using voice commands or voice notes that get transcribed automatically'
            ],
            [
                'title' => 'Smart Deadline Suggestions',
                'description' => 'AI suggests realistic deadlines based on task complexity and team workload'
            ],
            [
                'title' => 'Calendar Integration',
                'description' => 'Two-way sync with Google Calendar, Outlook, and other calendar applications'
            ],
            [
                'title' => 'Automated Status Updates',
                'description' => 'Tasks automatically move between statuses based on certain triggers and conditions'
            ],
            [
                'title' => 'Team Workload Balancing',
                'description' => 'Visualize team capacity and automatically suggest task reassignments for better balance'
            ],
            [
                'title' => 'Dependency Mapping',
                'description' => 'Visual dependency graphs showing task relationships and critical path analysis'
            ],
            [
                'title' => 'Gamification Elements',
                'description' => 'Points, badges, and achievement system to motivate team members and track progress'
            ]
        ];

        foreach ($ideas as $idea) {
            Task::create([
                'title' => $idea['title'],
                'description' => $idea['description'],
                'status' => TaskStatus::Ideas,
                'user_id' => $admin->id,
                'created_by' => $admin->id,
            ]);
        }

        $this->command->info('Created ' . count($ideas) . ' idea tasks assigned to ' . $admin->name);
    }
}