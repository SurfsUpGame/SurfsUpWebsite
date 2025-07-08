<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Sprint;
use App\Models\Epic;
use App\Models\Label;
use App\Models\TaskVote;
use App\Models\Suggestion;
use App\Models\SuggestionVote;
use App\Enums\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class RoadmapController extends Controller
{
    public function index(Request $request)
    {
        $showPast = $request->get('show_past', false);
        
        if ($showPast) {
            // Show past sprints with archived tasks
            $tasks = Task::with(['user', 'creator', 'sprint', 'epic', 'labels', 'votes'])->whereNotNull('archived_at')->get();
            $sprints = Sprint::where('is_active', false)->orderBy('start_date', 'desc')->get();
        } else {
            // Show current active sprints with non-archived tasks
            $tasks = Task::with(['user', 'creator', 'sprint', 'epic', 'labels', 'votes'])->whereNull('archived_at')->get();
            $sprints = Sprint::where('is_active', true)->orderBy('start_date', 'desc')->get();
        }
        
        $statuses = TaskStatus::cases();
        $epics = Epic::where('is_active', true)->get();
        $labels = Label::all();
        
        // Get users with admin or staff roles for task assignment
        $eligibleUsers = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'staff']);
        })->get();
        
        // Group tasks by status
        $tasksByStatus = collect($statuses)->mapWithKeys(function ($status) use ($tasks) {
            return [
                $status->value => $tasks->where('status', $status)->values()
            ];
        });

        // Get suggestions ordered by score
        $suggestions = Suggestion::with(['user', 'votes'])
            ->where('converted_to_task', false)
            ->orderBy('score', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('roadmap', [
            'statuses' => $statuses,
            'tasksByStatus' => $tasksByStatus,
            'tasks' => $tasks,
            'eligibleUsers' => $eligibleUsers,
            'sprints' => $sprints,
            'epics' => $epics,
            'labels' => $labels,
            'showPast' => $showPast,
            'suggestions' => $suggestions,
        ]);
    }
    
    public function store(Request $request)
    {
        // Check if user has admin or staff role
        if (!auth()->user()->hasRole(['admin', 'staff'])) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:' . implode(',', array_column(TaskStatus::cases(), 'value')),
            'user_id' => 'required|exists:users,id',
            'due_date' => 'nullable|date',
            'sprint_id' => 'nullable|exists:sprints,id',
            'epic_id' => 'nullable|exists:epics,id',
            'priority' => 'nullable|in:low,medium,high,critical',
            'labels' => 'nullable|array',
            'labels.*' => 'exists:labels,id',
        ]);
        
        // Verify the assigned user has admin or staff role
        $assignedUser = User::find($validated['user_id']);
        if (!$assignedUser->hasRole(['admin', 'staff'])) {
            return back()->withErrors(['user_id' => 'Selected user must have admin or staff role.']);
        }
        
        $validated['created_by'] = auth()->id();
        $labels = $validated['labels'] ?? [];
        unset($validated['labels']);
        
        $task = Task::create($validated);
        
        if (!empty($labels)) {
            $task->labels()->sync($labels);
        }
        
        return redirect()->route('roadmap')->with('success', 'Task created successfully!');
    }
    
    public function update(Request $request, Task $task)
    {
        // Check if user has admin or staff role
        if (!auth()->user()->hasRole(['admin', 'staff'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:' . implode(',', array_column(TaskStatus::cases(), 'value')),
            'user_id' => 'required|exists:users,id',
            'due_date' => 'nullable|date',
            'sprint_id' => 'nullable|exists:sprints,id',
            'epic_id' => 'nullable|exists:epics,id',
            'priority' => 'nullable|in:low,medium,high,critical',
            'labels' => 'nullable|array',
            'labels.*' => 'exists:labels,id',
        ]);
        
        // Verify the assigned user has admin or staff role
        $assignedUser = User::find($validated['user_id']);
        if (!$assignedUser->hasRole(['admin', 'staff'])) {
            return response()->json(['error' => 'Selected user must have admin or staff role.'], 422);
        }
        
        $labels = $validated['labels'] ?? [];
        unset($validated['labels']);
        
        $task->update($validated);
        
        $task->labels()->sync($labels);
        
        return response()->json(['success' => true, 'message' => 'Task updated successfully']);
    }
    
    public function updateStatus(Request $request, Task $task)
    {
        // Check if user has admin or staff role
        if (!auth()->user()->hasRole(['admin', 'staff'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'status' => 'required|string|in:' . implode(',', array_column(TaskStatus::cases(), 'value')),
            'sprint_id' => 'nullable|exists:sprints,id',
        ]);
        
        $updateData = ['status' => $validated['status']];
        
        // If sprint_id is provided, update it; if null, remove sprint assignment
        if (array_key_exists('sprint_id', $validated)) {
            $updateData['sprint_id'] = $validated['sprint_id'];
        }
        
        $task->update($updateData);
        
        return response()->json(['success' => true, 'message' => 'Task updated successfully']);
    }
    
    public function archive(Task $task)
    {
        // Check if user has admin or staff role
        if (!auth()->user()->hasRole(['admin', 'staff'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $task->update(['archived_at' => now()]);
        
        return response()->json(['success' => true, 'message' => 'Task archived successfully']);
    }
    
    public function destroy(Task $task)
    {
        // Check if user has admin or staff role
        if (!auth()->user()->hasRole(['admin', 'staff'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $task->delete();
        
        return response()->json(['success' => true, 'message' => 'Task deleted successfully']);
    }

    public function vote(Request $request, Task $task)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'vote' => 'required|integer|in:-1,1',
        ]);

        $userId = auth()->id();
        $existingVote = TaskVote::where('task_id', $task->id)
                              ->where('user_id', $userId)
                              ->first();

        if ($existingVote) {
            if ($existingVote->vote == $validated['vote']) {
                // Same vote - remove it (toggle off)
                $existingVote->delete();
                $action = 'removed';
            } else {
                // Different vote - update it
                $existingVote->update(['vote' => $validated['vote']]);
                $action = 'updated';
            }
        } else {
            // No existing vote - create new one
            TaskVote::create([
                'task_id' => $task->id,
                'user_id' => $userId,
                'vote' => $validated['vote'],
            ]);
            $action = 'added';
        }

        // Refresh task to get updated vote counts
        $task->load('votes');

        return response()->json([
            'success' => true,
            'action' => $action,
            'vote_score' => $task->vote_score,
            'upvote_count' => $task->upvote_count,
            'downvote_count' => $task->downvote_count,
            'user_vote' => $task->user_vote,
            'message' => 'Vote ' . $action . ' successfully'
        ]);
    }

    public function storeSuggestion(Request $request)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:280',
        ]);

        $suggestion = Suggestion::create([
            'content' => $validated['content'],
            'user_id' => auth()->id(),
        ]);

        $suggestion->load('user');

        return response()->json([
            'success' => true,
            'suggestion' => $suggestion,
            'message' => 'Suggestion submitted successfully!'
        ]);
    }

    public function voteSuggestion(Request $request, Suggestion $suggestion)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'vote' => 'required|integer|in:-1,1',
        ]);

        $userId = auth()->id();
        $existingVote = SuggestionVote::where('suggestion_id', $suggestion->id)
                              ->where('user_id', $userId)
                              ->first();

        if ($existingVote) {
            if ($existingVote->vote == $validated['vote']) {
                // Same vote - remove it (toggle off)
                $existingVote->delete();
                
                // Update vote counts
                if ($validated['vote'] == 1) {
                    $suggestion->decrement('upvotes');
                } else {
                    $suggestion->decrement('downvotes');
                }
                
                $action = 'removed';
            } else {
                // Different vote - update it
                $oldVote = $existingVote->vote;
                $existingVote->update(['vote' => $validated['vote']]);
                
                // Update vote counts
                if ($oldVote == 1) {
                    $suggestion->decrement('upvotes');
                } else {
                    $suggestion->decrement('downvotes');
                }
                
                if ($validated['vote'] == 1) {
                    $suggestion->increment('upvotes');
                } else {
                    $suggestion->increment('downvotes');
                }
                
                $action = 'updated';
            }
        } else {
            // No existing vote - create new one
            SuggestionVote::create([
                'suggestion_id' => $suggestion->id,
                'user_id' => $userId,
                'vote' => $validated['vote'],
            ]);
            
            // Update vote counts
            if ($validated['vote'] == 1) {
                $suggestion->increment('upvotes');
            } else {
                $suggestion->increment('downvotes');
            }
            
            $action = 'added';
        }

        // Update score
        $suggestion->updateScore();

        // Refresh suggestion to get updated vote counts
        $suggestion->refresh();

        return response()->json([
            'success' => true,
            'action' => $action,
            'score' => $suggestion->score,
            'upvotes' => $suggestion->upvotes,
            'downvotes' => $suggestion->downvotes,
            'user_vote' => $suggestion->user_vote,
            'message' => 'Vote ' . $action . ' successfully'
        ]);
    }

    public function convertSuggestionToTask(Request $request, Suggestion $suggestion)
    {
        // Check if user has admin or staff role
        if (!auth()->user()->hasRole(['admin', 'staff'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if already converted
        if ($suggestion->converted_to_task) {
            return response()->json(['error' => 'Suggestion already converted to task'], 400);
        }

        // Create the task
        $task = Task::create([
            'title' => Str::limit($suggestion->content, 255),
            'description' => $suggestion->content,
            'status' => TaskStatus::Ideas,
            'user_id' => auth()->id(), // Assign to current admin/staff
            'created_by' => $suggestion->user_id, // Keep original author
            'priority' => 'medium',
        ]);

        // Mark suggestion as converted
        $suggestion->update([
            'converted_to_task' => true,
            'task_id' => $task->id,
        ]);

        return response()->json([
            'success' => true,
            'task' => $task,
            'message' => 'Suggestion converted to task successfully!'
        ]);
    }

    public function endSprint(Sprint $sprint)
    {
        // Check if user has admin role
        if (!auth()->user()->hasRole(['admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Verify sprint is active
        if (!$sprint->is_active) {
            return response()->json(['error' => 'Sprint is already inactive'], 400);
        }

        // Get the next sprint (the one with the earliest start_date that hasn't been used)
        $nextSprint = Sprint::where('is_active', false)
            ->whereDoesntHave('tasks')
            ->orderBy('start_date', 'asc')
            ->first();

        $summary = [
            'todo_moved' => 0,
            'done_archived' => 0,
            'in_progress_moved' => 0,
            'review_moved' => 0,
            'in_progress_to_backlog' => 0,
            'review_to_backlog' => 0
        ];

        // Get all tasks in this sprint
        $sprintTasks = Task::where('sprint_id', $sprint->id)->get();

        foreach ($sprintTasks as $task) {
            switch ($task->status->value) {
                case 'todo':
                    // Move to-do tasks to backlog and remove sprint assignment
                    $task->update([
                        'status' => TaskStatus::Backlog,
                        'sprint_id' => null
                    ]);
                    $summary['todo_moved']++;
                    break;

                case 'done':
                    // Archive done tasks
                    $task->update(['archived_at' => now()]);
                    $summary['done_archived']++;
                    break;

                case 'in_progress':
                    if ($nextSprint) {
                        // Move in-progress tasks to next sprint
                        $task->update(['sprint_id' => $nextSprint->id]);
                        $summary['in_progress_moved']++;
                    } else {
                        // No next sprint available, move to backlog
                        $task->update([
                            'status' => TaskStatus::Backlog,
                            'sprint_id' => null
                        ]);
                        $summary['in_progress_to_backlog']++;
                    }
                    break;

                case 'review':
                    if ($nextSprint) {
                        // Move review tasks to next sprint
                        $task->update(['sprint_id' => $nextSprint->id]);
                        $summary['review_moved']++;
                    } else {
                        // No next sprint available, move to backlog
                        $task->update([
                            'status' => TaskStatus::Backlog,
                            'sprint_id' => null
                        ]);
                        $summary['review_to_backlog']++;
                    }
                    break;

                // Ideas and backlog tasks should already not be in sprints
                default:
                    // For any other status, just remove sprint assignment
                    $task->update(['sprint_id' => null]);
                    break;
            }
        }

        // Mark the sprint as inactive
        $sprint->update(['is_active' => false]);

        // Build summary message
        $message = "Sprint '{$sprint->name}' has been ended successfully!\n\n";
        $message .= "Summary:\n";
        $message .= "• {$summary['todo_moved']} To-Do tasks moved to backlog\n";
        $message .= "• {$summary['done_archived']} Done tasks archived\n";
        
        if ($nextSprint) {
            $message .= "• {$summary['in_progress_moved']} In-Progress tasks moved to '{$nextSprint->name}'\n";
            $message .= "• {$summary['review_moved']} Review tasks moved to '{$nextSprint->name}'";
        } else {
            $message .= "• {$summary['in_progress_to_backlog']} In-Progress tasks moved to backlog (no next sprint available)\n";
            $message .= "• {$summary['review_to_backlog']} Review tasks moved to backlog (no next sprint available)";
            
            if ($summary['in_progress_to_backlog'] > 0 || $summary['review_to_backlog'] > 0) {
                $message .= "\n\nNote: Create a new sprint to avoid moving in-progress/review tasks to backlog.";
            }
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'summary' => $summary,
            'next_sprint' => $nextSprint ? $nextSprint->name : null
        ]);
    }
}
