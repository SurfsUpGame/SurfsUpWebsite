<!-- Task Edit Modal -->
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
    <div @click.stop class="bg-gray-800 rounded-lg p-6 max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div x-show="selectedTask">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold" x-text="selectedTask ? selectedTask.title : ''"></h2>
                <button @click="showDetailsModal = false" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            @auth
                @if(auth()->user()->hasRole(['admin', 'staff']))
                    <form @submit.prevent="updateTask">
                        @include('roadmap.partials.task-form-fields', ['isEdit' => true])

                        <div class="flex justify-between mt-6">
                            <div class="space-x-3">
                                <button type="button" @click="archiveTask(selectedTask.id)" class="bg-yellow-600 hover:bg-yellow-700 px-4 py-2 rounded-md text-white font-medium transition-colors duration-200">
                                    <i class="fas fa-archive mr-2"></i>Archive
                                </button>
                                <button type="button" @click="deleteTask(selectedTask.id)" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-md text-white font-medium transition-colors duration-200">
                                    <i class="fas fa-trash mr-2"></i>Delete
                                </button>
                            </div>
                            <div class="space-x-3">
                                <button type="button"
                                        @click="showDetailsModal = false"
                                        class="px-4 py-2 text-gray-300 hover:text-white transition-colors duration-200">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-md text-white font-medium transition-colors duration-200">
                                    <i class="fas fa-save mr-2"></i>Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    @include('roadmap.partials.task-readonly-view')
                @endif
            @else
                @include('roadmap.partials.task-readonly-view')
            @endauth
        </div>
    </div>
</div>