<!-- Bulk Action Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1" aria-labelledby="bulkActionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionModalLabel">@translate('bulk_action')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bulkActionForm">
                <div class="modal-body">
                    <input type="hidden" id="bulkActionStatus" name="status">
                    <input type="hidden" id="bulkActionType" name="action_type">
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <span id="bulkActionDescription">@translate('select_action_description')</span>
                    </div>

                    <!-- Assign Action -->
                    <div id="bulkAssignSection" style="display: none;">
                        <div class="mb-3">
                            <label for="bulkAssignTo" class="form-label">@translate('assign_to') <span class="text-danger">*</span></label>
                            <select class="form-select" id="bulkAssignTo" name="assigned_to">
                                <option value="">@translate('select_user')</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Priority Action -->
                    <div id="bulkPrioritySection" style="display: none;">
                        <div class="mb-3">
                            <label for="bulkPriority" class="form-label">@translate('priority') <span class="text-danger">*</span></label>
                            <select class="form-select" id="bulkPriority" name="priority">
                                <option value="">@translate('select_priority')</option>
                                <option value="urgent">@translate('urgent')</option>
                                <option value="high">@translate('high')</option>
                                <option value="medium">@translate('medium')</option>
                                <option value="low">@translate('low')</option>
                            </select>
                        </div>
                    </div>

                    <!-- Status Action -->
                    <div id="bulkStatusSection" style="display: none;">
                        <div class="mb-3">
                            <label for="bulkStatus" class="form-label">@translate('new_status') <span class="text-danger">*</span></label>
                            <select class="form-select" id="bulkStatus" name="new_status">
                                <option value="">@translate('select_status')</option>
                                @foreach($statusOptions as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="bulkComment" class="form-label">@translate('comment') (@translate('optional'))</label>
                        <textarea class="form-control" id="bulkComment" name="comment" rows="3" 
                                  placeholder="@translate('add_bulk_action_comment')"></textarea>
                    </div>

                    <!-- Confirmation -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="bulkConfirm" required>
                        <label class="form-check-label" for="bulkConfirm">
                            @translate('confirm_bulk_action')
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@translate('cancel')</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-tasks"></i> @translate('apply_bulk_action')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bulkActionModal = document.getElementById('bulkActionModal');
    const bulkActionForm = document.getElementById('bulkActionForm');
    
    if (bulkActionModal) {
        bulkActionModal.addEventListener('show.bs.modal', function(e) {
            const actionType = document.getElementById('bulkActionType').value;
            const status = document.getElementById('bulkActionStatus').value;
            
            // Hide all sections first
            document.getElementById('bulkAssignSection').style.display = 'none';
            document.getElementById('bulkPrioritySection').style.display = 'none';
            document.getElementById('bulkStatusSection').style.display = 'none';
            
            // Show relevant section and update description
            const description = document.getElementById('bulkActionDescription');
            
            switch(actionType) {
                case 'assign':
                    document.getElementById('bulkAssignSection').style.display = 'block';
                    description.textContent = `@translate('bulk_assign_description') ${status} @translate('status')`;
                    break;
                case 'priority':
                    document.getElementById('bulkPrioritySection').style.display = 'block';
                    description.textContent = `@translate('bulk_priority_description') ${status} @translate('status')`;
                    break;
                case 'status':
                    document.getElementById('bulkStatusSection').style.display = 'block';
                    description.textContent = `@translate('bulk_status_description') ${status} @translate('status')`;
                    break;
            }
        });
    }

    if (bulkActionForm) {
        bulkActionForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const actionType = formData.get('action_type');
            const status = formData.get('status');
            
            // Get all ticket IDs from the current status column
            const ticketIds = Array.from(document.querySelectorAll(`[data-status="${status}"] .ticket-card`))
                .map(card => card.querySelector('[data-ticket-id]')?.dataset.ticketId)
                .filter(id => id);
            
            if (ticketIds.length === 0) {
                toastr.warning('@translate('no_tickets_found')');
                return;
            }

            try {
                const requestData = {
                    ticket_ids: ticketIds,
                    action: actionType,
                    comment: formData.get('comment')
                };

                switch(actionType) {
                    case 'assign':
                        requestData.value = formData.get('assigned_to');
                        break;
                    case 'priority':
                        requestData.value = formData.get('priority');
                        break;
                    case 'status':
                        requestData.value = formData.get('new_status');
                        break;
                }

                const response = await fetch('/landlord/tickets/bulk-update', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                });

                if (response.ok) {
                    const data = await response.json();
                    toastr.success(`@translate('bulk_action_completed'): ${data.data.updated_count} @translate('tickets_updated')`);
                    bootstrap.Modal.getInstance(bulkActionModal).hide();
                    location.reload(); // Refresh the kanban board
                } else {
                    throw new Error('Failed to perform bulk action');
                }
            } catch (error) {
                console.error('Error performing bulk action:', error);
                toastr.error('@translate('error_bulk_action')');
            }
        });
    }
});
</script>
