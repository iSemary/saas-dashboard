<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ env('APP_NAME') }} | @lang('login')</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/icons/favicon.ico" />
    <link rel="stylesheet" href="{{ asset('assets/guest/css/style.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    @yield('content')
    <script src="{{ asset('assets/guest/plugins/jquery/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/guest/js/main.js') }}"></script>
    @yield('js')
</body>

</html>
