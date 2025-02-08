<form
    action="{{ isset($row) ? route('landlord.email-credentials.update', $row->id) : route('landlord.email-credentials.store') }}"
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

    <div class="row">
        <div class="col-6 form-group">
            <label for="name" class="form-label">@translate('from_name') <span class="text-danger">*</span></label>
            <input type="text" name="from_name" id="fromName" class="form-control"
                value="{{ isset($row) ? $row->from_name : '' }}" required>
        </div>

        <div class="col-6 form-group">
            <label for="name" class="form-label">@translate('from_address') <span class="text-danger">*</span></label>
            <input type="email" name="from_address" id="fromAddress" class="form-control"
                value="{{ isset($row) ? $row->from_address : '' }}" required>
        </div>

        <div class="col-6 form-group">
            <label for="name" class="form-label">@translate('mailer') <span class="text-danger">*</span></label>
            <input type="text" name="mailer" id="mailer" class="form-control"
                value="{{ isset($row) ? $row->mailer : '' }}" required>
        </div>

        <div class="col-6 form-group">
            <label for="name" class="form-label">@translate('host') <span class="text-danger">*</span></label>
            <input type="text" name="host" id="host" class="form-control"
                value="{{ isset($row) ? $row->host : '' }}" required>
        </div>

        <div class="col-6 form-group">
            <label for="name" class="form-label">@translate('port') <span class="text-danger">*</span></label>
            <input type="number" name="port" id="port" class="form-control"
                value="{{ isset($row) ? $row->port : '' }}" required>
        </div>

        <div class="col-6 form-group">
            <label for="name" class="form-label">@translate('username') <span class="text-danger">*</span></label>
            <input type="text" name="username" id="username" class="form-control"
                value="{{ isset($row) ? $row->username : '' }}" required>
        </div>

        <div class="col-6 form-group">
            <label for="name" class="form-label">@translate('password')
                @if (!isset($row))
                    <span class="text-danger">*</span>
                @endif
            </label>
            <input type="password" name="password" id="password" class="form-control" value=""
                {{ isset($row) ? '' : 'required' }}>
                <small class="text-success"><i class="fas fa-lock"></i> @translate('email_credentials.all_passwords_are_encrypted')</small>
        </div>

        <div class="col-6 form-group">
            <label for="encryption" class="form-label">@translate('encryption') <span class="text-danger">*</span></label>
            <select name="encryption" id="encryption" class="form-control select2 w-100" required>
                @foreach ($encryptionOptions as $encryptionOption)
                    <option value="{{ $encryptionOption }}"
                        {{ isset($row) && $row->encryption == $encryptionOption ? 'selected' : '' }}>
                        @translate($encryptionOption)
                    </option>
                @endforeach
            </select>
        </div>
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

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit"
            class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">{{ isset($row) ? translate('update') : translate('create') }}</button>
    </div>
</form>
