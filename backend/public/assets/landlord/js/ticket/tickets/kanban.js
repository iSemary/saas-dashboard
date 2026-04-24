/**
 * Kanban Board JavaScript for Ticket Management
 * Uses AlpineJS for reactivity and drag & drop functionality
 */

function kanbanBoard() {
    return {
        kanbanData: {},
        loading: false,
        showFilters: false,
        autoRefresh: false,
        autoRefreshInterval: null,
        filters: {
            assigned_to: '',
            priority: '',
            search: ''
        },
        draggedTicket: null,

        init() {
            this.loadKanbanData();
            this.setupAutoRefresh();
        },

        async loadKanbanData() {
            this.loading = true;
            try {
                const response = await fetch('/landlord/tickets/kanban-data', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.kanbanData = data.data;
                } else {
                    throw new Error('Failed to load kanban data');
                }
            } catch (error) {
                console.error('Error loading kanban data:', error);
                toastr.error(translate('error_loading_data'));
            } finally {
                this.loading = false;
            }
        },

        async refreshBoard() {
            await this.loadKanbanData();
            toastr.success(translate('board_refreshed'));
        },

        toggleFilters() {
            this.showFilters = !this.showFilters;
        },

        toggleAutoRefresh() {
            this.autoRefresh = !this.autoRefresh;
            if (this.autoRefresh) {
                this.autoRefreshInterval = setInterval(() => {
                    this.loadKanbanData();
                }, 30000); // Refresh every 30 seconds
                toastr.info(translate('auto_refresh_enabled'));
            } else {
                if (this.autoRefreshInterval) {
                    clearInterval(this.autoRefreshInterval);
                    this.autoRefreshInterval = null;
                }
                toastr.info(translate('auto_refresh_disabled'));
            }
        },

        applyFilters() {
            // Filter tickets based on current filters
            // This would typically make an API call with filter parameters
            this.loadKanbanData();
        },

        clearFilters() {
            this.filters = {
                assigned_to: '',
                priority: '',
                search: ''
            };
            this.applyFilters();
        },

        handleDragStart(event, ticket) {
            this.draggedTicket = ticket;
            ticket.is_dragging = true;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/html', event.target.outerHTML);
            event.target.classList.add('dragging');
        },

        handleDragEnd(event, ticket) {
            ticket.is_dragging = false;
            event.target.classList.remove('dragging');
        },

        handleDrop(event, newStatus) {
            event.preventDefault();
            const ticketsContainer = event.currentTarget;
            ticketsContainer.classList.remove('drag-over');

            if (this.draggedTicket && this.draggedTicket.status !== newStatus) {
                this.updateTicketStatus(this.draggedTicket.id, newStatus);
            }
        },

        async updateTicketStatus(ticketId, newStatus) {
            try {
                const response = await fetch(`/landlord/tickets/${ticketId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: newStatus,
                        comment: `Status changed via Kanban board`
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    toastr.success(translate('status_updated'));
                    await this.loadKanbanData(); // Refresh the board
                } else {
                    throw new Error('Failed to update status');
                }
            } catch (error) {
                console.error('Error updating ticket status:', error);
                toastr.error(translate('error_updating_status'));
            }
        },

        assignTicket(ticketId) {
            // Open assign modal
            const modal = new bootstrap.Modal(document.getElementById('assignModal'));
            document.getElementById('assignTicketId').value = ticketId;
            modal.show();
        },

        addComment(ticketId) {
            // Open comment modal
            const modal = new bootstrap.Modal(document.getElementById('commentModal'));
            document.getElementById('commentTicketId').value = ticketId;
            modal.show();
        },

        bulkAction(status, action) {
            // Open bulk action modal
            const modal = new bootstrap.Modal(document.getElementById('bulkActionModal'));
            document.getElementById('bulkActionStatus').value = status;
            document.getElementById('bulkActionType').value = action;
            modal.show();
        },

        setupAutoRefresh() {
            // Setup auto-refresh when page becomes visible
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden && this.autoRefresh) {
                    this.loadKanbanData();
                }
            });
        }
    };
}

// Drag and drop event listeners for better UX
document.addEventListener('DOMContentLoaded', function() {
    // Add drag over effects
    document.addEventListener('dragover', function(e) {
        e.preventDefault();
        const ticketsContainer = e.target.closest('.kanban-tickets');
        if (ticketsContainer) {
            ticketsContainer.classList.add('drag-over');
        }
    });

    document.addEventListener('dragleave', function(e) {
        const ticketsContainer = e.target.closest('.kanban-tickets');
        if (ticketsContainer && !ticketsContainer.contains(e.relatedTarget)) {
            ticketsContainer.classList.remove('drag-over');
        }
    });

    // Handle assign modal form submission
    const assignForm = document.getElementById('assignForm');
    if (assignForm) {
        assignForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const ticketId = formData.get('ticket_id');
            const assignedTo = formData.get('assigned_to');
            const comment = formData.get('comment');

            try {
                const response = await fetch(`/landlord/tickets/${ticketId}/assign`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        assigned_to: assignedTo,
                        comment: comment
                    })
                });

                if (response.ok) {
                    toastr.success(translate('ticket_assigned'));
                    bootstrap.Modal.getInstance(document.getElementById('assignModal')).hide();
                    // Refresh kanban board if Alpine component is available
                    if (window.Alpine && window.Alpine.store) {
                        location.reload(); // Simple refresh for now
                    }
                } else {
                    throw new Error('Failed to assign ticket');
                }
            } catch (error) {
                console.error('Error assigning ticket:', error);
                toastr.error(translate('error_assigning_ticket'));
            }
        });
    }

    // Handle comment modal form submission
    const commentForm = document.getElementById('commentForm');
    if (commentForm) {
        commentForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const ticketId = formData.get('ticket_id');
            const comment = formData.get('comment');

            try {
                const response = await fetch('/landlord/comments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        object_id: ticketId,
                        object_model: 'Modules\\Ticket\\Entities\\Ticket',
                        comment: comment
                    })
                });

                if (response.ok) {
                    toastr.success(translate('comment_added'));
                    bootstrap.Modal.getInstance(document.getElementById('commentModal')).hide();
                    document.getElementById('commentForm').reset();
                } else {
                    throw new Error('Failed to add comment');
                }
            } catch (error) {
                console.error('Error adding comment:', error);
                toastr.error(translate('error_adding_comment'));
            }
        });
    }
});

// Translation helper function
function translate(key) {
    const translations = {
        'refresh': 'Refresh',
        'filters': 'Filters',
        'pause_auto_refresh': 'Pause Auto Refresh',
        'auto_refresh': 'Auto Refresh',
        'all_users': 'All Users',
        'all_priorities': 'All Priorities',
        'urgent': 'Urgent',
        'high': 'High',
        'medium': 'Medium',
        'low': 'Low',
        'search': 'Search',
        'search_tickets': 'Search tickets...',
        'clear_filters': 'Clear Filters',
        'bulk_assign': 'Bulk Assign',
        'bulk_priority': 'Bulk Priority',
        'view': 'View',
        'edit': 'Edit',
        'assign': 'Assign',
        'add_comment': 'Add Comment',
        'overdue': 'Overdue',
        'created': 'Created',
        'due': 'Due',
        'no_tickets_in_status': 'No tickets in this status',
        'loading': 'Loading...',
        'error_loading_data': 'Error loading data',
        'board_refreshed': 'Board refreshed',
        'auto_refresh_enabled': 'Auto refresh enabled',
        'auto_refresh_disabled': 'Auto refresh disabled',
        'status_updated': 'Status updated successfully',
        'error_updating_status': 'Error updating status',
        'ticket_assigned': 'Ticket assigned successfully',
        'error_assigning_ticket': 'Error assigning ticket',
        'comment_added': 'Comment added successfully',
        'error_adding_comment': 'Error adding comment'
    };

    return translations[key] || key;
}
