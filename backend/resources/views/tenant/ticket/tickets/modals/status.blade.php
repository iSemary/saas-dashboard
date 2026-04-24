<!-- Change Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">@translate('change_status')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="statusForm">
                <div class="modal-body">
                    <input type="hidden" id="statusTicketId" name="ticket_id">
                    
                    <div class="mb-3">
                        <label for="newStatus" class="form-label">@translate('new_status') <span class="text-danger">*</span></label>
                        <select class="form-select" id="newStatus" name="status" required>
                            <option value="">@translate('select_status')</option>
                            @foreach($statusOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="statusComment" class="form-label">@translate('comment') (@translate('optional'))</label>
                        <textarea class="form-control" id="statusComment" name="comment" rows="3" 
                                  placeholder="@translate('add_status_change_comment')"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@translate('cancel')</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-tasks"></i> @translate('change_status')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


