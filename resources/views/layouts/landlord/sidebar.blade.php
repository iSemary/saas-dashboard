<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link">
        <!-- Store Logo / Name -->
        <img src="{{ asset('assets/global/images/icons/logo/favicon.svg') }}" alt="logo"
            class="brand-image img-circle elevation-3 float-revert">
        <span class="brand-text font-weight-light">{{ env('APP_NAME') }}</span>
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ auth()->user()->avatar }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->name }}</a>
            </div>
        </div>
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
            <div class="sidebar-search-results">
                <div class="list-group"><a href="#" class="list-group-item">
                        <div class="search-title">
                            <div class="text-light">
                                @translate('no_elements_found')
                            </div>
                        </div>
                        <div class="search-path"></div>
                    </a></div>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                @php
                    $sidebarNavigation = [
                        'main' => [
                            'icon' => 'fas fa-home',
                            'label' => 'main',
                            'items' => [
                                'dashboard' => [
                                    'icon' => 'fas fa-tachometer-alt',
                                    'single' => true,
                                    'external' => false,
                                    'route' => 'home',
                                ],
                            ],
                        ],
                        'account_management' => [
                            'icon' => 'fas fa-user-cog',
                            'label' => 'account_management',
                            'items' => [
                                'users' => [
                                    'icon' => 'fas fa-users',
                                    'permission' => 'read.users',
                                    'route' => 'landlord.users.index',
                                    'single' => true,
                                    'external' => false,
                                ],
                                'clients' => [
                                    'icon' => 'fas fa-briefcase',
                                    'permission' => 'read.clients',
                                    'route' => 'landlord.clients.index',
                                    'single' => true,
                                    'external' => false,
                                ],
                                'tenants' => [
                                    'icon' => 'fas fa-cubes',
                                    'permission' => 'read.tenants',
                                    'route' => 'landlord.tenants.index',
                                    'single' => true,
                                    'external' => false,
                                ],
                            ],
                        ],
                        'mailing' => [
                            'icon' => 'fas fa-mail-bulk',
                            'label' => 'mailing',
                            'items' => [
                                'email_templates' => [
                                    'icon' => 'fas fa-envelope-open-text',
                                    'permission' => [
                                        'read' => 'read.email_templates',
                                        'create' => 'create.email_templates',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.email-templates.index',
                                        'create' => 'landlord.email-templates.create',
                                    ],
                                ],
                                'email_log' => [
                                    'icon' => 'fas fa-history',
                                    'permission' => 'read.email_logs',
                                    'route' => 'landlord.email-logs.index',
                                    'single' => true,
                                    'external' => false,
                                ],
                            ],
                        ],
                        'locale' => [
                            'icon' => 'fas fa-globe-americas',
                            'label' => 'locale',
                            'items' => [
                                'languages' => [
                                    'icon' => 'fas fa-language',
                                    'permission' => [
                                        'read' => 'read.languages',
                                        'create' => 'create.languages',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.languages.index',
                                        'create' => 'landlord.languages.create',
                                    ],
                                ],
                                'translations' => [
                                    'icon' => 'fas fa-exchange-alt',
                                    'permission' => [
                                        'read' => 'read.translations',
                                        'create' => 'create.translations',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.translations.index',
                                        'create' => 'landlord.translations.create',
                                    ],
                                ],
                            ],
                        ],
                        'geography' => [
                            'icon' => 'fas fa-map-marked-alt',
                            'label' => 'geography',
                            'items' => [
                                'countries' => [
                                    'icon' => 'fas fa-flag',
                                    'permission' => [
                                        'read' => 'read.countries',
                                        'create' => 'create.countries',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.countries.index',
                                        'create' => 'landlord.countries.create',
                                    ],
                                ],
                                'provinces' => [
                                    'icon' => 'fas fa-map-marker-alt',
                                    'permission' => [
                                        'read' => 'read.provinces',
                                        'create' => 'create.provinces',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.provinces.index',
                                        'create' => 'landlord.provinces.create',
                                    ],
                                ],
                                'cities' => [
                                    'icon' => 'fas fa-city',
                                    'permission' => [
                                        'read' => 'read.cities',
                                        'create' => 'create.cities',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.cities.index',
                                        'create' => 'landlord.cities.create',
                                    ],
                                ],
                                'towns' => [
                                    'icon' => 'fas fa-home',
                                    'permission' => [
                                        'read' => 'read.towns',
                                        'create' => 'create.towns',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.towns.index',
                                        'create' => 'landlord.towns.create',
                                    ],
                                ],
                                'streets' => [
                                    'icon' => 'fas fa-road',
                                    'permission' => [
                                        'read' => 'read.streets',
                                        'create' => 'create.streets',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.streets.index',
                                        'create' => 'landlord.streets.create',
                                    ],
                                ],
                            ],
                        ],
                        'utilities' => [
                            'icon' => 'fas fa-tools',
                            'label' => 'utilities',
                            'items' => [
                                'categories' => [
                                    'icon' => 'fas fa-tags',
                                    'permission' => [
                                        'read' => 'read.categories',
                                        'create' => 'create.categories',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.categories.index',
                                        'create' => 'landlord.categories.create',
                                    ],
                                ],
                                'tags' => [
                                    'icon' => 'fas fa-tag',
                                    'permission' => [
                                        'read' => 'read.tags',
                                        'create' => 'create.tags',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.tags.index',
                                        'create' => 'landlord.tags.create',
                                    ],
                                ],
                                'types' => [
                                    'icon' => 'fas fa-icons',
                                    'permission' => [
                                        'read' => 'read.types',
                                        'create' => 'create.types',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.types.index',
                                        'create' => 'landlord.types.create',
                                    ],
                                ],
                                'industries' => [
                                    'icon' => 'fas fa-building',
                                    'permission' => [
                                        'read' => 'read.industries',
                                        'create' => 'create.industries',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.industries.index',
                                        'create' => 'landlord.industries.create',
                                    ],
                                ],
                                'currencies' => [
                                    'icon' => 'fas fa-dollar-sign',
                                    'permission' => [
                                        'read' => 'read.currencies',
                                        'create' => 'create.currencies',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.currencies.index',
                                        'create' => 'landlord.currencies.create',
                                    ],
                                ],
                            ],
                        ],
                        'payments' => [
                            'icon' => 'fas fa-credit-card',
                            'label' => 'payments',
                            'items' => [
                                'payment_methods' => [
                                    'icon' => 'fas fa-money-check-alt',
                                    'permission' => 'read.payment_methods',
                                    'route' => 'landlord.payment-methods.index',
                                    'single' => true,
                                ],
                                'payment_logs' => [
                                    'icon' => 'fas fa-file-invoice-dollar',
                                    'permission' => 'read.payment_logs',
                                    'route' => 'landlord.payment-logs.index',
                                    'single' => true,
                                ],
                            ],
                        ],
                        'subscriptions' => [
                            'icon' => 'fas fa-recurring',
                            'label' => 'subscriptions',
                            'items' => [
                                'subscriptions' => [
                                    'icon' => 'fas fa-calendar-alt',
                                    'permission' => 'read.subscriptions',
                                    'route' => 'landlord.subscriptions.index',
                                    'single' => true,
                                ],
                                'plans' => [
                                    'icon' => 'fas fa-clipboard-list',
                                    'permission' => 'read.plans',
                                    'route' => 'landlord.plans.index',
                                    'single' => true,
                                ],
                            ],
                        ],
                        'authorizations' => [
                            'icon' => 'fas fa-user-shield',
                            'label' => 'authorizations',
                            'items' => [
                                'permissions' => [
                                    'icon' => 'fas fa-lock',
                                    'permission' => [
                                        'read' => 'read.permissions',
                                        'create' => 'create.permissions',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.permissions.index',
                                        'create' => 'landlord.permissions.create',
                                    ],
                                ],
                                'roles' => [
                                    'icon' => 'fas fa-user-tag',
                                    'permission' => [
                                        'read' => 'read.roles',
                                        'create' => 'create.roles',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.roles.index',
                                        'create' => 'landlord.roles.create',
                                    ],
                                ],
                            ],
                        ],
                        'system_settings' => [
                            'icon' => 'fas fa-cogs',
                            'label' => 'system_settings',
                            'items' => [
                                'settings' => [
                                    'icon' => 'fas fa-sliders-h',
                                    'permission' => 'read.settings',
                                    'route' => 'landlord.settings.index',
                                    'single' => true,
                                ],
                                'announcements' => [
                                    'icon' => 'fas fa-bullhorn',
                                    'permission' => [
                                        'read' => 'read.announcements',
                                        'create' => 'create.announcements',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.announcements.index',
                                        'create' => 'landlord.announcements.create',
                                    ],
                                ],
                                'modules' => [
                                    'icon' => 'fas fa-puzzle-piece',
                                    'permission' => [
                                        'read' => 'read.modules',
                                        'create' => 'create.modules',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.modules.index',
                                        'create' => 'landlord.modules.create',
                                    ],
                                ],
                            ],
                        ],
                        'system_monitoring' => [
                            'icon' => 'fas fa-server',
                            'label' => 'system_monitoring',
                            'items' => [
                                'horizon' => [
                                    'icon' => 'fas fa-chart-line',
                                    'permission' => [
                                        'read' => 'read.horizon',
                                        'create' => false,
                                    ],
                                    'routes' => [
                                        'index' => 'horizon.index',
                                    ],
                                    'external' => true,
                                    'path' => env('HORIZON_PATH'),
                                ],
                                'telescope' => [
                                    'icon' => 'fas fa-search-plus',
                                    'permission' => [
                                        'read' => 'read.telescope',
                                        'create' => false,
                                    ],
                                    'external' => true,
                                    'path' => env('TELESCOPE_PATH'),
                                ],
                            ],
                        ],
                        'development' => [
                            'icon' => 'fas fa-server',
                            'label' => 'development',
                            'items' => [
                                'code_builder' => [
                                    'icon' => 'fas fa-code',
                                    'permission' => 'read.code_builder',
                                    'route' => 'landlord.development.code-builder.show',
                                    'single' => true,
                                ],
                                'env_diff' => [
                                    'icon' => 'fas fa-file-alt',
                                    'permission' => 'read.env_diff',
                                    'route' => 'landlord.development.env-diff.show',
                                    'single' => true,
                                ],
                            ],
                        ],
                    ];
                @endphp

                @foreach ($sidebarNavigation as $section => $sectionConfig)
                    @php
                        // Check if the user has permission to see ANY item in this section
                        $hasVisibleItems = collect($sectionConfig['items'])->contains(function ($item) {
                            // Check if the item has no permission requirement or the user has the required permission
                            return !isset($item['permission']) || Gate::check($item['permission']['read'] ?? null);
                        });
                    @endphp

                    @if ($hasVisibleItems)
                        <li class="nav-header">@translate($sectionConfig['label'])</li>
                        @if (isset($sectionConfig['items']))
                            @foreach ($sectionConfig['items'] as $key => $item)
                                {{-- Check for routes without permission or with specific permission --}}
                                @if (!isset($item['permission']) || Gate::check($item['permission']['read'] ?? null))
                                    @if (isset($item['external']) && $item['external'])
                                        <li class="nav-item">
                                            <a href="{{ $item['path'] ?? route($item['routes']['index']) }}"
                                                target="_blank" class="nav-link">
                                                <i class="nav-icon fas fa-{{ $item['icon'] ?? '' }}"></i>
                                                <p>@translate($key)</p>
                                            </a>
                                        </li>
                                    @elseif(isset($item['single']) && $item['single'])
                                        @if (!isset($item['permission']) || Gate::check($item['permission']))
                                            <li class="nav-item">
                                                <a href="{{ route($item['route']) }}"
                                                    class="nav-link @if (Request::route()->getName() === $item['route']) active @endif">
                                                    <i class="nav-icon fas fa-{{ $item['icon'] ?? '' }}"></i>
                                                    <p>@translate($key)</p>
                                                </a>
                                            </li>
                                        @endif
                                    @else
                                        <li class="nav-item">
                                            <a href="#"
                                                class="nav-link @if (strpos(Request::url(), '/' . $key) !== false) active @endif">
                                                <i class="nav-icon fas fa-{{ $item['icon'] ?? '' }}"></i>
                                                <p>
                                                    @translate($key)
                                                    <i class="right fas fa-angle-left"></i>
                                                </p>
                                            </a>
                                            <ul class="nav nav-treeview" style="display: none;">
                                                {{-- Index Page --}}
                                                <li class="nav-item">
                                                    <a href="{{ route($item['routes']['index']) }}"
                                                        class="pl-2 nav-link @if (Request::url() === route($item['routes']['index'])) active @endif">
                                                        <i class="fas fa-{{ $item['icon'] ?? '' }}"></i>
                                                        <p>@translate($key)</p>
                                                    </a>
                                                </li>

                                                {{-- Create Page --}}
                                                @if (isset($item['permission']['create']) && $item['permission']['create'])
                                                    @can($item['permission']['create'])
                                                        <li class="nav-item">
                                                            <a href="#"
                                                                data-modal-title="@translate('create') @translate($key)"
                                                                data-modal-link="{{ route($item['routes']['create']) }}"
                                                                class="pl-2 nav-link open-create-modal" type="button">
                                                                <i class="fas fa-plus-circle"></i>
                                                                <p>@translate('create') @translate($key)</p>
                                                            </a>
                                                        </li>
                                                    @endcan
                                                @endif
                                            </ul>
                                        </li>
                                    @endif
                                @endif
                            @endforeach
                        @endif
                    @endif
                @endforeach
                <li style="height:10vh"></li>
            </ul>
        </nav>
    </div>
</aside>
