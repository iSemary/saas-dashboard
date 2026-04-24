<form action="{{ isset($row) ? route('landlord.payment-methods.update', $row->id) : route('landlord.payment-methods.store') }}"
    class="{{ isset($row) ? 'edit-form' : 'create-form' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($row))
        @method('PUT')
    @endif

    <div class="form-group">
        <label for="name" class="form-label">@translate('name') <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control"
            value="{{ isset($row) ? $row->name : '' }}" required>
    </div>

    <div class="form-group">
        <label for="description" class="form-label">@translate('description')</label>
        <textarea name="description" id="description" class="form-control">{{ isset($row) ? $row->description : '' }}</textarea>
    </div>

    <div class="form-group">
        <label for="processor_type" class="form-label">@translate('processor_type') <span class="text-danger">*</span></label>
        <select name="processor_type" id="processor_type" class="form-control select2" required>
            <option value="">@translate('select_processor_type')</option>
            @foreach ($processorTypes as $type)
                <option value="{{ $type }}"
                    {{ isset($row) && $row->processor_type == $type ? 'selected' : '' }}>
                    {{ ucfirst($type) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="gateway_name" class="form-label">@translate('gateway_name') <span class="text-danger">*</span></label>
        <input type="text" name="gateway_name" id="gateway_name" class="form-control"
            value="{{ isset($row) ? $row->gateway_name : '' }}" required>
    </div>

    <div class="form-group">
        <label for="supported_currencies" class="form-label">@translate('supported_currencies') <span class="text-danger">*</span></label>
        <select name="supported_currencies[]" id="supported_currencies" class="form-control select2" multiple required>
            @foreach ($currencies as $currency)
                <option value="{{ $currency->code }}"
                    {{ isset($row) && in_array($currency->code, $row->supported_currencies ?? []) ? 'selected' : '' }}>
                    {{ $currency->code }} - {{ $currency->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="country_codes" class="form-label">@translate('country_codes')</label>
        <input type="text" name="country_codes" id="country_codes" class="form-control"
            value="{{ isset($row) ? implode(',', $row->country_codes ?? []) : '' }}"
            placeholder="US,CA,GB (comma separated)">
        <small class="form-text text-muted">@translate('leave_empty_for_global')</small>
    </div>

    <div class="form-group">
        <label for="authentication_type" class="form-label">@translate('authentication_type')</label>
        <select name="authentication_type" id="authentication_type" class="form-control select2">
            <option value="">@translate('select_authentication_type')</option>
            @foreach ($authenticationTypes as $type)
                <option value="{{ $type }}"
                    {{ isset($row) && $row->authentication_type == $type ? 'selected' : '' }}>
                    @translate($type)
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <div class="form-check">
            <input type="checkbox" name="is_global" id="is_global" class="form-check-input" value="1"
                {{ isset($row) && $row->is_global ? 'checked' : '' }}>
            <label for="is_global" class="form-check-label">@translate('is_global')</label>
        </div>
    </div>

    <div class="form-group">
        <label for="status" class="form-label">@translate('status')</label>
        <select name="status" id="status" class="form-control select2">
            @foreach ($statusOptions as $status)
                <option value="{{ $status }}" {{ isset($row) && $row->status == $status ? 'selected' : '' }}>
                    @translate($status)
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit"
            class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">{{ isset($row) ? translate('update') : translate('create') }}</button>
    </div>
</form>
