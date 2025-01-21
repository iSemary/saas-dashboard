<div class="card card-primary card-outline">
    <form action="{{ route('landlord.emails.send') }}" class="{{ 'create-form' }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            {{-- TODO on select email template, call ajax, get subject and body, put it in the inputs --}}
            <div class="form-group">
                <label for="email_template_id" class="form-label">@translate('email_template')</label>
                <select name="email_template_id" id="email_template_id" class="form-control select2">
                    <option value="">@translate('select')</option>
                    @foreach ($emailTemplates as $emailTemplate)
                        <option value="{{ $emailTemplate->id }}">
                            @translate($emailTemplate->name)
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="email_credential_id" class="form-label">@translate('from'):</label>
                <select name="email_credential_id" id="email_credential_id" class="form-control select2">
                    <option value="">@translate('select')</option>
                    @foreach ($emailCredentials as $emailCredential)
                        <option value="{{ $emailCredential->id }}">
                            {{ $emailCredential->from_address }}
                        </option>
                    @endforeach
                </select>
            </div>
            {{-- TODO Make this lists or input or multiple or upload excel --}}
            <div class="form-group">
                <input class="form-control" placeholder="@translate('to'):" type="email" name="email" id="email" />
            </div>

            <hr />
            <div class="form-group">
                <input class="form-control email-subject" placeholder="@translate('subject'):" type="text" name="subject" id="subject" required>
            </div>
            <div class="form-group">
                <textarea id="ckInput" class="form-control ckeditor email-body" name="body" required></textarea>
            </div>
            <div class="form-group">
                <div class="btn btn-default btn-file">
                    <i class="fas fa-paperclip"></i> @translate('attachment')
                    <input type="file" name="attachment">
                </div>
                <p class="help-block">@translate('max'): @configuration('max_email_file_size') @translate('sizes.mb')</p>
            </div>
        </div>
        <div class="card-footer">
            <div class="float-right">
                <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> @translate('send')</button>
            </div>
        </div>
    </form>
</div>
