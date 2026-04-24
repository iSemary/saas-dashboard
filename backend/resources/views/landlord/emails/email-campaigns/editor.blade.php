<form
    action="{{ isset($row) ? route('landlord.email-campaigns.update', $row->id) : route('landlord.email-campaigns.store') }}"
    class="{{ isset($row) ? 'edit-form' : 'create-form' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($row))
        @method('PUT')
    @endif
    <div class="row">
        <div class="form-group col-12">
            <label for="name" class="form-label">@translate('name') <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control"
                value="{{ isset($row) ? $row->name : '' }}" required>
        </div>

        {{-- Email Template --}}
        <div class="form-group col-xl-6 col-md-12 col-sm-12">
            <label for="email_template_id" class="form-label">@translate('email_template') <span
                    class="text-danger">*</span></label>
            <select name="email_template_id" id="emailTemplateId" class="form-control select2 select-email-template"
                required>
                <option value="">@translate('select')</option>
                @foreach ($emailTemplates as $emailTemplate)
                    <option data-route="{{ route('landlord.email-templates.show', $emailTemplate->id) }}"
                        value="{{ $emailTemplate->id }}">
                        @translate($emailTemplate->name)
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Email Credential --}}
        <div class="form-group col-xl-6 col-md-12 col-sm-12">
            <label for="email_credential_id" class="form-label">@translate('from'): <span
                    class="text-danger">*</span></label>
            <select name="email_credential_id" id="email_credential_id" class="form-control select2" required>
                <option value="">@translate('select')</option>
                @foreach ($emailCredentials as $emailCredential)
                    <option value="{{ $emailCredential->id }}">
                        {{ $emailCredential->from_address }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Select Recipients Type --}}
        <div class="form-group col-12">
            <label for="recipients_type" class="form-label">@translate('recipients_type') <span class="text-danger">*</span></label>
            <select name="recipients_type" id="recipientsType"
                data-excel-sample="{{ asset('assets/shared/samples/excel/emails.xlsx') }}"
                data-recipients-route="{{ route('landlord.email-recipients.list') }}"
                data-groups-route="{{ route('landlord.email-groups.list') }}"
                data-all-users-route="{{ route('landlord.emails.users.all') }}"
                class="form-control select2 select-recipients-type" required>
                @foreach ($emailTypes as $key => $value)
                    <option value="{{ $value }}"
                        {{ $value === \App\Constants\EmailType::SINGLE ? 'selected' : '' }}>
                        @translate($value)
                    </option>
                @endforeach
            </select>
        </div>

        <hr />
        {{-- Recipients --}}
        <div class="form-group col-12 email-to-container">
            <input class="form-control" placeholder="@translate('to'):" type="email" name="email" id="email"
                required />
        </div>

        <hr />

        {{-- Subject --}}
        <div class="form-group col-12">
            <input class="form-control email-subject" placeholder="@translate('subject'):" type="text" name="subject"
                id="subject" required>
        </div>

        {{-- Body --}}
        <div class="form-group col-12">
            <textarea id="ckInput" class="form-control ckeditor email-body" name="body" required></textarea>
        </div>

        {{-- Attachment --}}
        <div class="form-group col-12">
            @translate('attachments')
            <div class="file-uploader" data-multiple="true" data-required="false" data-max-file-size="1024"
                data-allowed-files="png,jpg,pdf,xlsx" data-label="Drag & Drop Files Here"
                data-button-label="Browse Files"></div>
            <p class="help-block">@translate('max'): @configuration('max_email_file_size') @translate('sizes.mb')</p>
        </div>

        {{-- Status --}}
        <div class="form-group col-12">
            <label for="status" class="form-label">@translate('status')</label>
            <select name="status" id="status" class="form-control select2">
                @foreach ($statusOptions as $status)
                    <option value="{{ $status }}"
                        {{ isset($row) && $row->status == $status ? 'selected' : '' }}>
                        @translate($status)
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-12">
            <label for="scheduled_at" class="form-label">@translate('scheduled_at')</label>
            <input type="datetime-local" name="scheduled_at" id="scheduled_at" class="form-control"
                value="{{ isset($row) ? $row->scheduled_at : '' }}">
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

<script src="{{ asset("assets/landlord/js/emails/compose.js") }}"></script>