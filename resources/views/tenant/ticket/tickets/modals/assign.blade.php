<!-- Assign Ticket Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignModalLabel">@translate('assign_ticket')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignForm">
                <div class="modal-body">
                    <input type="hidden" id="assignTicketId" name="ticket_id">
                    
                    <div class="mb-3">
                        <label for="assignedTo" class="form-label">@translate('assign_to') <span class="text-danger">*</span></label>
                        <select class="form-select" id="assignedTo" name="assigned_to" required>
                            <option value="">@translate('select_user')</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="assignComment" class="form-label">@translate('comment') (@translate('optional'))</label>
                        <textarea class="form-control" id="assignComment" name="comment" rows="3" 
                                  placeholder="@translate('add_assignment_comment')"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@translate('cancel')</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> @translate('assign_ticket')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


