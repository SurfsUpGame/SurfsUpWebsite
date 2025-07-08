<div class="rich-text-editor">
    <!-- Toolbar -->
    <div class="bg-gray-600 border border-gray-600 border-b-0 rounded-t-md px-3 py-2 flex space-x-2">
        <button type="button" onclick="{{ $isEdit ? 'formatEditText' : 'formatText' }}('bold')" class="px-2 py-1 text-xs bg-gray-700 hover:bg-gray-500 rounded text-white font-bold" title="Bold" tabindex="-1">
            <i class="fas fa-bold"></i>
        </button>
        <button type="button" onclick="{{ $isEdit ? 'formatEditText' : 'formatText' }}('italic')" class="px-2 py-1 text-xs bg-gray-700 hover:bg-gray-500 rounded text-white italic" title="Italic" tabindex="-1">
            <i class="fas fa-italic"></i>
        </button>
        <button type="button" onclick="{{ $isEdit ? 'formatEditText' : 'formatText' }}('underline')" class="px-2 py-1 text-xs bg-gray-700 hover:bg-gray-500 rounded text-white underline" title="Underline" tabindex="-1">
            <i class="fas fa-underline"></i>
        </button>
        <button type="button" onclick="{{ $isEdit ? 'formatEditText' : 'formatText' }}('insertUnorderedList')" class="px-2 py-1 text-xs bg-gray-700 hover:bg-gray-500 rounded text-white" title="Bullet List" tabindex="-1">
            <i class="fas fa-list-ul"></i>
        </button>
        <button type="button" onclick="{{ $isEdit ? 'formatEditText' : 'formatText' }}('insertOrderedList')" class="px-2 py-1 text-xs bg-gray-700 hover:bg-gray-500 rounded text-white" title="Numbered List" tabindex="-1">
            <i class="fas fa-list-ol"></i>
        </button>
    </div>
    <!-- Editor -->
    @if($isEdit)
        <div id="edit-description-editor"
             contenteditable="true"
             class="w-full px-3 py-2 bg-gray-700 border border-gray-600 border-t-0 rounded-b-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent min-h-[450px]"
             data-placeholder="Enter task description..."
             oninput="updateEditHiddenField()"
             x-html="selectedTask ? selectedTask.description : ''"
             @click="initEditDescriptionContent()"></div>
        <!-- Hidden field for form submission -->
        <textarea id="edit_description" x-model="selectedTask.description" style="display: none;"></textarea>
    @else
        <div id="description-editor"
             contenteditable="true"
             class="w-full px-3 py-2 bg-gray-700 border border-gray-600 border-t-0 rounded-b-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent min-h-[450px]"
             data-placeholder="Enter task description..."
             oninput="updateHiddenField()">{{ old('description') }}</div>
        <!-- Hidden field for form submission -->
        <textarea name="description" id="description" style="display: none;">{{ old('description') }}</textarea>
    @endif
</div>