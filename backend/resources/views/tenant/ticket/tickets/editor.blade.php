<form action="{{ isset($row) ? route('tenant.tickets.update', $row->id) : route('tenant.tickets.store') }}"
    class="{{ isset($row) ? 'edit-form' : 'create-form' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($row))
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-md-8">
            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">@translate('basic_information')</h6>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="title" class="form-label">@translate('title') <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control"
                            value="{{ isset($row) ? $row->title : '' }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="description" class="form-label">@translate('description') <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" class="form-control" rows="4" required>{{ isset($row) ? $row->description : '' }}</textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="html_content" class="form-label">@translate('rich_content')</label>
                        <textarea name="html_content" id="html_content" class="form-control rich-editor" rows="6">{{ isset($row) ? $row->html_content : '' }}</textarea>
                        <div class="form-text">@translate('optional_rich_text_content')</div>
                    </div>
                </div>
            </div>

            <!-- Tags -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">@translate('tags')</h6>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="tags" class="form-label">@translate('tags')</label>
                        <input type="text" name="tags_input" id="tags_input" class="form-control" 
                               placeholder="@translate('enter_tags_separated_by_commas')"
                               value="{{ isset($row) && $row->tags ? implode(', ', $row->tags) : '' }}">
                        <input type="hidden" name="tags" id="tags" value="{{ isset($row) && $row->tags ? json_encode($row->tags) : '[]' }}">
                        <div class="form-text">@translate('tags_help_text')</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Ticket Properties -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">@translate('ticket_properties')</h6>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="status" class="form-label">@translate('status') <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-control" required>
                            @foreach($statusOptions as $key => $label)
                                <option value="{{ $key }}" {{ isset($row) && $row->status == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="priority" class="form-label">@translate('priority') <span class="text-danger">*</span></label>
                        <select name="priority" id="priority" class="form-control" required>
                            @foreach($priorityOptions as $key => $label)
                                <option value="{{ $key }}" {{ isset($row) && $row->priority == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="assigned_to" class="form-label">@translate('assigned_to')</label>
                        <select name="assigned_to" id="assigned_to" class="form-control">
                            <option value="">@translate('unassigned')</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ isset($row) && $row->assigned_to == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="brand_id" class="form-label">@translate('brand')</label>
                        <select name="brand_id" id="brand_id" class="form-control">
                            <option value="">@translate('no_brand')</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ isset($row) && $row->brand_id == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="due_date" class="form-label">@translate('due_date')</label>
                        <input type="datetime-local" name="due_date" id="due_date" class="form-control"
                            value="{{ isset($row) && $row->due_date ? $row->due_date->format('Y-m-d\TH:i') : '' }}">
                    </div>
                </div>
            </div>

            <!-- Status Change Comment (for edit mode) -->
            @if(isset($row))
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">@translate('status_change_comment')</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="status_comment" class="form-label">@translate('comment')</label>
                            <textarea name="status_comment" id="status_comment" class="form-control" rows="3" 
                                      placeholder="@translate('optional_comment_for_status_change')"></textarea>
                            <div class="form-text">@translate('status_comment_help')</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="form-group">
        <div class="form-status"></div>
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-secondary me-2" onclick="closeModal()">
                @translate('cancel')
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> 
                @if(isset($row))
                    @translate('update')
                @else
                    @translate('create')
                @endif
            </button>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tags handling
    const tagsInput = document.getElementById('tags_input');
    const tagsHidden = document.getElementById('tags');
    
    function updateTags() {
        const tags = tagsInput.value.split(',').map(tag => tag.trim()).filter(tag => tag.length > 0);
        tagsHidden.value = JSON.stringify(tags);
    }
    
    tagsInput.addEventListener('input', updateTags);
    updateTags(); // Initialize

    // Rich text editor initialization (if available)
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '.rich-editor',
            height: 300,
            menubar: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; }'
        });
    }
});
</script>


