<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link">
        <!-- Store Logo / Name -->
        <img src="{{ asset('uploads/store_logo/' . Auth::user()?->store?->logo ?? 'default.png') }}" alt="logo"
             class="brand-image img-circle elevation-3 float-revert">
        <span class="brand-text font-weight-light">{{ Auth::user()->store?->name }}</span>
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                {{-- <img src="{{ Auth::user()->getThumbImagePathAttribute() }}" class="img-circle elevation-2" --}}
                     {{-- alt="User Image"> --}}
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->first_name . ' ' . auth()->user()->last_name }}</a>
            </div>
        </div>
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
            <div class="sidebar-search-results">
                <div class="list-group"><a href="#" class="list-group-item">
                        <div class="search-title"><div class="text-light">
                                @lang('no_elements_found')
                            </div></div>
                        <div class="search-path"></div>
                    </a></div>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link @if (strpos(Request::url(), '/')) active @endif">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>@lang('dashboard')
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                </li>
                <li class="nav-header">@lang('resources')</li>
                {{-- Categories --}}
                @can('read.categoriesa')
                    <li class="nav-item">
                        <a href="#" class="nav-link @if (strpos(Request::url(), '/categories')) active @endif">
                            <i class="nav-icon fas fa-tags"></i>
                            <p>@lang('categories')
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" style="display: none;">
                            <li class="nav-item">
                                <a href="{{ route('categories.index') }}" class="nav-link active">
                                    <i class="fas fa-tags"></i>
                                    <p>@lang('categories')</p>
                                </a>
                            </li>
                            @if (auth()->user()->hasPermission('create_categories'))
                                <li class="nav-item">
                                    <a href="#" data-url="{{ route('categories.create') }}"
                                       class="nav-link create-btn" type="button">
                                        <i class="fas fa-tags"></i>
                                        <p>@lang('add') @lang('categories')</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endcan
                @can('read.horizon')
                <li class="nav-item">
                    <a href="{{ env("HORIZON_PATH") }}" target="_blank" class="nav-link">
                      <i class="nav-icon fas fa-ellipsis-h"></i>
                      <p>Horizon</p>
                    </a>
                  </li>
                @endcan
                @can('read.telescope')
                <li class="nav-item">
                    <a href="{{ env("TELESCOPE_PATH") }}" target="_blank" class="nav-link">
                      <i class="nav-icon fas fa-ellipsis-h"></i>
                      <p>Telescope</p>
                    </a>
                  </li>
                @endcan
                <li style="height:10vh"></li>
            </ul>
        </nav>
    </div>
</aside>
