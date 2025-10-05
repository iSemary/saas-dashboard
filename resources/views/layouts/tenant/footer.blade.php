{{-- Main Footer --}}
<footer class="main-footer">
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">{{ config('app.name') }}</a>.</strong>
    @translate('all_rights_reserved')
    <div class="float-right d-none d-sm-inline-block">
        <b>@translate('version')</b> 1.0.0
    </div>
</footer>
