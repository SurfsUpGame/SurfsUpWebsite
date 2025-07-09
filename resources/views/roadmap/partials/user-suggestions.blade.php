<!-- User Suggestions Section -->
<div class="mt-12">
    <div class="bg-gray-800 rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-6 text-white flex items-center">
            <i class="fas fa-lightbulb mr-3 text-yellow-400"></i>
            Community Suggestions
        </h2>

        @auth
            <!-- Suggestion Submission Form -->
            <div class="mb-6 bg-gray-700 rounded-lg p-4">
                <form id="suggestion-form" @submit.prevent="submitSuggestion">
                    <label for="suggestion-input" class="block text-sm font-medium mb-2 text-gray-300">
                        Submit Your Suggestion
                    </label>
                    <div class="flex space-x-3">
                        <div class="flex-1">
                            <textarea
                                id="suggestion-input"
                                x-model="suggestionContent"
                                maxlength="280"
                                rows="3"
                                placeholder="Share your ideas to improve SurfsUp (max 280 characters)"
                                class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                required
                            ></textarea>
                            <div class="mt-1 text-xs text-gray-400">
                                <span x-text="suggestionContent.length"></span>/280 characters
                            </div>
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                    :disabled="suggestionContent.length === 0 || suggestionContent.length > 280"
                                    class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-600 disabled:cursor-not-allowed px-4 py-2 rounded-md text-white font-medium transition-colors duration-200">
                                <i class="fas fa-paper-plane mr-2"></i>Submit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @else
            <div class="mb-6 bg-gray-700 rounded-lg p-4 text-center">
                <p class="text-gray-300">
                    <a href="{{ route('auth.steam') }}" class="text-blue-400 hover:text-blue-300 underline">Login</a>
                    to submit your suggestions
                </p>
            </div>
        @endauth

        <!-- Suggestions List -->
        <div class="space-y-4" id="suggestions-list">
            @forelse($suggestions as $suggestion)
                <div class="bg-gray-700 rounded-lg p-4 suggestion-item" data-suggestion-id="{{ $suggestion->id }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 pr-4">
                            <p class="text-white mb-2">{{ $suggestion->content }}</p>
                            <div class="flex items-center space-x-4 text-sm text-gray-400">
                                <div class="flex items-center space-x-2">
                                    @if($suggestion->user->avatar)
                                        <img src="{{ $suggestion->user->avatar }}" alt="{{ $suggestion->user->name }}" class="w-5 h-5 rounded-full">
                                    @else
                                        <div class="w-5 h-5 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-medium">
                                            {{ $suggestion->user->initials() }}
                                        </div>
                                    @endif
                                    <span>{{ $suggestion->user->name }}</span>
                                </div>
                                <span>•</span>
                                <span>{{ $suggestion->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <!-- Voting Buttons -->
                            @auth
                                <div class="flex items-center space-x-2">
                                    <button onclick="voteSuggestion({{ $suggestion->id }}, 1)"
                                            class="suggestion-vote-btn suggestion-upvote flex items-center space-x-1 px-3 py-1 rounded text-sm transition-colors
                                                   {{ $suggestion->user_vote === 1 ? 'bg-green-600 text-white' : 'bg-gray-600 text-gray-300 hover:bg-green-600 hover:text-white' }}">
                                        <i class="fas fa-thumbs-up"></i>
                                        <span class="suggestion-upvote-count">{{ $suggestion->upvotes }}</span>
                                    </button>
                                    <button onclick="voteSuggestion({{ $suggestion->id }}, -1)"
                                            class="suggestion-vote-btn suggestion-downvote flex items-center space-x-1 px-3 py-1 rounded text-sm transition-colors
                                                   {{ $suggestion->user_vote === -1 ? 'bg-red-600 text-white' : 'bg-gray-600 text-gray-300 hover:bg-red-600 hover:text-white' }}">
                                        <i class="fas fa-thumbs-down"></i>
                                        <span class="suggestion-downvote-count">{{ $suggestion->downvotes }}</span>
                                    </button>
                                </div>
                            @else
                                <div class="flex items-center space-x-2">
                                    <div class="flex items-center space-x-1 px-3 py-1 rounded text-sm bg-gray-600 text-gray-300">
                                        <i class="fas fa-thumbs-up"></i>
                                        <span>{{ $suggestion->upvotes }}</span>
                                    </div>
                                    <div class="flex items-center space-x-1 px-3 py-1 rounded text-sm bg-gray-600 text-gray-300">
                                        <i class="fas fa-thumbs-down"></i>
                                        <span>{{ $suggestion->downvotes }}</span>
                                    </div>
                                </div>
                            @endauth

                            <div class="text-sm text-gray-400">
                                Score: <span class="suggestion-score font-semibold text-white">{{ $suggestion->score }}</span>
                            </div>

                            <!-- Create as Task Button -->
                            @auth
                                @if(auth()->user()->hasRole(['admin', 'staff']))
                                    <button onclick="convertSuggestionToTask({{ $suggestion->id }})"
                                            class="bg-purple-600 hover:bg-purple-700 px-3 py-1 rounded text-sm text-white font-medium transition-colors duration-200"
                                            title="Convert to Task">
                                        <i class="fas fa-tasks mr-1"></i>Create Task
                                    </button>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-lightbulb text-4xl mb-3 opacity-50"></i>
                    <p>No suggestions yet. Be the first to share your ideas!</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
