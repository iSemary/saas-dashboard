<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ env('APP_NAME') }} | @translate('login')</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/icons/favicon.ico" />
    <link rel="stylesheet" href="{{ asset("assets/shared/plugins/bootstrap-5.3.3/css/bootstrap.min.css") }}">
    <link rel="stylesheet" href="{{ asset('assets/guest/css/style.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    @include("layouts.guest.header")
    @yield('content')

    @include("layouts.guest.footer")
    <script src="{{ asset('assets/guest/plugins/jquery/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset("assets/shared/plugins/bootstrap-5.3.3/js/bootstrap.bundle.min.js") }}"></script>
    <script src="{{ asset('assets/guest/js/main.js') }}"></script>
    @yield('js')
</body>

</html>
