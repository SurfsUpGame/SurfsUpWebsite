<div class="flex items-center justify-between mb-6">
    <div class="flex items-center space-x-4">
        <button @click="expanded = !expanded" class="text-gray-600 hover:text-gray-800 transition-colors">
            <i class="fas fa-chevron-right transform transition-transform duration-200" :class="{'rotate-90': expanded}"></i>
        </button>
        <div>
            <h2 class="text-3xl font-bold text-gray-800 cursor-pointer" @click="expanded = !expanded">{{ $sprint->name }}</h2>
        @if($sprint->description)
            <p class="text-gray-600 mt-1">{{ $sprint->description }}</p>
        @endif
        <div class="flex items-center space-x-4 mt-2 text-sm text-gray-600">
            @if($sprint->start_date)
                <span><i class="fas fa-play mr-1"></i>{{ $sprint->start_date->format('M d, Y') }}</span>
            @endif
            @if($sprint->end_date)
                <span><i class="fas fa-flag-checkered mr-1"></i>{{ $sprint->end_date->format('M d, Y') }}</span>
            @endif
            @if($sprint->is_active)
                <span class="bg-green-600 px-2 py-1 rounded text-white text-xs">Active</span>
            @else
                <span class="bg-gray-600 px-2 py-1 rounded text-white text-xs">Completed</span>
            @endif
        </div>
    </div>
    @auth
        @if(auth()->user()->hasRole(['admin']) && $sprint->is_active)
            <button @click="endSprint({{ $sprint->id }})" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-md text-white font-medium transition-colors duration-200 flex items-center space-x-2">
                <i class="fas fa-stop"></i>
                <span>End Sprint</span>
            </button>
        @endif
    @endauth
</div>