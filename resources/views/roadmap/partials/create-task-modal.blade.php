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
            <div @click.stop class="bg-gray-800 rounded-lg p-6 max-w-6xl w-full max-h-[95vh] overflow-y-auto">
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

                    @include('roadmap.partials.task-form-fields', ['isEdit' => false])

                    <div class="flex justify-end space-x-3 mt-6">
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