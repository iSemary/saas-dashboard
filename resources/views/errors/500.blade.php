@extends('layouts.errors.app')
@section('content')
    <div class="notfound-container">
        <div class="notfound">
            <div>
                <div class="notfound-404">
                    <h1>!</h1>
                </div>
                <h2>@translate('error_pages.error')<br>{{  $exception->getStatusCode() }}</h2>
            </div>
            <p>{{ $exception->getMessage() !==null ? $exception->getMessage() : translate('error_pages.default_message') }}
                <a href="/">@translate('error_pages.back_to_homepage')</a>
            </p>
        </div>
    </div>
@endsection
