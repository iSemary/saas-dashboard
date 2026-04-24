@if(!empty($breadcrumbs))
    {{-- Content Header (Page header) --}}
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $title ?? trans('dashboard') }}</h1>
                </div>
                {{-- /.col --}}
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        @foreach($breadcrumbs as $breadcrumb)
                            @if($loop->last)
                                <li class="breadcrumb-item active">{{ $breadcrumb['text'] ?? $breadcrumb['title'] ?? '' }}</li>
                            @else
                                <li class="breadcrumb-item {{ isset($breadcrumb['link']) || isset($breadcrumb['url']) ? '' : 'active' }}">
                                    @if(isset($breadcrumb['link']) || isset($breadcrumb['url']))
                                        <a href="{{ $breadcrumb['link'] ?? $breadcrumb['url'] }}">{{ $breadcrumb['text'] ?? $breadcrumb['title'] ?? '' }}</a>
                                    @else
                                        {{ $breadcrumb['text'] ?? $breadcrumb['title'] ?? '' }}
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    </ol>
                </div>
                {{-- /.col --}}
            </div>
            {{-- /.row --}}
        </div>
        {{-- /.container-fluid --}}
    </div>
    {{-- /.content-header --}}
@endif
