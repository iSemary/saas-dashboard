@extends('layouts.' . $layoutPrefix . '.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-4">
                    <h4 class="card-title">{{ $title }}</h4>
                </div>
                @php
                    $currentType = request()->get('type');
                    $isAllActivities = is_null($currentType);
                    $isDeletedActivities = $currentType === 'deleted';
                @endphp
                <div class="col-8 text-revert-only direction-ltr">
                    <a href="{{ $isAllActivities ? '#' : route('activity-logs.index') . '?page=1' }}"
                        class="{{ $isAllActivities ? 'text-muted' : 'text-primary' }}">
                        <i class="fas fa-list-ul"></i>
                        @translate('all_activities')
                    </a>
                    <span class="mx-2 text-muted">|</span>
                    <a href="{{ $isDeletedActivities ? '#' : route('activity-logs.index') . '?type=deleted&page=1' }}"
                        class="{{ $isDeletedActivities ? 'text-muted' : 'text-danger' }}">
                        <i class="fas fa-trash"></i>
                        @translate('recently_deleted')
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="activity-timeline">
                @if ($activities->count() > 0)
                    @foreach ($activities as $date => $modules)
                        <div class="date-group mb-4">
                            <h5 class="text-muted">{{ \Carbon\Carbon::parse($date)->format('F d, Y') }} /
                                {{ \Carbon\Carbon::parse($date)->diffForHumans() }}</h5>
                            @foreach ($modules as $moduleName => $moduleActivities)
                                <div class="module-group ml-4 mb-3">
                                    <h6 class="module-title">{{ translate($moduleName) }}</h6>

                                    <div class="timeline-items">
                                        @foreach ($moduleActivities as $activity)
                                            <div class="timeline-item d-flex mb-3">
                                                <div class="timeline-icon mr-3">
                                                    {!! App\Helpers\IconHelper::formatEventIcon($activity->event) !!}
                                                </div>
                                                <div class="timeline-content">
                                                    <div class="d-flex justify-content-between">
                                                        <h6 class="mb-1">
                                                            {{ translate($activity->event) }}
                                                            {{ translate($activity->auditable_type) }}
                                                        </h6>
                                                        <small class="text-muted">
                                                            {{ $activity->created_at->format('h:i A') }}
                                                        </small>
                                                    </div>

                                                    @if ($activity->event === 'updated')
                                                        <div class="changes-list">
                                                            @foreach (App\Helpers\AuditHelper::formatChanges($activity->old_values, $activity->new_values) as $change)
                                                                <div class="change-item">
                                                                    <small>
                                                                        {{ translate('changed') }} {{ $change['field'] }}
                                                                        {{ translate('from') }}
                                                                        <span
                                                                            class="text-danger">{{ $change['old'] }}</span>
                                                                        {{ translate('to') }}
                                                                        <span
                                                                            class="text-success">{{ $change['new'] }}</span>
                                                                    </small>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif


                                                    <div class="text-muted font-weight-bold">#{{ $activity->auditable_id }}</div>

                                                    <div class="meta-info mt-2">
                                                        <small class="text-muted">
                                                            <i class="fas fa-globe"></i> {{ $activity->ip_address }}
                                                            {!! App\Helpers\IconHelper::formatAgentIcons($activity->user_agent) !!}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                    {{-- pagination --}}
                    <div class="mt-4">
                        {!! $pagination->render('layouts.pagination.default') !!}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">{{ translate('no_activity_logs_found') }}</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
