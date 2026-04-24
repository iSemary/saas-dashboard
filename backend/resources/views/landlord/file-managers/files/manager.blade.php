@extends('layouts.landlord.app')
@section('styles')
    <link rel="stylesheet" href="{{ asset('vendor/file-manager/css/file-manager.css') }}">
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div id="fm" style="height: 600px;"></div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('vendor/file-manager/js/file-manager.js') }}"></script>
@endsection
