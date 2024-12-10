<!doctype html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ env("APP_NAME") }} | @yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.ico') }}" />
    <meta name="_token" content="{{ csrf_token() }}">
    {{-- <meta name="store_currency" content="{{ json_encode(auth()->user()->getCurrency()) }}"> --}}
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="{{ asset('assets/landlord/plugins/fontawesome-free/css/all.min.css') }}" media="screen">
    {{-- Bootstrap --}}
    <link rel="stylesheet" href="{{ asset('assets/landlord/css/bootstrap.min.css') }}" media="screen">
    {{-- Theme style --}}
    <link rel="stylesheet" href="{{ asset('assets/landlord/css/adminlte.min.css') }}" media="screen">
    {{-- overlayScrollbars --}}
    <link rel="stylesheet" href="{{ asset('assets/landlord/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}"
        media="screen">
    {{--  Sweetalert 2  --}}
    <link rel="stylesheet" href="{{ asset('assets/landlord/css/sweetalert2.min.css') }}" media="screen">
    {{--  Slick   --}}
    <link rel="stylesheet" href="{{ asset('assets/landlord/plugins/slick/slick.css') }}" media="screen">
    <link rel="stylesheet" href="{{ asset('assets/landlord/plugins/slick/slick-theme.css') }}" media="screen">
    {{-- Datatables --}}
    <link rel="stylesheet" href="{{ asset('assets/global/plugins/DataTables/datatables.min.css') }}" media="screen">
    {{-- Select2 --}}
    <link rel="stylesheet" href="{{ asset('assets/landlord/plugins/select2/select2.min.css') }}" media="screen">
    {{-- flickity --}}
    <link rel="stylesheet" href="{{ asset('assets/landlord/plugins/flickity/flickity.css') }}" media="screen">
    @if (app()->getLocale() == 'ar')
        <link rel="stylesheet" href="{{ asset('assets/landlord/css/custom.css') . '?v=1.2.1' }}" media="screen">
        <link rel="stylesheet" href="{{ asset('assets/landlord/css/bootstrap-rtl.min.css') }}" media="screen">
        <link rel="stylesheet" href="{{ asset('assets/landlord/css/dashboard.rtl.css') }}" media="screen">
    @else
        <style>
            table {
                direction: ltr;
            }
        </style>
    @endif
    {{-- Main Style --}}
    <link rel="stylesheet" href="{{ asset('assets/landlord/css/main.css') . '?v=1.2.1' }}" media="screen">
    {{-- Dark Mode --}}
    {{-- @if (
        !is_null(\App\Models\Setting::where('user_id', Auth::id())->first()) &&
            \App\Models\Setting::where('user_id', auth::id())->first()->theme_mode == 0)
        <link rel="stylesheet" id="DarkModeSheet" href="{{ asset('assets/landlord/css/darkmode-bootstrap.css') }}">
    @endif --}}
    {{-- Styles --}}
    <style>
        .swal2-image {
            width: 24% !important;
        }
    </style>

    @yield('styles')

</head>

<body class="sidebar-mini layout-fixed sidebar-collapse">
    {{-- All Content --}}
    <div class="wrapper">
        {{-- Header --}}
        @include('layouts.landlord.header')
        {{-- Aside --}}
        @include('layouts.landlord.sidebar')
        {{-- Content --}}
        <div class="content-wrapper">
            @yield('content')
        </div>
        {{-- Image Modal --}}
        {{-- @include('layouts.utilities.image-modal')
        @include('layouts.utilities.edit-modal')
        @include('layouts.utilities.create-modal') --}}

        @include("layouts.landlord.footer")
    </div>
    {{-- Jquery --}}
    <script src="{{ asset('assets/landlord/plugins/jquery-min/jquery-3.6.0.min.js') }}"></script>
    {{-- Jquery UI --}}
    <script src="{{ asset('assets/landlord/js/jquery-ui.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/global/lang/js/locale.js') }}" locale="{{ asset('assets/global/lang/' . Lang::locale() . '.json') }}">
    </script> --}}
    {{-- Bootstrap 4 --}}
    <script src="{{ asset('assets/landlord/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    {{-- Print This --}}
    <script src="{{ asset('assets/landlord/js/printthis.js') }}"></script>
    {{-- ChartJS --}}
    <script src="{{ asset('assets/landlord/plugins/chart.js/Chart.min.js') }}"></script>
    {{-- Sparkline --}}
    <script src="{{ asset('assets/landlord/plugins/sparklines/sparkline.js') }}"></script>
    {{-- overlayScrollbars --}}
    <script src="{{ asset('assets/landlord/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    {{-- AdminLTE App --}}
    <script src="{{ asset('assets/landlord/js/adminlte.min.js') }}"></script>
    {{-- Slick --}}
    <script src="{{ asset('assets/landlord/plugins/slick/slick.min.js') }}"></script>
    {{-- Sweet Alert --}}
    <script src="{{ asset('assets/landlord/js/sweetalert2.all.min.js') }}"></script>
    {{-- CKEDITOR --}}
    <script src="{{ asset('assets/landlord/plugins/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('assets/landlord/plugins/ckeditor/config.js') }}"></script>
    <script>
        CKEDITOR.config.language = "{{ app()->getLocale() }}";
    </script>
    {{-- Datatables --}}
    <script src="{{ asset('assets/global/plugins/DataTables/datatables.js') }}"></script>
    {{-- Select2 --}}
    <script src="{{ asset('assets/landlord/plugins/select2/select2.min.js') }}"></script>
    {{-- Apex Charts --}}
    <script src="{{ asset('assets/global/plugins/apexcharts/apexcharts.min.js') }}"></script>
    {{-- Flickity --}}
    <script src="{{ asset('assets/landlord/plugins/flickity/flickity.pkgd.min.js') }}"></script>


    {{-- Main Script --}}
    <script src="{{ asset('assets/landlord/js/main.js') . '?v=1.2.1' }}" 
        {{-- theme="{{ auth()->user()->getUserTheme() }}" --}}
     {{-- env="{{ env('APP_ENV') }}" --}}
     ></script>
    <script src="{{ asset('assets/global/js/shared.js') . '?v=1.2.1' }}"></script>

    @yield('scripts')
    @stack('scripts')
</body>

</html>
