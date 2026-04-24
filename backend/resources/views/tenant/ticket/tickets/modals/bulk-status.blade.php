<!-- Bulk Status Update Modal -->
<div class="modal fade" id="bulkStatusModal" tabindex="-1" aria-labelledby="bulkStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkStatusModalLabel">@translate('bulk_status_update')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bulkStatusForm">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        @translate('bulk_status_description')
                    </div>

                    <div class="mb-3">
                        <label for="bulkNewStatus" class="form-label">@translate('new_status') <span class="text-danger">*</span></label>
                        <select class="form-select" id="bulkNewStatus" name="status" required>
                            <option value="">@translate('select_status')</option>
                            @foreach($statusOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="bulkStatusComment" class="form-label">@translate('comment') (@translate('optional'))</label>
                        <textarea class="form-control" id="bulkStatusComment" name="comment" rows="3" 
                                  placeholder="@translate('add_bulk_status_comment')"></textarea>
                    </div>

                    <!-- Confirmation -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="bulkStatusConfirm" required>
                        <label class="form-check-label" for="bulkStatusConfirm">
                            @translate('confirm_bulk_status_update')
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@translate('cancel')</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-tasks"></i> @translate('bulk_status_update')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


