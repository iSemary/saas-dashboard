{{-- Log Details --}}
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-6">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item p-1">
                        <strong>{{ translate('status') }}:</strong>
                        {{ translate($data->status) }}
                    </li>
                    <li class="list-group-item p-1">
                        <strong>{{ translate('opened_at') }}:</strong>
                        {{ $data->opened_at ?? '-' }}
                    </li>
                    <li class="list-group-item p-1">
                        <strong>{{ translate('clicked_at') }}:</strong>
                        {{ $data->clicked_at ?? '-' }}
                    </li>
                </ul>
            </div>
            <div class="col-6">
                <ul class="list-group list-group-flush">
                    @if (!empty($data->template_name))
                        <li class="list-group-item p-1"><strong>{{ translate('template') }}:</strong>
                            {{ $data->template_name }}
                        </li>
                    @endif
                    @if (!empty($data->campaign_name))
                        <li class="list-group-item p-1"><strong>{{ translate('campaign') }}:</strong>
                            {{ $data->campaign_name }}
                        </li>
                    @endif
                    <li class="list-group-item p-1">
                        <strong>{{ translate('sent_from') }}:</strong> {{ $data->email_from }}
                    </li>
                </ul>
            </div>
        </div>

    </div>
</div>

{{-- Email Details --}}
<div class="card">
    {{-- <div class="card-header">
        <div class="card-tools">
            <a href="#" class="btn btn-tool" title="Previous"><i class="fas fa-chevron-left"></i></a>
            <a href="#" class="btn btn-tool" title="Next"><i class="fas fa-chevron-right"></i></a>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-default btn-sm" data-container="body" title="Delete">
                <i class="far fa-trash-alt"></i>
            </button>
            <button type="button" class="btn btn-default btn-sm" data-container="body" title="Reply">
                <i class="fas fa-reply"></i>
            </button>
            <button type="button" class="btn btn-default btn-sm" data-container="body" title="Forward">
                <i class="fas fa-share"></i>
            </button>
            <button type="button" class="btn btn-default btn-sm" data-container="body" title="Print">
                <i class="fas fa-print"></i>
            </button>
        </div>
    </div> --}}
    <!-- /.card-header -->
    <div class="card-body p-0">
        <div class="mailbox-read-info">
            <h5>{{ $data->subject }}</h5>
            <h6>@translate('from'): {{ $data->email_from }}
                <span class="mailbox-read-time float-right">
                    {{ $data->created_at->diffForHumans() }} /
                    {{ $data->created_at }}
                </span>
            </h6>
        </div>
        <div class="mailbox-read-message">
            {!! $data->body !!}
        </div>
    </div>
    @if (count($data->attachments))
        <div class="card-footer bg-white">
            <ul class="mailbox-attachments d-flex align-items-stretch clearfix">
                @foreach ($data->attachments as $attachment)
                    <li style="background-color: #f8f9fa;">
                        <span class="mailbox-attachment-icon"><i class="far fa-file"></i></span>
                        <div class="mailbox-attachment-info">
                            <div class="mailbox-attachment-name"><i class="fas fa-paperclip"></i>
                                {{ $attachment->original_name }}
                            </div>
                            <span class="mailbox-attachment-size clearfix mt-1">
                                <span>{{ App\Helpers\FileHelper::returnSizeString($attachment->size) }}</span>
                            </span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- /.card-footer -->
    {{-- <div class="card-footer">
        <div class="float-right">
            <button type="button" class="btn btn-default"><i class="fas fa-reply"></i> Reply</button>
            <button type="button" class="btn btn-default"><i class="fas fa-share"></i> Forward</button>
        </div>
        <button type="button" class="btn btn-default"><i class="fas fa-print"></i> Print</button>
    </div> --}}
</div>
