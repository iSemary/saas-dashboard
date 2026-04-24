<!-- Add Comment Modal -->
<div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentModalLabel">@translate('add_comment')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="commentForm">
                <div class="modal-body">
                    <input type="hidden" id="commentTicketId" name="ticket_id">
                    
                    <div class="mb-3">
                        <label for="commentText" class="form-label">@translate('comment') <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="commentText" name="comment" rows="5" 
                                  placeholder="@translate('enter_your_comment')" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="commentAttachments" class="form-label">@translate('attachments') (@translate('optional'))</label>
                        <input type="file" class="form-control" id="commentAttachments" name="attachments[]" multiple
                               accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt">
                        <div class="form-text">@translate('max_file_size_10mb')</div>
                    </div>

                    <!-- File Preview Area -->
                    <div id="filePreview" class="mb-3" style="display: none;">
                        <label class="form-label">@translate('selected_files')</label>
                        <div id="fileList" class="border rounded p-2 bg-light"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@translate('cancel')</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-comment"></i> @translate('add_comment')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// File preview functionality
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('commentAttachments');
    const filePreview = document.getElementById('filePreview');
    const fileList = document.getElementById('fileList');

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            
            if (files.length > 0) {
                filePreview.style.display = 'block';
                fileList.innerHTML = '';
                
                files.forEach((file, index) => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'd-flex justify-content-between align-items-center mb-1';
                    fileItem.innerHTML = `
                        <span>
                            <i class="fas fa-file"></i> ${file.name} 
                            <small class="text-muted">(${formatFileSize(file.size)})</small>
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    fileList.appendChild(fileItem);
                });
            } else {
                filePreview.style.display = 'none';
            }
        });
    }
});

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function removeFile(index) {
    const fileInput = document.getElementById('commentAttachments');
    const dt = new DataTransfer();
    const files = Array.from(fileInput.files);
    
    files.forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    fileInput.files = dt.files;
    fileInput.dispatchEvent(new Event('change'));
}
</script>
