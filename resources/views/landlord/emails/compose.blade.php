<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">@translate('compose_new_message')</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <input class="form-control" placeholder="@translate('to'):">
        </div>
        <div class="form-group">
            <input class="form-control" placeholder="@translate('subject'):">
        </div>
        <div class="form-group">
            <textarea id="ckInput" class="form-control ckeditor"></textarea>
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
</div>
