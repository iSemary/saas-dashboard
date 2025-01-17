@extends('layouts.'.$layoutPrefix.'.app')
@section('content')
    <div class="card">
        <div class="card-body">
            notifications
        </div>
    </div>
@endsection
@section("scripts")
<script src="{{ asset("assets/global/js/notifications/index.js") }}"></script>
@endsection