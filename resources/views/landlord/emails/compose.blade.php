<div class="card card-primary card-outline">
    <form action="{{ route('landlord.emails.send') }}" class="{{ 'create-form' }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            {{-- Email Template --}}
            <div class="form-group">
                <label for="email_template_id" class="form-label">@translate('email_template')</label>
                <select name="email_template_id" id="emailTemplateId" class="form-control select2 select-email-template">
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
            <div class="form-group">
                <label for="email_credential_id" class="form-label">@translate('from'):</label>
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
            <div class="form-group">
                <label for="recipients_type" class="form-label">@translate('recipients_type')</label>
                <select name="recipients_type" id="recipientsType"
                    data-excel-sample="{{ asset('assets/shared/samples/excel/emails.xlsx') }}"
                    data-recipients-route="{{ route('landlord.email-recipients.list') }}"
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
            <div class="form-group email-to-container">
                <input class="form-control" placeholder="@translate('to'):" type="email" name="email"
                    id="email" required />
            </div>

            <hr />

            {{-- Subject --}}
            <div class="form-group">
                <input class="form-control email-subject" placeholder="@translate('subject'):" type="text" name="subject"
                    id="subject" required>
            </div>

            {{-- Body --}}
            <div class="form-group">
                <textarea id="ckInput" class="form-control ckeditor email-body" name="body" required></textarea>
            </div>

            {{-- Attachment --}}
            <div class="form-group">
                @translate('attachments')
                <div class="file-uploader" data-multiple="true" data-required="false" data-max-file-size="1024"
                    data-allowed-files="png,jpg,pdf,xlsx" data-label="Drag & Drop Files Here"
                    data-button-label="Browse Files"></div>
                <p class="help-block">@translate('max'): @configuration('max_email_file_size') @translate('sizes.mb')</p>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">

                <div class="form-group col-8">
                    <div class="form-status"></div>
                </div>
                <div class="form-group col-4 text-revert">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i>
                        @translate('send')</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="{{ asset('assets/shared/plugins/xlsx/xlsx.full.min.js') }}"></script>
<script src="{{ asset('assets/landlord/js/emails/compose.js') }}"></script>
