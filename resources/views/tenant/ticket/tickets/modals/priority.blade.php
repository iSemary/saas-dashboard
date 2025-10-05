<!-- Change Priority Modal -->
<div class="modal fade" id="priorityModal" tabindex="-1" aria-labelledby="priorityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="priorityModalLabel">@translate('change_priority')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="priorityForm">
                <div class="modal-body">
                    <input type="hidden" id="priorityTicketId" name="ticket_id">
                    
                    <div class="mb-3">
                        <label for="newPriority" class="form-label">@translate('new_priority') <span class="text-danger">*</span></label>
                        <select class="form-select" id="newPriority" name="priority" required>
                            <option value="">@translate('select_priority')</option>
                            @foreach($priorityOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="priorityComment" class="form-label">@translate('comment') (@translate('optional'))</label>
                        <textarea class="form-control" id="priorityComment" name="comment" rows="3" 
                                  placeholder="@translate('add_priority_change_comment')"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@translate('cancel')</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-flag"></i> @translate('change_priority')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


