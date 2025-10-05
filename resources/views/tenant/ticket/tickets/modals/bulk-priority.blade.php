<!-- Bulk Priority Update Modal -->
<div class="modal fade" id="bulkPriorityModal" tabindex="-1" aria-labelledby="bulkPriorityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkPriorityModalLabel">@translate('bulk_priority_update')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bulkPriorityForm">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        @translate('bulk_priority_description')
                    </div>

                    <div class="mb-3">
                        <label for="bulkNewPriority" class="form-label">@translate('new_priority') <span class="text-danger">*</span></label>
                        <select class="form-select" id="bulkNewPriority" name="priority" required>
                            <option value="">@translate('select_priority')</option>
                            @foreach($priorityOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="bulkPriorityComment" class="form-label">@translate('comment') (@translate('optional'))</label>
                        <textarea class="form-control" id="bulkPriorityComment" name="comment" rows="3" 
                                  placeholder="@translate('add_bulk_priority_comment')"></textarea>
                    </div>

                    <!-- Confirmation -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="bulkPriorityConfirm" required>
                        <label class="form-check-label" for="bulkPriorityConfirm">
                            @translate('confirm_bulk_priority_update')
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@translate('cancel')</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-flag"></i> @translate('bulk_priority_update')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


