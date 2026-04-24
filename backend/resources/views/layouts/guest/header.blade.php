<div class="container">
    <header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom">
        <a href="/"
            class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <svg class="bi me-2" width="40" height="32">
                <use xlink:href="#bootstrap"></use>
            </svg>
            <span class="fs-4">{{ env('APP_NAME') }}</span>
        </a>

        <ul class="nav nav-pills">
            @guest
                <a href="{{ route('login') }}" type="button" class="btn btn-outline-primary me-2">@t('login')</a>
            @endguest
            @auth
                <a style="cursor: pointer" data-form="logout-form" class="btn btn-primary logout-btn">
                    <i class="fas fa-sign-out-alt"></i> @translate('logout')
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @endauth
        </ul>
    </header>
</div>
