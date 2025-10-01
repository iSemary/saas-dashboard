<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $tenant->name ?? 'Dashboard' }} - Business Dashboard</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Navigation -->
    <nav class="gradient-bg shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-white text-xl font-bold">{{ $tenant->name ?? 'Business Dashboard' }}</h1>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <div class="relative">
                        <button class="text-white hover:text-gray-200 p-2" onclick="toggleNotifications()">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center" id="notification-count">2</span>
                        </button>
                    </div>
                    
                    <!-- User Menu -->
                    <div class="relative">
                        <button class="flex items-center text-white hover:text-gray-200" onclick="toggleUserMenu()">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-2">
                                <i class="fas fa-user text-sm"></i>
                            </div>
                            <span>{{ $user->name }}</span>
                            <i class="fas fa-chevron-down ml-2 text-sm"></i>
                        </button>
                        
                        <!-- User Dropdown -->
                        <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i>Profile
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog mr-2"></i>Settings
                            </a>
                            <hr class="my-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Welcome back, {{ $user->name }}!</h2>
            <p class="text-gray-600 mt-2">Here's what's happening with your business today.</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6 card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900" id="total-users">{{ $stats['overview']['total_users'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-user-check text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active Users</p>
                        <p class="text-2xl font-bold text-gray-900" id="active-users">{{ $stats['overview']['active_users'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-folder text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Projects</p>
                        <p class="text-2xl font-bold text-gray-900" id="total-projects">{{ $stats['overview']['total_projects'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Growth Rate</p>
                        <p class="text-2xl font-bold text-gray-900" id="growth-rate">{{ $stats['overview']['growth_rate'] ?? 0 }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Activity -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                        <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</button>
                    </div>
                    
                    <div class="space-y-4" id="recent-activity">
                        @foreach($stats['recent_activity'] ?? [] as $activity)
                        <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-{{ $activity['color'] }}-100 flex items-center justify-center">
                                    <i class="fas {{ $activity['icon'] }} text-{{ $activity['color'] }}-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $activity['message'] }}</p>
                                <p class="text-xs text-gray-500">{{ $activity['user'] }} • {{ $activity['time'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Quick Actions</h3>
                    
                    <div class="space-y-3" id="quick-actions">
                        @foreach($stats['quick_actions'] ?? [] as $action)
                        <button class="w-full flex items-center p-3 rounded-lg border border-gray-200 hover:border-{{ $action['color'] }}-300 hover:bg-{{ $action['color'] }}-50 transition-all group">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-{{ $action['color'] }}-100 flex items-center justify-center group-hover:bg-{{ $action['color'] }}-200 transition-colors">
                                    <i class="fas {{ $action['icon'] }} text-{{ $action['color'] }}-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-3 text-left">
                                <p class="text-sm font-medium text-gray-900">{{ $action['title'] }}</p>
                                <p class="text-xs text-gray-500">{{ $action['description'] }}</p>
                            </div>
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications Panel -->
        <div id="notifications-panel" class="hidden fixed top-16 right-4 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                    <button onclick="toggleNotifications()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="max-h-96 overflow-y-auto">
                @foreach($stats['notifications'] ?? [] as $notification)
                <div class="p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-2 h-2 rounded-full bg-{{ $notification['type'] == 'info' ? 'blue' : 'green' }}-500 mt-2"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $notification['title'] }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ $notification['message'] }}</p>
                            <p class="text-xs text-gray-400 mt-2">{{ $notification['time'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Toggle user menu
        function toggleUserMenu() {
            const menu = document.getElementById('user-menu');
            menu.classList.toggle('hidden');
        }

        // Toggle notifications
        function toggleNotifications() {
            const panel = document.getElementById('notifications-panel');
            panel.classList.toggle('hidden');
        }

        // Close menus when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('user-menu');
            const notificationsPanel = document.getElementById('notifications-panel');
            
            if (!event.target.closest('.relative')) {
                userMenu.classList.add('hidden');
                notificationsPanel.classList.add('hidden');
            }
        });

        // Load dashboard stats
        function loadDashboardStats() {
            fetch('/dashboard/stats')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateStats(data.data);
                    }
                })
                .catch(error => {
                    console.error('Error loading dashboard stats:', error);
                });
        }

        // Update stats on the page
        function updateStats(stats) {
            if (stats.overview) {
                document.getElementById('total-users').textContent = stats.overview.total_users || 0;
                document.getElementById('active-users').textContent = stats.overview.active_users || 0;
                document.getElementById('total-projects').textContent = stats.overview.total_projects || 0;
                document.getElementById('growth-rate').textContent = (stats.overview.growth_rate || 0) + '%';
            }
        }

        // Load stats on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardStats();
        });
    </script>
</body>
</html>
