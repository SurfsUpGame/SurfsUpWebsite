<!-- Backlog and Ideas Sections Side by Side (only shown for active sprints) -->
@if(!$showPast)
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Backlog Section -->
    <div>
        <h2 class="text-2xl font-bold mb-4 text-gray-800">
            <i class="fas fa-archive mr-2"></i>Backlog
        </h2>
        <div class="bg-gray-800 rounded-lg p-6">
            <div class="space-y-4" id="column-backlog" data-status="backlog">
                @foreach($tasksByStatus['backlog'] ?? [] as $task)
                    @include('roadmap.partials.task-card', ['task' => $task, 'descriptionLimit' => 120])
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
        <h2 class="text-2xl font-bold mb-4 text-gray-800">
            <i class="fas fa-lightbulb mr-2"></i>Ideas
        </h2>
        <div class="bg-gray-800 rounded-lg p-6">
            <div class="space-y-4" id="column-ideas" data-status="ideas">
                @foreach($tasksByStatus['ideas'] ?? [] as $task)
                    @include('roadmap.partials.task-card', ['task' => $task, 'descriptionLimit' => 120])
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
@endif