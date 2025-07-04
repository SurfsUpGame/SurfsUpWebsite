<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Sprint;
use App\Models\Epic;
use App\Models\Label;
use App\Enums\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RoadmapController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['user', 'creator', 'sprint', 'epic', 'labels'])->whereNull('archived_at')->get();
        $statuses = TaskStatus::cases();
        $sprints = Sprint::all();
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

        return view('roadmap', [
            'statuses' => $statuses,
            'tasksByStatus' => $tasksByStatus,
            'tasks' => $tasks,
            'eligibleUsers' => $eligibleUsers,
            'sprints' => $sprints,
            'epics' => $epics,
            'labels' => $labels,
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
}
