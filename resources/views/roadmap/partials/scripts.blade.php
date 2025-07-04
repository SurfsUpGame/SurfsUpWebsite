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
            },
            toggleLabel(labelId) {
                if (!this.selectedTask.label_ids) {
                    this.selectedTask.label_ids = [];
                }
                const index = this.selectedTask.label_ids.indexOf(labelId);
                if (index > -1) {
                    this.selectedTask.label_ids.splice(index, 1);
                } else {
                    this.selectedTask.label_ids.push(labelId);
                }
            },
            updateTask() {
                if (!this.selectedTask) return;

                const formData = {
                    title: this.selectedTask.title,
                    description: this.selectedTask.description,
                    status: this.selectedTask.status_value,
                    user_id: this.selectedTask.assigned_user_id,
                    due_date: this.selectedTask.due_date_value,
                    sprint_id: this.selectedTask.sprint_id || null,
                    epic_id: this.selectedTask.epic_id || null,
                    priority: this.selectedTask.priority_value,
                    labels: this.selectedTask.label_ids || []
                };

                fetch(`/roadmap/task/${this.selectedTask.id}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.showDetailsModal = false;
                        location.reload();
                    } else {
                        alert('Failed to update task: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error updating task:', error);
                    alert('Error updating task');
                });
            },
            endSprint(sprintId) {
                if (confirm('Are you sure you want to end this sprint? This will:\n\n• Mark the sprint as inactive\n• Move all To-Do tasks to the backlog\n• Archive all Done tasks\n• Move In-Progress and Review tasks to the next sprint (or backlog if no next sprint available)\n\nThis action cannot be undone.')) {
                    fetch(`/roadmap/sprint/${sprintId}/end`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message || 'Sprint ended successfully!');
                            location.reload();
                        } else {
                            alert('Failed to end sprint: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error ending sprint:', error);
                        alert('Error ending sprint');
                    });
                }
            }
        };
    }

    // Rich text editor functions
    function formatText(command) {
        document.execCommand(command, false, null);
        updateHiddenField();
    }

    function updateHiddenField() {
        const editor = document.getElementById('description-editor');
        const hiddenField = document.getElementById('description');
        hiddenField.value = editor.innerHTML;
    }

    // Rich text editor functions for edit modal
    function formatEditText(command) {
        document.execCommand(command, false, null);
        updateEditHiddenField();
    }

    function updateEditHiddenField() {
        const editor = document.getElementById('edit-description-editor');
        const hiddenField = document.getElementById('edit_description');
        if (editor && hiddenField) {
            hiddenField.value = editor.innerHTML;
            // Update Alpine.js model
            const selectedTaskData = window.Alpine.store ? window.Alpine.store('selectedTask') : null;
            if (selectedTaskData) {
                selectedTaskData.description = editor.innerHTML;
            }
        }
    }

    function initEditDescriptionContent() {
        const editor = document.getElementById('edit-description-editor');
        const hiddenField = document.getElementById('edit_description');
        if (editor && hiddenField && hiddenField.value) {
            editor.innerHTML = hiddenField.value;
        }
    }

    // Initialize rich text editor content
    document.addEventListener('DOMContentLoaded', function() {
        const editor = document.getElementById('description-editor');
        const hiddenField = document.getElementById('description');

        // Set initial content if there's old input
        if (hiddenField && hiddenField.value) {
            editor.innerHTML = hiddenField.value;
        }

        if (editor) {
            // Add placeholder behavior
            editor.addEventListener('focus', function() {
                if (this.innerHTML === '' || this.innerHTML === '<br>') {
                    this.innerHTML = '';
                }
            });

            editor.addEventListener('blur', function() {
                if (this.innerHTML === '' || this.innerHTML === '<br>') {
                    this.innerHTML = '';
                }
            });
        }
    });

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
            const newSprintId = column.dataset.sprint;
            const currentStatus = draggedElement.closest('[id^="column-"]').dataset.status;
            const currentSprintId = draggedElement.closest('[id^="column-"]').dataset.sprint;

            // Don't do anything if dropped in the same column
            if (newStatus === currentStatus && newSprintId === currentSprintId) return;

            // Move the element visually first
            const afterElement = getDragAfterElement(column, e.clientY);
            if (afterElement == null) {
                column.appendChild(draggedElement);
            } else {
                column.insertBefore(draggedElement, afterElement);
            }

            // Prepare request body
            const requestBody = {
                status: newStatus
            };

            // Add sprint_id to request body (null for unassigned, sprint ID for assigned)
            if (newSprintId === '') {
                requestBody.sprint_id = null;
            } else {
                requestBody.sprint_id = parseInt(newSprintId);
            }

            // Make AJAX call to update task status and sprint
            fetch(`/roadmap/task/${taskId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(requestBody)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Task updated successfully');
                    location.reload();
                } else {
                    console.error('Failed to update task');
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error updating task:', error);
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

    // Voting function
    function vote(taskId, voteValue, event) {
        event.stopPropagation(); // Prevent opening task details modal

        fetch(`/roadmap/task/${taskId}/vote`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                vote: voteValue
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Find the task card and update vote counts
                const taskCard = event.target.closest('.kanban-card');

                // Update upvote count
                const upvoteCount = taskCard.querySelector('.upvote-count');
                if (upvoteCount) {
                    upvoteCount.textContent = data.upvote_count;
                }

                // Update downvote count
                const downvoteCount = taskCard.querySelector('.downvote-count');
                if (downvoteCount) {
                    downvoteCount.textContent = data.downvote_count;
                }

                // Update vote score
                const voteScore = taskCard.querySelector('.vote-score');
                if (voteScore) {
                    voteScore.textContent = data.vote_score;
                }

                // Update button states
                const upvoteBtn = taskCard.querySelector('.upvote');
                const downvoteBtn = taskCard.querySelector('.downvote');

                // Reset button classes
                upvoteBtn.className = upvoteBtn.className.replace(/(bg-green-600|text-white)/g, '').replace(/\s+/g, ' ').trim();
                downvoteBtn.className = downvoteBtn.className.replace(/(bg-red-600|text-white)/g, '').replace(/\s+/g, ' ').trim();

                if (!upvoteBtn.className.includes('bg-gray-600')) {
                    upvoteBtn.className += ' bg-gray-600 text-gray-300 hover:bg-green-600 hover:text-white';
                }
                if (!downvoteBtn.className.includes('bg-gray-600')) {
                    downvoteBtn.className += ' bg-gray-600 text-gray-300 hover:bg-red-600 hover:text-white';
                }

                // Apply active state based on user's current vote
                if (data.user_vote === 1) {
                    upvoteBtn.className = upvoteBtn.className.replace(/(bg-gray-600|text-gray-300|hover:bg-green-600|hover:text-white)/g, '').replace(/\s+/g, ' ').trim();
                    upvoteBtn.className += ' bg-green-600 text-white';
                } else if (data.user_vote === -1) {
                    downvoteBtn.className = downvoteBtn.className.replace(/(bg-gray-600|text-gray-300|hover:bg-red-600|hover:text-white)/g, '').replace(/\s+/g, ' ').trim();
                    downvoteBtn.className += ' bg-red-600 text-white';
                }

                console.log('Vote registered successfully');
            } else {
                alert('Failed to register vote: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error voting:', error);
            alert('Error voting on task');
        });
    }
</script>