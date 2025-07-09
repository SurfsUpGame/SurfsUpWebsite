<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Task;
use App\Enums\TaskStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all distinct combinations of status and sprint_id
        $statusGroups = Task::select('status', 'sprint_id')
            ->groupBy('status', 'sprint_id')
            ->get();

        foreach ($statusGroups as $group) {
            // Get tasks for this status/sprint combination ordered by vote score
            $tasks = Task::where('status', $group->status)
                ->where(function($query) use ($group) {
                    if ($group->sprint_id) {
                        $query->where('sprint_id', $group->sprint_id);
                    } else {
                        $query->whereNull('sprint_id');
                    }
                })
                ->withCount(['votes as vote_score' => function($query) {
                    $query->select(DB::raw('COALESCE(SUM(vote), 0)'));
                }])
                ->orderByDesc('vote_score')
                ->orderBy('created_at')
                ->get();

            // Update order field based on vote score ranking
            foreach ($tasks as $index => $task) {
                $task->update(['order' => $index]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset all order fields to 0
        Task::query()->update(['order' => 0]);
    }
};