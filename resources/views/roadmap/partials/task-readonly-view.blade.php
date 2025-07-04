<!-- Read-only view for non-admin users and guests -->
<div class="space-y-4">
    <div x-show="selectedTask && selectedTask.description">
        <h3 class="text-sm font-semibold text-gray-300 mb-1">Description</h3>
        <p class="text-gray-200" x-text="selectedTask ? selectedTask.description : ''"></p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <h3 class="text-sm font-semibold text-gray-300 mb-1">Status</h3>
            <span class="inline-block px-3 py-1 bg-blue-600 text-white rounded-full text-sm" x-text="selectedTask ? selectedTask.status : ''"></span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-300 mb-1">Priority</h3>
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-2 text-yellow-500"></i>
                <span class="text-gray-200" x-text="selectedTask ? selectedTask.priority : ''"></span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <h3 class="text-sm font-semibold text-gray-300 mb-1">Sprint</h3>
            <p class="text-gray-200" x-text="selectedTask ? selectedTask.sprint : ''"></p>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-300 mb-1">Epic</h3>
            <p class="text-gray-200" x-text="selectedTask ? selectedTask.epic : ''"></p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <h3 class="text-sm font-semibold text-gray-300 mb-1">Assigned To</h3>
            <div class="flex items-center space-x-2" x-show="selectedTask && selectedTask.assigned_user && selectedTask.assigned_user !== 'Unassigned'">
                <div x-show="selectedTask && selectedTask.assigned_user_avatar">
                    <img :src="selectedTask.assigned_user_avatar" :alt="selectedTask.assigned_user" class="w-6 h-6 rounded-full border border-gray-500">
                </div>
                <div x-show="selectedTask && !selectedTask.assigned_user_avatar && selectedTask.assigned_user_initials">
                    <div class="w-6 h-6 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-medium" x-text="selectedTask.assigned_user_initials"></div>
                </div>
                <span class="text-gray-200" x-text="selectedTask ? selectedTask.assigned_user : ''"></span>
            </div>
            <span x-show="!selectedTask || !selectedTask.assigned_user || selectedTask.assigned_user === 'Unassigned'" class="text-gray-400 italic">Unassigned</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-300 mb-1">Due Date</h3>
            <div class="flex items-center text-gray-200" x-show="selectedTask && selectedTask.due_date && selectedTask.due_date !== 'No due date'">
                <i class="fas fa-calendar mr-2 text-blue-400"></i>
                <span x-text="selectedTask ? selectedTask.due_date : ''"></span>
            </div>
            <span x-show="!selectedTask || !selectedTask.due_date || selectedTask.due_date === 'No due date'" class="text-gray-400 italic">No due date</span>
        </div>
    </div>

    <div x-show="selectedTask && selectedTask.labels && selectedTask.labels.length > 0">
        <h3 class="text-sm font-semibold text-gray-300 mb-2">Labels</h3>
        <div class="flex flex-wrap gap-2">
            <template x-for="label in (selectedTask ? selectedTask.labels : [])" :key="label">
                <span class="inline-block px-2 py-1 text-xs rounded bg-gray-600 text-gray-200 border border-gray-500" x-text="label"></span>
            </template>
        </div>
    </div>

    <!-- Voting Section -->
    @auth
        <div>
            <h3 class="text-sm font-semibold text-gray-300 mb-2">Community Feedback</h3>
            <div class="flex items-center justify-between bg-gray-700 p-3 rounded-md">
                <div class="flex items-center space-x-2">
                    <button :onclick="`vote(${selectedTask.id}, 1, event)`"
                            class="vote-btn upvote flex items-center space-x-1 px-3 py-2 rounded text-sm transition-colors bg-gray-600 text-gray-300 hover:bg-green-600 hover:text-white">
                        <i class="fas fa-thumbs-up"></i>
                        <span class="upvote-count" x-text="selectedTask ? selectedTask.upvote_count || 0 : 0"></span>
                    </button>
                    <button :onclick="`vote(${selectedTask.id}, -1, event)`"
                            class="vote-btn downvote flex items-center space-x-1 px-3 py-2 rounded text-sm transition-colors bg-gray-600 text-gray-300 hover:bg-red-600 hover:text-white">
                        <i class="fas fa-thumbs-down"></i>
                        <span class="downvote-count" x-text="selectedTask ? selectedTask.downvote_count || 0 : 0"></span>
                    </button>
                </div>
                <div class="text-sm text-gray-400">
                    Score: <span class="vote-score font-semibold text-white" x-text="selectedTask ? selectedTask.vote_score || 0 : 0"></span>
                </div>
            </div>
        </div>
    @else
        <div>
            <h3 class="text-sm font-semibold text-gray-300 mb-2">Community Feedback</h3>
            <div class="flex items-center justify-between bg-gray-700 p-3 rounded-md">
                <div class="flex items-center space-x-2">
                    <div class="flex items-center space-x-1 px-3 py-2 rounded text-sm bg-gray-600 text-gray-300">
                        <i class="fas fa-thumbs-up"></i>
                        <span x-text="selectedTask ? selectedTask.upvote_count || 0 : 0"></span>
                    </div>
                    <div class="flex items-center space-x-1 px-3 py-2 rounded text-sm bg-gray-600 text-gray-300">
                        <i class="fas fa-thumbs-down"></i>
                        <span x-text="selectedTask ? selectedTask.downvote_count || 0 : 0"></span>
                    </div>
                </div>
                <div class="text-sm text-gray-400">
                    Score: <span class="font-semibold text-white" x-text="selectedTask ? selectedTask.vote_score || 0 : 0"></span>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Login to vote on this task</p>
        </div>
    @endauth

    <div>
        <h3 class="text-sm font-semibold text-gray-300 mb-2">Task Information</h3>
        <div class="text-xs text-gray-400 space-y-1 bg-gray-700 p-3 rounded-md">
            <div class="flex items-center" x-show="selectedTask && selectedTask.creator">
                <i class="fas fa-user-plus mr-2 text-green-400"></i>
                <span>Created by: </span>
                <div class="flex items-center space-x-2 ml-1">
                    <div x-show="selectedTask && selectedTask.creator_avatar">
                        <img :src="selectedTask.creator_avatar" :alt="selectedTask.creator" class="w-4 h-4 rounded-full border border-gray-500">
                    </div>
                    <div x-show="selectedTask && !selectedTask.creator_avatar && selectedTask.creator_initials">
                        <div class="w-4 h-4 rounded-full bg-green-600 flex items-center justify-center text-white text-xs font-medium" x-text="selectedTask.creator_initials"></div>
                    </div>
                    <span x-text="selectedTask ? selectedTask.creator : ''"></span>
                </div>
            </div>
            <div class="flex items-center" x-show="selectedTask && selectedTask.created_at">
                <i class="fas fa-clock mr-2 text-blue-400"></i>
                <span>Created: <span x-text="selectedTask ? selectedTask.created_at : ''"></span></span>
            </div>
        </div>
    </div>
</div>
<div class="mt-6 flex justify-end">
    <button @click="showDetailsModal = false" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-md text-white font-medium transition-colors duration-200">
        Close
    </button>
</div>