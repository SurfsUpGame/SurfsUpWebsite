<style>
    [x-cloak] { display: none !important; }
    .kanban-column {
        flex: 1;
        min-width: 280px;
    }
    .kanban-card {
        cursor: pointer;
    }
    @auth
        @if(auth()->user()->hasRole(['admin', 'staff']))
            .kanban-card[draggable="true"] {
                cursor: move;
            }
        @endif
    @endauth
    .kanban-card.dragging {
        opacity: 0.5;
    }

    /* Rich text editor styles */
    #description-editor:empty:before,
    #edit-description-editor:empty:before {
        content: attr(data-placeholder);
        color: #9CA3AF;
        pointer-events: none;
    }

    #description-editor:focus:before,
    #edit-description-editor:focus:before {
        display: none;
    }

    #description-editor,
    #edit-description-editor {
        height: 120px;
        overflow-y: auto;
        line-height: 1.5;
    }

    #description-editor b, #description-editor strong,
    #edit-description-editor b, #edit-description-editor strong {
        font-weight: bold;
    }

    #description-editor i, #description-editor em,
    #edit-description-editor i, #edit-description-editor em {
        font-style: italic;
    }

    #description-editor u,
    #edit-description-editor u {
        text-decoration: underline;
    }

    #description-editor ul, #description-editor ol,
    #edit-description-editor ul, #edit-description-editor ol {
        margin-left: 20px;
    }

    #description-editor ul,
    #edit-description-editor ul {
        list-style-type: disc;
    }

    #description-editor ol,
    #edit-description-editor ol {
        list-style-type: decimal;
    }
</style>