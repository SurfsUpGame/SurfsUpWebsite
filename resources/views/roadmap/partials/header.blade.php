<div class="flex items-center justify-between mt-16 mb-8">
    <h1 class="text-4xl font-bold text-center flex-1 text-gray-800">Development Roadmap</h1>
    <div class="flex items-center space-x-4">
        <!-- Toggle between active and past sprints -->
        <div class="flex items-center bg-gray-200 rounded-lg p-1">
            <a href="{{ route('roadmap') }}" class="px-3 py-1 rounded-md text-sm font-medium transition-colors duration-200 {{ !$showPast ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                Active Sprints
            </a>
            <a href="{{ route('roadmap', ['show_past' => true]) }}" class="px-3 py-1 rounded-md text-sm font-medium transition-colors duration-200 {{ $showPast ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                Past Sprints
            </a>
        </div>
        
        @auth
            @if(auth()->user()->hasRole(['admin', 'staff']) && !$showPast)
                <button @click="showCreateModal = true" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-md text-white font-medium transition-colors duration-200 flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Add Task</span>
                </button>
            @endif
        @endauth
    </div>
</div>