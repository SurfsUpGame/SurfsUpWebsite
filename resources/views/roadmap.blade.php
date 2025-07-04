<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roadmap - SurfsUp</title>
    <meta name="description" content="Check out the development roadmap for SurfsUp">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('/img/favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://kit.fontawesome.com/d251d3e9b0.js" crossorigin="anonymous"></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    <style>
        [x-cloak] { display: none !important; }
        .kanban-column {
            min-width: 300px;
            max-width: 350px;
        }
        .kanban-card {
            cursor: move;
        }
        .kanban-card.dragging {
            opacity: 0.5;
        }
    </style>
</head>
<body class="bg-gray-900 text-white antialiased min-h-screen flex flex-col" style="background-image: url('{{ asset('img/surfsup-hero.png') }}'); background-size: cover; background-position: center; background-attachment: fixed;">
    @include('partials.header')

    <main class="container mx-auto px-4 py-8 flex-grow" x-data="roadmapData()" x-init="console.log('Alpine.js loaded on main')">
        @if(session('success'))
            <div class="bg-green-600 text-white px-4 py-3 rounded-md mb-4 mt-16">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex items-center justify-between mt-16 mb-8">
            <h1 class="text-4xl font-bold text-center flex-1 drop-shadow-lg">Development Roadmap</h1>
            @auth
                @if(auth()->user()->hasRole(['admin', 'staff']))
                    <button @click="showCreateModal = true" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-md text-white font-medium transition-colors duration-200 flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Add Task</span>
                    </button>
                @endif
            @endauth
        </div>

        <!-- Main Kanban Board (excluding backlog) -->
        <div class="flex overflow-x-auto gap-6 pb-4">
            @foreach($statuses as $status)
                @if(!in_array($status->value, ['backlog', 'ideas']))
                    <div class="kanban-column flex-shrink-0">
                        <div class="bg-gray-800 rounded-lg p-4">
                            <h2 class="text-xl font-semibold mb-4 text-{{ $status->getColor() }}-400">
                                {{ $status->getTitle() }}
                            </h2>

                            <div class="space-y-3" id="column-{{ $status->value }}" data-status="{{ $status->value }}">
                                @foreach($tasksByStatus[$status->value] ?? [] as $task)
                                    <div class="kanban-card bg-gray-700 rounded-lg p-4 hover:bg-gray-600 transition-colors cursor-pointer"
                                         @auth @if(auth()->user()->hasRole(['admin', 'staff'])) draggable="true" @endif @endauth
                                         data-task-id="{{ $task->id }}"
                                         @click="openTaskDetails({
                                             id: {{ $task->id }},
                                             title: '{{ addslashes($task->title) }}',
                                             description: '{{ addslashes($task->description ?? '') }}',
                                             status: '{{ $task->status->getTitle() }}',
                                             assigned_user: '{{ $task->user ? addslashes($task->user->name) : 'Unassigned' }}',
                                             creator: '{{ $task->creator ? addslashes($task->creator->name) : 'Unknown' }}',
                                             due_date: '{{ $task->due_date ? $task->due_date->format('M d, Y g:i A') : 'No due date' }}',
                                             created_at: '{{ $task->created_at->format('M d, Y g:i A') }}'
                                         })">
                                        <h3 class="font-semibold mb-2">{{ $task->title }}</h3>

                                        @if($task->description)
                                            <p class="text-sm text-gray-300 mb-2">{{ Str::limit($task->description, 100) }}</p>
                                        @endif

                                        <div class="flex items-center justify-between text-xs text-gray-400">
                                            @if($task->user)
                                                <span>
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $task->user->name }}
                                                </span>
                                            @endif

                                            @if($task->due_date)
                                                <span>
                                                    <i class="fas fa-calendar mr-1"></i>
                                                    {{ $task->due_date->format('M d, Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Backlog and Ideas Sections Side by Side -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Backlog Section -->
            <div>
                <h2 class="text-2xl font-bold mb-4 text-white drop-shadow-lg">
                    <i class="fas fa-archive mr-2"></i>Backlog
                </h2>
                <div class="bg-gray-800 rounded-lg p-6">
                    <div class="space-y-4" id="column-backlog" data-status="backlog">
                        @foreach($tasksByStatus['backlog'] ?? [] as $task)
                            <div class="kanban-card bg-gray-700 rounded-lg p-4 hover:bg-gray-600 transition-colors cursor-pointer"
                                 @auth @if(auth()->user()->hasRole(['admin', 'staff'])) draggable="true" @endif @endauth
                                 data-task-id="{{ $task->id }}"
                                 @click="openTaskDetails({
                                     id: {{ $task->id }},
                                     title: '{{ addslashes($task->title) }}',
                                     description: '{{ addslashes($task->description ?? '') }}',
                                     status: '{{ $task->status->getTitle() }}',
                                     assigned_user: '{{ $task->user ? addslashes($task->user->name) : 'Unassigned' }}',
                                     creator: '{{ $task->creator ? addslashes($task->creator->name) : 'Unknown' }}',
                                     due_date: '{{ $task->due_date ? $task->due_date->format('M d, Y g:i A') : 'No due date' }}',
                                     created_at: '{{ $task->created_at->format('M d, Y g:i A') }}'
                                 })">
                                <h3 class="font-semibold mb-2">{{ $task->title }}</h3>

                                @if($task->description)
                                    <p class="text-sm text-gray-300 mb-2">{{ Str::limit($task->description, 120) }}</p>
                                @endif

                                <div class="flex items-center justify-between text-xs text-gray-400">
                                    @if($task->user)
                                        <span>
                                            <i class="fas fa-user mr-1"></i>
                                            {{ $task->user->name }}
                                        </span>
                                    @endif

                                    @if($task->due_date)
                                        <span>
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ $task->due_date->format('M d, Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if(empty($tasksByStatus['backlog']) || count($tasksByStatus['backlog']) === 0)
                        <div class="text-center text-gray-400 py-8">
                            <i class="fas fa-inbox text-4xl mb-4"></i>
                            <p>No backlog items yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Ideas Section -->
            <div>
                <h2 class="text-2xl font-bold mb-4 text-white drop-shadow-lg">
                    <i class="fas fa-lightbulb mr-2"></i>Ideas
                </h2>
                <div class="bg-gray-800 rounded-lg p-6">
                    <div class="space-y-4" id="column-ideas" data-status="ideas">
                        @foreach($tasksByStatus['ideas'] ?? [] as $task)
                            <div class="kanban-card bg-gray-700 rounded-lg p-4 hover:bg-gray-600 transition-colors cursor-pointer"
                                 @auth @if(auth()->user()->hasRole(['admin', 'staff'])) draggable="true" @endif @endauth
                                 data-task-id="{{ $task->id }}"
                                 @click="openTaskDetails({
                                     id: {{ $task->id }},
                                     title: '{{ addslashes($task->title) }}',
                                     description: '{{ addslashes($task->description ?? '') }}',
                                     status: '{{ $task->status->getTitle() }}',
                                     assigned_user: '{{ $task->user ? addslashes($task->user->name) : 'Unassigned' }}',
                                     creator: '{{ $task->creator ? addslashes($task->creator->name) : 'Unknown' }}',
                                     due_date: '{{ $task->due_date ? $task->due_date->format('M d, Y g:i A') : 'No due date' }}',
                                     created_at: '{{ $task->created_at->format('M d, Y g:i A') }}'
                                 })">
                                <h3 class="font-semibold mb-2">{{ $task->title }}</h3>

                                @if($task->description)
                                    <p class="text-sm text-gray-300 mb-2">{{ Str::limit($task->description, 120) }}</p>
                                @endif

                                <div class="flex items-center justify-between text-xs text-gray-400">
                                    @if($task->user)
                                        <span>
                                            <i class="fas fa-user mr-1"></i>
                                            {{ $task->user->name }}
                                        </span>
                                    @endif

                                    @if($task->due_date)
                                        <span>
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ $task->due_date->format('M d, Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if(empty($tasksByStatus['ideas']) || count($tasksByStatus['ideas']) === 0)
                        <div class="text-center text-gray-400 py-8">
                            <i class="fas fa-brain text-4xl mb-4"></i>
                            <p>No ideas yet - let your creativity flow!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @auth
            @if(auth()->user()->hasRole(['admin', 'staff']))
                <!-- Create Task Modal -->
                <div x-show="showCreateModal"
                     x-cloak
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click.away="showCreateModal = false"
                     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                    <div @click.stop class="bg-gray-800 rounded-lg p-6 max-w-md w-full">
                        <h2 class="text-2xl font-bold mb-4">Create New Task</h2>

                        <form action="{{ route('roadmap.store') }}" method="POST">
                            @csrf

                            @if($errors->any())
                                <div class="mb-4 p-4 bg-red-600 rounded-md">
                                    <ul class="text-sm">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="mb-4">
                                <label for="title" class="block text-sm font-medium mb-2">Title</label>
                                <input type="text"
                                       name="title"
                                       id="title"
                                       value="{{ old('title') }}"
                                       required
                                       class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div class="mb-4">
                                <label for="description" class="block text-sm font-medium mb-2">Description</label>
                                <textarea name="description"
                                          id="description"
                                          rows="3"
                                          class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('description') }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label for="status" class="block text-sm font-medium mb-2">Status</label>
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
                            </div>

                            <div class="mb-4">
                                <label for="user_id" class="block text-sm font-medium mb-2">Assign To</label>
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
                            </div>

                            <div class="mb-4">
                                <label for="due_date" class="block text-sm font-medium mb-2">Due Date (Optional)</label>
                                <input type="datetime-local"
                                       name="due_date"
                                       id="due_date"
                                       value="{{ old('due_date') }}"
                                       class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button type="button"
                                        @click="showCreateModal = false"
                                        class="px-4 py-2 text-gray-300 hover:text-white transition-colors duration-200">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-md text-white font-medium transition-colors duration-200">
                                    Create Task
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        @endauth

        <!-- Task Details Modal -->
        <div x-show="showDetailsModal"
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click.away="showDetailsModal = false"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div @click.stop class="bg-gray-800 rounded-lg p-6 max-w-lg w-full">
                <div x-show="selectedTask">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-bold" x-text="selectedTask ? selectedTask.title : ''"></h2>
                        <button @click="showDetailsModal = false" class="text-gray-400 hover:text-white">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-300 mb-1">Status</h3>
                            <span class="inline-block px-3 py-1 bg-blue-600 text-white rounded-full text-sm" x-text="selectedTask ? selectedTask.status : ''"></span>
                        </div>

                        <div x-show="selectedTask && selectedTask.description">
                            <h3 class="text-sm font-semibold text-gray-300 mb-1">Description</h3>
                            <p class="text-gray-200" x-text="selectedTask ? selectedTask.description : ''"></p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-300 mb-1">Assigned To</h3>
                                <p class="text-gray-200 flex items-center">
                                    <i class="fas fa-user mr-2"></i>
                                    <span x-text="selectedTask ? selectedTask.assigned_user : ''"></span>
                                </p>
                            </div>

                            <div>
                                <h3 class="text-sm font-semibold text-gray-300 mb-1">Created By</h3>
                                <p class="text-gray-200 flex items-center">
                                    <i class="fas fa-user-plus mr-2"></i>
                                    <span x-text="selectedTask ? selectedTask.creator : ''"></span>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-300 mb-1">Due Date</h3>
                                <p class="text-gray-200 flex items-center">
                                    <i class="fas fa-calendar mr-2"></i>
                                    <span x-text="selectedTask ? selectedTask.due_date : ''"></span>
                                </p>
                            </div>

                            <div>
                                <h3 class="text-sm font-semibold text-gray-300 mb-1">Created On</h3>
                                <p class="text-gray-200 flex items-center">
                                    <i class="fas fa-clock mr-2"></i>
                                    <span x-text="selectedTask ? selectedTask.created_at : ''"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    @auth
                        @if(auth()->user()->hasRole(['admin', 'staff']))
                            <div class="mt-6 flex justify-between">
                                <div class="space-x-3">
                                    <button @click="archiveTask(selectedTask.id)" class="bg-yellow-600 hover:bg-yellow-700 px-4 py-2 rounded-md text-white font-medium transition-colors duration-200">
                                        <i class="fas fa-archive mr-2"></i>Archive
                                    </button>
                                    <button @click="deleteTask(selectedTask.id)" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-md text-white font-medium transition-colors duration-200">
                                        <i class="fas fa-trash mr-2"></i>Delete
                                    </button>
                                </div>
                                <button @click="showDetailsModal = false" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-md text-white font-medium transition-colors duration-200">
                                    Close
                                </button>
                            </div>
                        @else
                            <div class="mt-6 flex justify-end">
                                <button @click="showDetailsModal = false" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-md text-white font-medium transition-colors duration-200">
                                    Close
                                </button>
                            </div>
                        @endif
                    @else
                        <div class="mt-6 flex justify-end">
                            <button @click="showDetailsModal = false" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-md text-white font-medium transition-colors duration-200">
                                Close
                            </button>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </main>

    @include('partials.footer')

    <script>
        // Alpine.js component data
        function roadmapData() {
            return {
                showCreateModal: {{ isset($errors) && $errors->any() ? 'true' : 'false' }},
                showDetailsModal: false,
                selectedTask: null,
                openTaskDetails(task) {
                    this.selectedTask = task;
                    this.showDetailsModal = true;
                },
                archiveTask(taskId) {
                    if (confirm('Are you sure you want to archive this task?')) {
                        fetch(`/roadmap/task/${taskId}/archive`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.showDetailsModal = false;
                                location.reload();
                            } else {
                                alert('Failed to archive task');
                            }
                        })
                        .catch(error => {
                            console.error('Error archiving task:', error);
                            alert('Error archiving task');
                        });
                    }
                },
                deleteTask(taskId) {
                    if (confirm('Are you sure you want to permanently delete this task? This action cannot be undone.')) {
                        fetch(`/roadmap/task/${taskId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.showDetailsModal = false;
                                location.reload();
                            } else {
                                alert('Failed to delete task');
                            }
                        })
                        .catch(error => {
                            console.error('Error deleting task:', error);
                            alert('Error deleting task');
                        });
                    }
                }
            };
        }

        // Simple drag and drop functionality
        let draggedElement = null;

        document.querySelectorAll('.kanban-card').forEach(card => {
            card.addEventListener('dragstart', (e) => {
                draggedElement = e.target;
                e.target.classList.add('dragging');
            });

            card.addEventListener('dragend', (e) => {
                e.target.classList.remove('dragging');
            });
        });

        // Add drop zones for columns (including empty ones)
        document.querySelectorAll('.kanban-column .bg-gray-800, .bg-gray-800:has(#column-backlog), .bg-gray-800:has(#column-ideas)').forEach(columnContainer => {
            const column = columnContainer.querySelector('[id^="column-"]');

            columnContainer.addEventListener('dragover', (e) => {
                e.preventDefault();
                columnContainer.classList.add('bg-gray-700'); // Visual feedback
            });

            columnContainer.addEventListener('dragleave', (e) => {
                // Only remove highlight if we're actually leaving the container
                if (!columnContainer.contains(e.relatedTarget)) {
                    columnContainer.classList.remove('bg-gray-700');
                }
            });

            columnContainer.addEventListener('drop', (e) => {
                e.preventDefault();
                columnContainer.classList.remove('bg-gray-700'); // Remove visual feedback

                if (!draggedElement) return;

                const taskId = draggedElement.dataset.taskId;
                const newStatus = column.dataset.status;
                const currentStatus = draggedElement.closest('[id^="column-"]').dataset.status;

                // Don't do anything if dropped in the same column
                if (newStatus === currentStatus) return;

                // Move the element visually first
                const afterElement = getDragAfterElement(column, e.clientY);
                if (afterElement == null) {
                    column.appendChild(draggedElement);
                } else {
                    column.insertBefore(draggedElement, afterElement);
                }

                // Make AJAX call to update task status
                fetch(`/roadmap/task/${taskId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        status: newStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Task status updated successfully');
                    } else {
                        console.error('Failed to update task status');
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error updating task status:', error);
                    location.reload();
                });
            });
        });

        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('.kanban-card:not(.dragging)')];

            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;

                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child };
                } else {
                    return closest;
                }
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }
    </script>
</body>
</html>
