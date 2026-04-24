<!-- Bulk Assign Modal -->
<div class="modal fade" id="bulkAssignModal" tabindex="-1" aria-labelledby="bulkAssignModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkAssignModalLabel">@translate('bulk_assign')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bulkAssignForm">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        @translate('bulk_assign_description')
                    </div>

                    <div class="mb-3">
                        <label for="bulkAssignTo" class="form-label">@translate('assign_to') <span class="text-danger">*</span></label>
                        <select class="form-select" id="bulkAssignTo" name="assigned_to" required>
                            <option value="">@translate('select_user')</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="bulkAssignComment" class="form-label">@translate('comment') (@translate('optional'))</label>
                        <textarea class="form-control" id="bulkAssignComment" name="comment" rows="3" 
                                  placeholder="@translate('add_bulk_assignment_comment')"></textarea>
                    </div>

                    <!-- Confirmation -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="bulkAssignConfirm" required>
                        <label class="form-check-label" for="bulkAssignConfirm">
                            @translate('confirm_bulk_assignment')
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@translate('cancel')</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> @translate('bulk_assign')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


