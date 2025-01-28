<!doctype html>
<html lang="{{ $language->locale }}" dir="{{ $language->direction }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ env('APP_NAME') }} {{ isset($title) ? ' | ' . $title : '' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/shared/images/icons/logo/favicon.ico') }}" />
    <meta name="_token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="{{ asset('assets/landlord/plugins/fontawesome-free/css/all.min.css') }}"
        media="screen">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="{{ asset('assets/shared/plugins/bootstrap-icons/css/bootstrap-icons.min.css') }}">
    {{-- Bootstrap --}}
    <link rel="stylesheet" href="{{ asset('assets/landlord/css/bootstrap.min.css') }}" media="screen">
    {{-- Theme style --}}
    <link rel="stylesheet" href="{{ asset('assets/landlord/css/adminlte.min.css') }}" media="screen">
    {{-- overlayScrollbars --}}
    <link rel="stylesheet"
        href="{{ asset('assets/landlord/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}" media="screen">
    {{--  Sweetalert 2  --}}
    <link rel="stylesheet" href="{{ asset('assets/landlord/css/sweetalert2.min.css') }}" media="screen">
    {{--  Slick   --}}
    <link rel="stylesheet" href="{{ asset('assets/landlord/plugins/slick/slick.css') }}" media="screen">
    <link rel="stylesheet" href="{{ asset('assets/landlord/plugins/slick/slick-theme.css') }}" media="screen">
    {{-- Datatables --}}
    <link rel="stylesheet" href="{{ asset('assets/shared/plugins/DataTables/datatables.min.css') }}" media="screen">
    {{-- Select2 --}}
    <link rel="stylesheet" href="{{ asset('assets/landlord/plugins/select2/select2.min.css') }}" media="screen">
    {{-- flickity --}}
    <link rel="stylesheet" href="{{ asset('assets/landlord/plugins/flickity/flickity.css') }}" media="screen">
    {{-- Bootstrap Toggle --}}
    <link rel="stylesheet" href="{{ asset('assets/shared/plugins/bootstrap-toggle/css/bootstrap-toggle.min.css') }}"
        media="screen">
    {{-- Intl Tel Input --}}
    <link rel="stylesheet" href="{{ asset('assets/shared/plugins/intl-tel-input/css/intlTelInput.min.css') }}"
        media="screen" />
    {{-- Emoji-Area --}}
    <link rel="stylesheet" href="{{ asset('assets/shared/plugins/emoji-area/emojionearea.min.css') }}"
    media="screen" />

    <link rel="stylesheet" href="{{ asset('assets/shared/plugins/jquery-file-upload/fileUpload.css') }}">

    @if ($language->locale == 'ar')
        <link rel="stylesheet" href="{{ asset('assets/landlord/css/custom.css') . '?v=1.2.1' }}" media="screen">
        <link rel="stylesheet" href="{{ asset('assets/landlord/css/bootstrap-rtl.min.css') }}" media="screen">
        <link rel="stylesheet" href="{{ asset('assets/landlord/css/dashboard.rtl.css') }}" media="screen">
    @endif

    <style>
        .direction {
            direction: {{ $language->direction }};
        }

        .dir {
            direction: {{ $language->direction }};
        }

        table {
            direction: {{ $language->direction }};
        }
    </style>
    {{-- Dark Mode --}}
    {{-- @if (auth()->user()->theme_mode == 1)
        <link rel="stylesheet" href="{{ asset('assets/landlord/css/darkmode-bootstrap.css') }}">
    @endif --}}
    {{-- Main Style --}}
    <link rel="stylesheet" href="{{ asset('assets/landlord/css/main.css') . '?v=' . filemtime(public_path('assets/landlord/css/main.css')) }}" media="screen">
    <link rel="stylesheet" href="{{ asset('assets/landlord/css/style.css') . '?v=' . filemtime(public_path('assets/landlord/css/style.css')) }}" media="screen">
    <link rel="stylesheet" href="{{ asset('assets/shared/css/style.css') . '?v=' . filemtime(public_path('assets/shared/css/style.css')) }}" media="screen">

    @yield('styles')
</head>

<body class="sidebar-mini layout-fixed {{ $language->direction }}">
    {{-- All Content --}}
    <div class="wrapper">
        {{-- Header --}}
        @include('layouts.landlord.header')
        {{-- Aside --}}
        @include('layouts.landlord.sidebar')
        {{-- Content --}}
        <div class="content-wrapper mt-2">
            <section class="content">
                @include('layouts.landlord.breadcrumb', isset($breadcrumbs) ? $breadcrumbs : [])
                @yield('content')
            </section>
        </div>

        {{-- Modals --}}
        @include('layouts.shared.modals.edit')
        @include('layouts.shared.modals.create')
        @include('layouts.shared.modals.show')
        @include('layouts.shared.modals.image-preview')

        {{-- Footer --}}
        @include('layouts.landlord.footer')
    </div>
    {{-- Jquery --}}
    <script src="{{ asset('assets/landlord/plugins/jquery-min/jquery-3.6.0.min.js') }}"></script>
    {{-- Jquery UI --}}
    <script src="{{ asset('assets/landlord/js/jquery-ui.min.js') }}"></script>
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
    <script src="{{ asset('assets/shared/plugins/ckeditor/ckeditor.js') }}"></script>
    {{-- Datatables --}}
    <script src="{{ asset('assets/shared/plugins/DataTables/datatables.min.js') }}"></script>
    {{-- Select2 --}}
    <script src="{{ asset('assets/landlord/plugins/select2/select2.min.js') }}"></script>
    {{-- Apex Charts --}}
    <script src="{{ asset('assets/shared/plugins/apexcharts/apexcharts.min.js') }}"></script>
    {{-- Flickity --}}
    <script src="{{ asset('assets/landlord/plugins/flickity/flickity.pkgd.min.js') }}"></script>
    {{-- Bootstrap Toggle --}}
    <script src="{{ asset('assets/shared/plugins/bootstrap-toggle/js/bootstrap-toggle.min.js') }}"></script>
    {{-- Intl Tel Input --}}
    <script src="{{ asset('assets/shared/plugins/intl-tel-input/js/intlTelInput.min.js') }}"></script>
    {{-- Emoji Area --}}
    <script src="{{ asset('assets/shared/plugins/emoji-area/emojionearea.min.js') }}"></script>
    {{-- Socket.io --}}
    {{-- <script src="{{ asset('assets/shared/plugins/socketio/socketio.min.js') }}"></script> --}}
    {{-- Socket.io Configurations --}}
    {{-- <script src="{{ asset('assets/landlord/js/socketio/config.js') }}"></script> --}}

    <script src="{{ asset('assets/shared/plugins/jquery-file-upload/fileUpload.js') }}"></script>

    {{-- Configurations --}}
    <script>
        let language = {
            locale: "{{ $language->locale }}",
            direction: "{{ $language->direction }}",
            languageFile: `{{ asset('assets/shared/lang/' . $language->locale . '.json') }}`,
            dataTableLanguageFile: `{{ asset('assets/shared/plugins/DataTables/lang/' . $language->locale . '.json') }}`,

        };
        CKEDITOR.config.language = "{{ $language->locale }}";
    </script>

    {{-- Main Scripts --}}
    <script src="{{ asset('assets/shared/js/shared.js') . '?v=' . filemtime(public_path('assets/shared/js/shared.js')) }}">
    </script>
    <script src="{{ asset('assets/landlord/js/main.js') . '?v=' . filemtime(public_path('assets/landlord/js/main.js')) }}">
    </script>
    
    @yield('scripts')
    @stack('scripts')

    {{-- App will still running without [Vite manifest not found] error if you're on localhost and not running npm run dev --}}
    @if (app()->environment('local') && isRunningViteDevServer())
        @viteReactRefresh
        @vite('resources/js/app.jsx')
    @endif
</body>

</html>
