/**
 * Ticket Detail JavaScript
 * Handles ticket actions, comments, and real-time updates
 */

function ticketDetail(ticketId) {
    return {
        ticketId: ticketId,
        showAddCommentForm: false,
        newComment: {
            comment: '',
            attachments: []
        },
        loading: false,

        init() {
            this.setupAutoRefresh();
            this.setupKeyboardShortcuts();
        },

        async addComment() {
            if (!this.newComment.comment.trim()) {
                toastr.warning(translate('comment_required'));
                return;
            }

            this.loading = true;
            
            try {
                const formData = new FormData();
                formData.append('object_id', this.ticketId);
                formData.append('object_model', 'Modules\\Ticket\\Entities\\Ticket');
                formData.append('comment', this.newComment.comment);
                
                // Add attachments if any
                const fileInput = this.$refs.commentAttachments;
                if (fileInput && fileInput.files.length > 0) {
                    Array.from(fileInput.files).forEach((file, index) => {
                        formData.append(`attachments[${index}]`, file);
                    });
                }

                const response = await fetch('/landlord/comments', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (response.ok) {
                    const data = await response.json();
                    toastr.success(translate('comment_added_successfully'));
                    
                    // Reset form
                    this.newComment.comment = '';
                    this.showAddCommentForm = false;
                    if (fileInput) fileInput.value = '';
                    
                    // Refresh comments section
                    await this.refreshComments();
                } else {
                    throw new Error('Failed to add comment');
                }
            } catch (error) {
                console.error('Error adding comment:', error);
                toastr.error(translate('error_adding_comment'));
            } finally {
                this.loading = false;
            }
        },

        async refreshComments() {
            try {
                const response = await fetch(`/landlord/comments/object/${this.ticketId}/Modules\\Ticket\\Entities\\Ticket`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    // Update comments section (you might want to implement a more sophisticated update)
                    location.reload();
                }
            } catch (error) {
                console.error('Error refreshing comments:', error);
            }
        },

        assignTicket() {
            $('#assignModal').modal('show');
            $('#assignTicketId').val(this.ticketId);
        },

        changeStatus() {
            $('#statusModal').modal('show');
            $('#statusTicketId').val(this.ticketId);
        },

        changePriority() {
            $('#priorityModal').modal('show');
            $('#priorityTicketId').val(this.ticketId);
        },

        async closeTicket() {
            const comment = prompt(translate('close_ticket_comment'));
            if (comment === null) return; // User cancelled
            
            try {
                const response = await fetch(`/landlord/tickets/${this.ticketId}/close`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        comment: comment
                    })
                });

                if (response.ok) {
                    toastr.success(translate('ticket_closed_successfully'));
                    location.reload();
                } else {
                    throw new Error('Failed to close ticket');
                }
            } catch (error) {
                console.error('Error closing ticket:', error);
                toastr.error(translate('error_closing_ticket'));
            }
        },

        async reopenTicket() {
            const comment = prompt(translate('reopen_ticket_comment'));
            if (comment === null) return; // User cancelled
            
            try {
                const response = await fetch(`/landlord/tickets/${this.ticketId}/reopen`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        comment: comment
                    })
                });

                if (response.ok) {
                    toastr.success(translate('ticket_reopened_successfully'));
                    location.reload();
                } else {
                    throw new Error('Failed to reopen ticket');
                }
            } catch (error) {
                console.error('Error reopening ticket:', error);
                toastr.error(translate('error_reopening_ticket'));
            }
        },

        printTicket() {
            window.print();
        },

        setupAutoRefresh() {
            // Auto-refresh every 5 minutes
            setInterval(() => {
                this.refreshComments();
            }, 300000);
        },

        setupKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                // Ctrl/Cmd + Enter to submit comment
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    if (this.showAddCommentForm && this.newComment.comment.trim()) {
                        this.addComment();
                    }
                }
                
                // Escape to close comment form
                if (e.key === 'Escape' && this.showAddCommentForm) {
                    this.showAddCommentForm = false;
                }
            });
        }
    };
}

// Form handlers for modals
$(document).ready(function() {
    // Assign form handler
    $('#assignForm').on('submit', async function(e) {
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
                toastr.success(translate('ticket_assigned_successfully'));
                $('#assignModal').modal('hide');
                location.reload();
            } else {
                throw new Error('Failed to assign ticket');
            }
        } catch (error) {
            console.error('Error assigning ticket:', error);
            toastr.error(translate('error_assigning_ticket'));
        }
    });

    // Status form handler
    $('#statusForm').on('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const ticketId = formData.get('ticket_id');
        const status = formData.get('status');
        const comment = formData.get('comment');

        try {
            const response = await fetch(`/landlord/tickets/${ticketId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: status,
                    comment: comment
                })
            });

            if (response.ok) {
                toastr.success(translate('status_updated_successfully'));
                $('#statusModal').modal('hide');
                location.reload();
            } else {
                throw new Error('Failed to update status');
            }
        } catch (error) {
            console.error('Error updating status:', error);
            toastr.error(translate('error_updating_status'));
        }
    });

    // Priority form handler
    $('#priorityForm').on('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const ticketId = formData.get('ticket_id');
        const priority = formData.get('priority');
        const comment = formData.get('comment');

        try {
            const response = await fetch(`/landlord/tickets/${ticketId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    priority: priority,
                    status_comment: comment
                })
            });

            if (response.ok) {
                toastr.success(translate('priority_updated_successfully'));
                $('#priorityModal').modal('hide');
                location.reload();
            } else {
                throw new Error('Failed to update priority');
            }
        } catch (error) {
            console.error('Error updating priority:', error);
            toastr.error(translate('error_updating_priority'));
        }
    });

    // Real-time notifications (if WebSocket is available)
    if (typeof Echo !== 'undefined') {
        Echo.private(`ticket.${ticketId}`)
            .listen('TicketUpdated', (e) => {
                toastr.info(translate('ticket_updated_by_another_user'));
                // Optionally refresh the page or specific sections
            })
            .listen('CommentAdded', (e) => {
                toastr.info(translate('new_comment_added'));
                // Refresh comments section
                location.reload();
            });
    }
});

// Translation helper
function translate(key) {
    const translations = {
        'comment_required': 'Comment is required',
        'comment_added_successfully': 'Comment added successfully',
        'error_adding_comment': 'Error adding comment',
        'close_ticket_comment': 'Enter a comment for closing this ticket (optional):',
        'reopen_ticket_comment': 'Enter a comment for reopening this ticket (optional):',
        'ticket_closed_successfully': 'Ticket closed successfully',
        'ticket_reopened_successfully': 'Ticket reopened successfully',
        'error_closing_ticket': 'Error closing ticket',
        'error_reopening_ticket': 'Error reopening ticket',
        'ticket_assigned_successfully': 'Ticket assigned successfully',
        'error_assigning_ticket': 'Error assigning ticket',
        'status_updated_successfully': 'Status updated successfully',
        'error_updating_status': 'Error updating status',
        'priority_updated_successfully': 'Priority updated successfully',
        'error_updating_priority': 'Error updating priority',
        'ticket_updated_by_another_user': 'This ticket was updated by another user',
        'new_comment_added': 'A new comment was added to this ticket'
    };
    
    return translations[key] || key;
}
