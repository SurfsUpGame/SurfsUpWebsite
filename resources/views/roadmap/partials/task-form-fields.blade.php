<!-- Title and Description (Full Width) -->
<div class="mb-6">
    <label for="{{ $isEdit ? 'edit_title' : 'title' }}" class="block text-sm font-medium mb-2">Title</label>
    @if($isEdit)
        <input type="text"
               id="edit_title"
               x-model="selectedTask.title"
               required
               class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    @else
        <input type="text"
               name="title"
               id="title"
               value="{{ old('title') }}"
               required
               class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    @endif
</div>

<div class="mb-6">
    <label for="{{ $isEdit ? 'edit_description' : 'description' }}" class="block text-sm font-medium mb-2">Description</label>
    @include('roadmap.partials.rich-text-editor', ['isEdit' => $isEdit])
</div>

<!-- Two Column Layout -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Left Column -->
    <div class="space-y-4">
        <div>
            <label for="{{ $isEdit ? 'edit_status' : 'status' }}" class="block text-sm font-medium mb-2">Status</label>
            @if($isEdit)
                <select id="edit_status"
                        x-model="selectedTask.status_value"
                        required
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->getTitle() }}</option>
                    @endforeach
                </select>
            @else
                <select name="status"
                        id="status"
                        required
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" {{ old('status', 'todo') === $status->value ? 'selected' : '' }}>
                            {{ $status->getTitle() }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        <div>
            <label for="{{ $isEdit ? 'edit_user_id' : 'user_id' }}" class="block text-sm font-medium mb-2">Assign To</label>
            @if($isEdit)
                <select id="edit_user_id"
                        x-model="selectedTask.assigned_user_id"
                        required
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Select a user...</option>
                    @foreach($eligibleUsers as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            @else
                <select name="user_id"
                        id="user_id"
                        required
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Select a user...</option>
                    @foreach($eligibleUsers as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', auth()->id()) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        <div>
            <label for="{{ $isEdit ? 'edit_sprint_id' : 'sprint_id' }}" class="block text-sm font-medium mb-2">Sprint (Optional)</label>
            @if($isEdit)
                <select id="edit_sprint_id"
                        x-model="selectedTask.sprint_id"
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">No sprint</option>
                    @foreach($sprints as $sprint)
                        <option value="{{ $sprint->id }}">{{ $sprint->name }}</option>
                    @endforeach
                </select>
            @else
                <select name="sprint_id"
                        id="sprint_id"
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">No sprint</option>
                    @foreach($sprints as $sprint)
                        <option value="{{ $sprint->id }}" {{ old('sprint_id') == $sprint->id ? 'selected' : '' }}>
                            {{ $sprint->name }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        <div>
            <label for="{{ $isEdit ? 'edit_due_date' : 'due_date' }}" class="block text-sm font-medium mb-2">Due Date (Optional)</label>
            @if($isEdit)
                <input type="datetime-local"
                       id="edit_due_date"
                       x-model="selectedTask.due_date_value"
                       class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            @else
                <input type="datetime-local"
                       name="due_date"
                       id="due_date"
                       value="{{ old('due_date') }}"
                       class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            @endif
        </div>
    </div>

    <!-- Right Column -->
    <div class="space-y-4">
        <div>
            <label for="{{ $isEdit ? 'edit_epic_id' : 'epic_id' }}" class="block text-sm font-medium mb-2">Epic (Optional)</label>
            @if($isEdit)
                <select id="edit_epic_id"
                        x-model="selectedTask.epic_id"
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">No epic</option>
                    @foreach($epics as $epic)
                        <option value="{{ $epic->id }}">{{ $epic->name }}</option>
                    @endforeach
                </select>
            @else
                <select name="epic_id"
                        id="epic_id"
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">No epic</option>
                    @foreach($epics as $epic)
                        <option value="{{ $epic->id }}" {{ old('epic_id') == $epic->id ? 'selected' : '' }}>
                            {{ $epic->name }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        <div>
            <label for="{{ $isEdit ? 'edit_priority' : 'priority' }}" class="block text-sm font-medium mb-2">Priority</label>
            @if($isEdit)
                <select id="edit_priority"
                        x-model="selectedTask.priority_value"
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="critical">Critical</option>
                </select>
            @else
                <select name="priority"
                        id="priority"
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="low" {{ old('priority', 'medium') === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ old('priority', 'medium') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="critical" {{ old('priority', 'medium') === 'critical' ? 'selected' : '' }}>Critical</option>
                </select>
            @endif
        </div>

        <div>
            <label class="block text-sm font-medium mb-2">Labels (Optional)</label>
            <div class="max-h-32 overflow-y-auto bg-gray-700 border border-gray-600 rounded-md p-2">
                @foreach($labels as $label)
                    <label class="flex items-center space-x-2 py-1">
                        @if($isEdit)
                            <input type="checkbox"
                                   :checked="selectedTask && selectedTask.label_ids && selectedTask.label_ids.includes({{ $label->id }})"
                                   @change="toggleLabel({{ $label->id }})"
                                   class="rounded bg-gray-600 border-gray-500 text-blue-600 focus:ring-blue-500">
                        @else
                            <input type="checkbox"
                                   name="labels[]"
                                   value="{{ $label->id }}"
                                   {{ in_array($label->id, old('labels', [])) ? 'checked' : '' }}
                                   class="rounded bg-gray-600 border-gray-500 text-blue-600 focus:ring-blue-500">
                        @endif
                        <span class="inline-block px-2 py-1 text-xs rounded" style="background-color: {{ $label->color }}20; color: {{ $label->color }}; border: 1px solid {{ $label->color }}30;">
                            {{ $label->name }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        @if($isEdit)
            <div>
                <h3 class="text-sm font-semibold text-gray-300 mb-2">Task Info</h3>
                <div class="text-xs text-gray-400 space-y-1">
                    <p><i class="fas fa-user-plus mr-2"></i>Created by: <span x-text="selectedTask ? selectedTask.creator : ''"></span></p>
                    <p><i class="fas fa-clock mr-2"></i>Created: <span x-text="selectedTask ? selectedTask.created_at : ''"></span></p>
                </div>
            </div>
        @endif
    </div>
</div>