@extends('layouts.'.$layoutPrefix.'.app')
@section('content')
    <div class="card">
        <div class="card-body">
            {{ $title }}
        </div>
        <div class="card-body">
            
        </div>
    </div>
@endsection
@section("scripts")
<script src="{{ asset('assets/global/js/auth/activity-logs/index.js') }}"></script>
@endsection

