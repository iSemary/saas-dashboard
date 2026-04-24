<form
    action="{{ isset($row) ? route('landlord.email-recipients.update', $row->id) : route('landlord.email-recipients.store') }}"
    class="{{ isset($row) ? 'edit-form' : 'create-form' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($row))
        @method('PUT')
    @endif
    <div class="form-group">
        <label for="email" class="form-label">@translate('email') <span class="text-danger">*</span></label>
        <input type="email" name="email" id="email" class="form-control"
            value="{{ isset($row) ? $row->email : '' }}" required>
    </div>

    <div class="form-group">
        <label for="status" class="form-label">@translate('status') <span class="text-danger">*</span></label>
        <select name="status" id="status" class="form-control select2" required>
            @foreach ($statusOptions as $status)
                <option value="{{ $status }}" {{ isset($row) && $row->status == $status ? 'selected' : '' }}>
                    @translate($status)
                </option>
            @endforeach
        </select>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>@translate('recipient_metadata')</h4>
        </div>
        <div class="card-body">
            <div class="meta-container">
                {{-- Meta values will be here --}}
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button type="button" class="btn btn-info add-meta">
                        <i class="fas fa-plus"></i> @translate('add_meta_field')
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit"
            class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">{{ isset($row) ? translate('update') : translate('create') }}</button>
    </div>
</form>

@include('landlord.emails.email-recipients.templates.meta-keys')

<script src="{{ asset('assets/landlord/js/emails/email-recipients/editor.js') }}"></script>

@if (isset($row) && $row->metas->count() > 0)
    <script>
        renderMetas(@json($row->metas));
    </script>
@endif
