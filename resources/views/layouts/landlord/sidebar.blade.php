<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link">
        <!-- Store Logo / Name -->
        <img src="{{ asset('assets/shared/images/icons/logo/favicon.svg') }}" alt="logo"
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
                <input class="form-control form-control-sidebar" type="search" placeholder="@translate('search')"
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
                                'system_users' => [
                                    'icon' => 'fas fa-user-cog',
                                    'permission' => 'read.system_users',
                                    'route' => 'landlord.system-users.index',
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
                                'email_campaigns' => [
                                    'icon' => 'fas fa-bullhorn',
                                    'permission' => [
                                        'read' => 'read.email_campaigns',
                                        'create' => 'create.email_campaigns',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.email-campaigns.index',
                                        'create' => 'landlord.email-campaigns.create',
                                    ],
                                ],
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
                                'email_credentials' => [
                                    'icon' => 'fas fa-key',
                                    'permission' => [
                                        'read' => 'read.email_credentials',
                                        'create' => 'create.email_credentials',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.email-credentials.index',
                                        'create' => 'landlord.email-credentials.create',
                                    ],
                                ],
                                'email_recipients' => [
                                    'icon' => 'fas fa-user-plus',
                                    'permission' => [
                                        'read' => 'read.email_recipients',
                                        'create' => 'create.email_recipients',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.email-recipients.index',
                                        'create' => 'landlord.email-recipients.create',
                                    ],
                                ],
                                'email_groups' => [
                                    'icon' => 'fas fa-user-friends',
                                    'permission' => [
                                        'read' => 'read.email_groups',
                                        'create' => 'create.email_groups',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.email-groups.index',
                                        'create' => 'landlord.email-groups.create',
                                    ],
                                ],
                                'email_subscribers' => [
                                    'icon' => 'fas fa-users',
                                    'permission' => 'read.email_subscribers',
                                    'route' => 'landlord.email-subscribers.index',
                                    'single' => true,
                                    'external' => false,
                                ],
                                'email_log' => [
                                    'icon' => 'fas fa-history',
                                    'permission' => 'read.emails',
                                    'route' => 'landlord.emails.index',
                                    'single' => true,
                                    'external' => false,
                                ],
                                'compose_email' => [
                                    'icon' => 'fas fa-paper-plane',
                                    'permission' => 'send.emails',
                                    'attr' => [
                                        'data-modal-link' => route('landlord.emails.compose'),
                                        'data-modal-title' => translate('compose'),
                                    ],
                                    'single' => true,
                                    'modal' => true,
                                    'class' => 'open-details-btn compose-email-btn',
                                    'text' => 'compose',
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
                                'units' => [
                                    'icon' => 'fas fa-ruler',
                                    'permission' => [
                                        'read' => 'read.units',
                                        'create' => 'create.units',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.units.index',
                                        'create' => 'landlord.units.create',
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
                                'static_pages' => [
                                    'icon' => 'fas fa-scroll',
                                    'permission' => [
                                        'read' => 'read.static_pages',
                                        'create' => 'create.static_pages',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.static-pages.index',
                                        'create' => 'landlord.static-pages.create',
                                    ],
                                ],
                                'releases' => [
                                    'icon' => 'fas fa-rocket',
                                    'permission' => [
                                        'read' => 'read.releases',
                                        'create' => 'create.releases',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.releases.index',
                                        'create' => 'landlord.releases.create',
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
                                    'external' => true,
                                    'translate' => false,
                                    'path' => '/' . env('HORIZON_PATH'),
                                ],
                                'telescope' => [
                                    'icon' => 'fas fa-search-plus',
                                    'permission' => [
                                        'read' => 'read.telescope',
                                        'create' => false,
                                    ],
                                    'external' => true,
                                    'translate' => false,
                                    'path' => '/' . env('TELESCOPE_PATH'),
                                ],
                                'log_viewer' => [
                                    'icon' => 'fas fa-file-alt',
                                    'permission' => [
                                        'read' => 'read.log_viewer',
                                        'create' => false,
                                    ],
                                    'external' => true,
                                    'path' => '/' . env('LOG_VIEWER_PATH'),
                                ],
                            ],
                        ],
                        'development' => [
                            'icon' => 'fas fa-server',
                            'label' => 'development',
                            'items' => [
                                'system_status' => [
                                    'icon' => 'fas fa-server',
                                    'permission' => 'read.system_status',
                                    'route' => 'landlord.development.system-status.show',
                                    'single' => true,
                                    'translate' => false,
                                ],
                                'files' => [
                                    'icon' => 'fas fa-photo-video',
                                    'permission' => 'read.files',
                                    'route' => 'landlord.development.files.index',
                                    'single' => true,
                                ],
                                'file_manager' => [
                                    'icon' => 'fas fa-folder-open',
                                    'permission' => 'manage.files',
                                    'route' => 'landlord.development.files.manage',
                                    'single' => true,
                                ],
                                'configurations' => [
                                    'icon' => 'fas fa-cogs',
                                    'permission' => 'read.configurations',
                                    'route' => 'landlord.development.configurations.index',
                                    'single' => true,
                                ],
                                'backups' => [
                                    'icon' => 'fas fa-cloud',
                                    'permission' => 'read.backups',
                                    'route' => 'landlord.development.backups.index',
                                    'single' => true,
                                ],
                                'ip_blacklists' => [
                                    'icon' => 'fas fa-map-marker-alt',
                                    'permission' => [
                                        'read' => 'read.ip_blacklists',
                                        'create' => 'create.ip_blacklists',
                                    ],
                                    'routes' => [
                                        'index' => 'landlord.development.ip-blacklists.index',
                                        'create' => 'landlord.development.ip-blacklists.create',
                                    ],
                                ],
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
                                    'translate' => false,
                                ],
                                'modules_entities' => [
                                    'icon' => 'fas fa-vector-square',
                                    'permission' => 'read.modules_flow',
                                    'route' => 'landlord.development.entities.index',
                                    'single' => true,
                                    'translate' => false,
                                ],
                                'modules_flow' => [
                                    'icon' => 'fas fa-bezier-curve',
                                    'permission' => 'read.modules_flow',
                                    'route' => 'landlord.development.flows.modules',
                                    'single' => true,
                                    'translate' => false,
                                ],
                                'database_flow' => [
                                    'icon' => 'fas fa-bezier-curve',
                                    'permission' => 'read.database_flow',
                                    'route' => 'landlord.development.flows.database',
                                    'single' => true,
                                    'translate' => false,
                                ],
                            ],
                        ],
                    ];
                @endphp

                @foreach ($sidebarNavigation as $section => $sectionConfig)
                    @php
                        // Check if user has permission for any visible items AND if those items have content
                        $hasVisibleItems = false;
                        foreach ($sectionConfig['items'] as $item) {
                            $hasPermission =
                                !isset($item['permission']) ||
                                (is_array($item['permission']) && Gate::check($item['permission']['read'])) ||
                                (is_string($item['permission']) && Gate::check($item['permission']));

                            if ($hasPermission) {
                                $hasVisibleItems = true;
                                break;
                            }
                        }
                    @endphp

                    @if ($hasVisibleItems)
                        <li class="nav-header">@translate($sectionConfig['label'])</li>
                        @if (isset($sectionConfig['items']))
                            @foreach ($sectionConfig['items'] as $key => $item)
                                {{-- Check for routes without permission or with specific permission --}}
                                @if (!isset($item['permission']) || (is_array($item['permission']) && Gate::check($item['permission']['read'] ?? null)) || (is_string($item['permission']) && Gate::check($item['permission'])))
                                    @if (isset($item['external']) && $item['external'])
                                        <li class="nav-item">
                                            <a href="{{ $item['path'] ?? route($item['routes']['index']) }}"
                                                target="_blank" class="nav-link">
                                                <i class="nav-icon fas fa-{{ $item['icon'] ?? '' }}"></i>
                                                {{-- <p>@translate($key)</p> --}}
                                                <p>{{ isset($item['translate']) && !$item['translate'] ? translate($key, [], 'en') : translate($key) }}
                                                </p>
                                            </a>
                                        </li>
                                    @elseif(isset($item['single']) && $item['single'])
                                        @if (!isset($item['permission']) || Gate::check($item['permission']))
                                            @if (isset($item['modal']))
                                                <li class="nav-item">
                                                    <a href="#"
                                                        @if (isset($item['attr'])) @foreach ($item['attr'] as $attr => $value){{ $attr }}="{{ $value }}"@endforeach @endif
                                                        class="nav-link {{ isset($item['class']) ? $item['class'] : '' }}">
                                                        <i class="nav-icon fas fa-{{ $item['icon'] ?? '' }}"></i>
                                                        <p>{{ isset($item['translate']) && !$item['translate'] ? translate($key, [], 'en') : translate($key) }}
                                                        </p>
                                                    </a>
                                                </li>
                                            @else
                                                <li class="nav-item">
                                                    <a href="{{ route($item['route']) }}"
                                                        class="nav-link @if (Request::route()->getName() === $item['route']) active @endif">
                                                        <i class="nav-icon fas fa-{{ $item['icon'] ?? '' }}"></i>
                                                        <p>{{ isset($item['translate']) && !$item['translate'] ? translate($key, [], 'en') : translate($key) }}
                                                        </p>
                                                    </a>
                                                </li>
                                            @endif
                                        @endif
                                    @else
                                    @php
                                    // Check if any child route is active for this parent
                                    $isAnyChildActive = false;
                                    if (isset($item['routes'])) {
                                        $currentRoute = Request::route()->getName();
                                        foreach ($item['routes'] as $routeName) {
                                            if (strpos($currentRoute, $routeName) !== false) {
                                                $isAnyChildActive = true;
                                                break;
                                            }
                                        }
                                    }
                                @endphp
                                        <li class="nav-item @if($isAnyChildActive) menu-open @endif">
                                            <a href="#"
                                                class="nav-link @if($isAnyChildActive || strpos(Request::url(), '/' . $key) !== false) active @endif">
                                                <i class="nav-icon fas fa-{{ $item['icon'] ?? '' }}"></i>
                                                <p>
                                                    @translate($key)
                                                    <i class="right fas fa-angle-left"></i>
                                                </p>
                                            </a>
                                            <ul class="nav nav-treeview" @if($isAnyChildActive) style="display: block;" @else style="display: none;" @endif>
                                                @if (isset($item['modal']))
                                                    <li class="nav-item">
                                                        <a href="#"
                                                            @if (isset($item['attr'])) @foreach ($item['attr'] as $attr => $value){{ $attr }}="{{ $value }}"@endforeach @endif
                                                            class="pl-2 nav-link {{ isset($item['class']) ? $item['class'] : '' }}">
                                                            <i class="fas fa-{{ $item['icon'] ?? '' }}"></i>
                                                            <p>{{ isset($item['translate']) && !$item['translate'] ? translate($key, [], 'en') : translate($key) }}
                                                            </p>
                                                        </a>
                                                    </li>
                                                @else
                                                    <li class="nav-item">
                                                        <a href="{{ route($item['routes']['index']) }}"
                                                            class="pl-2 nav-link @if (Request::url() === route($item['routes']['index'])) active @endif">
                                                            <i class="fas fa-{{ $item['icon'] ?? '' }}"></i>
                                                            <p>{{ isset($item['translate']) && !$item['translate'] ? translate($key, [], 'en') : translate($key) }}
                                                            </p>
                                                        </a>
                                                    </li>
                                                @endif

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
