<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\User;
use Modules\Auth\Entities\Role;
use Modules\Auth\Entities\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TenantEntitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Creating comprehensive tenant entities...');

        // 1. Create Roles with Permissions
        $this->createRolesAndPermissions();
        
        // 2. Create Sample Users
        $this->createSampleUsers();
        
        // 3. Create Projects
        $this->createProjects();
        
        // 4. Create Tasks for Projects
        $this->createTasks();
        
        // 5. Create Organizations/Companies
        $this->createOrganizations();
        
        // 6. Create Departments
        $this->createDepartments();
        
        // 7. Create Teams
        $this->createTeams();
        
        // 8. Create Meetings/Events
        $this->createMeetings();
        
        // 9. Create Documents/Files
        $this->createDocuments();
        
        // 10. Create Notifications
        $this->createNotifications();

        $this->command->info('✅ Tenant entities seeding completed successfully!');
    }

    private function createRolesAndPermissions()
    {
        $this->command->info('📋 Creating tenant roles and permissions...');

        // Create permissions
        $permissions = [
            'view.users', 'create.users', 'update.users', 'delete.users',
            'view.projects', 'create.projects', 'update.projects', 'delete.projects',
            'view.tasks', 'create.tasks', 'update.tasks', 'delete.tasks',
            'view.organizations', 'create.organizations', 'update.organizations', 'delete.organizations',
            'view.departments', 'create.departments', 'update.departments', 'delete.departments',
            'view.teams', 'create.teams', 'update.teams', 'delete.teams',
            'view.meetings', 'create.meetings', 'update.meetings', 'delete.meetings',
            'view.documents', 'create.documents', 'update.documents', 'delete.documents',
            'view.reports', 'create.reports', 'view.settings', 'update.settings',
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['name' => $permission, 'guard_name' => 'web']
            );
        }

        // Create roles with permissions
        $roles = [
            'super_admin' => [
                'description' => 'Full system access with all permissions',
                'permissions' => Permission::all()->pluck('name')->toArray()
            ],
            'admin' => [
                'description' => 'Administrative access with most permissions',
                'permissions' => [
                    'view.users', 'create.users', 'update.users', 'delete.users',
                    'view.projects', 'create.projects', 'update.projects', 'delete.projects',
                    'view.tasks', 'create.tasks', 'update.tasks', 'delete.tasks',
                    'view.organizations', 'create.organizations', 'update.organizations',
                    'view.departments', 'create.departments', 'update.departments',
                    'view.teams', 'create.teams', 'update.teams',
                    'view.meetings', 'create.meetings', 'update.meetings',
                    'view.reports', 'create.reports', 'view.settings'
                ]
            ],
            'manager' => [
                'description' => 'Management access for team and project oversight',
                'permissions' => [
                    'view.users', 'view.projects', 'create.projects', 'update.projects',
                    'view.tasks', 'create.tasks', 'update.tasks',
                    'view.teams', 'update.teams',
                    'view.meetings', 'create.meetings', 'update.meetings',
                    'view.reports', 'create.reports'
                ]
            ],
            'team_lead' => [
                'description' => 'Team leadership role with limited administrative access',
                'permissions' => [
                    'view.users', 'view.projects', 'update.projects',
                    'view.tasks', 'create.tasks', 'update.tasks',
                    'view.teams',
                    'view.meetings', 'create.meetings',
                    'view.reports'
                ]
            ],
            'employee' => [
                'description' => 'Standard employee access',
                'permissions' => [
                    'view.users', 'view.projects', 'view.tasks', 'update.tasks',
                    'view.teams', 'view.meetings'
                ]
            ],
            'viewer' => [
                'description' => 'Read-only access for stakeholders',
                'permissions' => [
                    'view.users', 'view.projects', 'view.tasks',
                    'view.teams', 'view.meetings', 'view.reports'
                ]
            ]
        ];

        foreach ($roles as $roleName => $roleData) {
            $role = Role::updateOrCreate(
                ['name' => $roleName, 'guard_name' => 'web'],
                ['name' => $roleName, 'guard_name' => 'web']
            );
            
            $role->syncPermissions($roleData['permissions']);
            
            $this->command->info("   ✅ Created role: {$roleName}");
        }
    }

    private function createSampleUsers()
    {
        $this->command->info('👥 Creating sample users...');

        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@tenant.test',
                'username' => 'superadmin',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'department' => 'IT',
                'position' => 'System Administrator'
            ],
            [
                'name' => 'John Smith',
                'email' => 'john.smith@tenant.test',
                'username' => 'johnsmith',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'department' => 'Management',
                'position' => 'CEO'
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@tenant.test',
                'username' => 'sarahjohnson',
                'password' => Hash::make('password123'),
                'role' => 'manager',
                'department' => 'Engineering',
                'position' => 'Engineering Manager'
            ],
            [
                'name' => 'Mike Wilson',
                'email' => 'mike.wilson@tenant.test',
                'username' => 'mikewilson',
                'password' => Hash::make('password123'),
                'role' => 'team_lead',
                'department' => 'Engineering',
                'position' => 'Senior Developer'
            ],
            [
                'name' => 'Emma Davis',
                'email' => 'emma.davis@tenant.test',
                'username' => 'emmadavis',
                'password' => Hash::make('password123'),
                'role' => 'employee',
                'department' => 'Marketing',
                'position' => 'Marketing Specialist'
            ],
            [
                'name' => 'David Brown',
                'email' => 'david.brown@tenant.test',
                'username' => 'davidbrown',
                'password' => Hash::make('password123'),
                'role' => 'employee',
                'department' => 'Sales',
                'position' => 'Sales Representative'
            ],
            [
                'name' => 'Lisa Anderson',
                'email' => 'lisa.anderson@tenant.test',
                'username' => 'lisaanderson',
                'password' => Hash::make('password123'),
                'role' => 'manager',
                'department' => 'Human Resources',
                'position' => 'HR Manager'
            ],
            [
                'name' => 'Robert Taylor',
                'email' => 'robert.taylor@tenant.test',
                'username' => 'roberttaylor',
                'password' => Hash::make('password123'),
                'role' => 'viewer',
                'department' => 'Finance',
                'position' => 'Financial Analyst'
            ]
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role'], $userData['department'], $userData['position']);
            
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            
            // Assign role
            $user->assignRole($role);
            
            $this->command->info("   ✅ Created user: {$user->name} ({$role})");
        }
    }

    private function createProjects()
    {
        $this->command->info('📝 Creating sample projects...');

        $projects = [
            [
                'name' => 'E-Commerce Platform Redesign',
                'description' => 'Complete redesign of the e-commerce platform with modern UI/UX',
                'status' => 'in_progress',
                'priority' => 'high',
                'start_date' => now()->subDays(30),
                'end_date' => now()->addDays(60),
                'budget' => 50000.00,
                'manager_id' => User::where('username', 'manager')->first()?->id ?? 1
            ],
            [
                'name' => 'Mobile App Development',
                'description' => 'Native iOS and Android application for customer engagement',
                'status' => 'in_progress',
                'priority' => 'medium',
                'start_date' => now()->subDays(15),
                'end_date' => now()->addDays(90),
                'budget' => 75000.00,
                'manager_id' => User::where('role', 'manager')->first()?->id ?? 1
            ],
            [
                'name' => 'Data Migration Project',
                'description' => 'Migration of legacy data to the new system architecture',
                'status' => 'completed',
                'priority' => 'high',
                'start_date' => now()->subDays(90),
                'end_date' => now()->subDays(30),
                'budget' => 25000.00,
                'manager_id' => User::where('role', 'manager')->first()?->id ?? 1
            ],
            [
                'name' => 'Security Audit & Implementation',
                'description' => 'Comprehensive security audit and implementation of recommended measures',
                'status' => 'planning',
                'priority' => 'critical',
                'start_date' => now()->addDays(7),
                'end_date' => now()->addDays(45),
                'budget' => 35000.00,
                'manager_id' => User::where('role', 'admin')->first()?->id ?? 1
            ],
            [
                'name' => 'Customer Portal Enhancement',
                'description' => 'Enhanced customer self-service portal with advanced features',
                'status' => 'on_hold',
                'priority' => 'low',
                'start_date' => now()->addDays(30),
                'end_date' => now()->addDays(120),
                'budget' => 40000.00,
                'manager_id' => User::where('role', 'manager')->first()?->id ?? 1
            ]
        ];

        foreach ($projects as $projectData) {
            DB::table('projects')->updateOrInsert(
                ['name' => $projectData['name']],
                $projectData
            );
            
            $this->command->info("   ✅ Created project: {$projectData['name']}");
        }
    }

    private function createTasks()
    {
        $this->command->info('📋 Creating sample tasks...');

        $projects = DB::table('projects')->pluck('id', 'name');
        $users = User::all()->keyBy('id');

        $tasks = [
            [
                'title' => 'Design Landing Page',
                'description' => 'Create modern and responsive landing page design',
                'status' => 'completed',
                'priority' => 'high',
                'project_id' => $projects['E-Commerce Platform Redesign'] ?? 1,
                'assigned_to' => $users->where('username', 'mikewilson')->first()?->id ?? 1,
                'due_date' => now()->subDays(5)
            ],
            [
                'title' => 'API Development',
                'description' => 'Develop REST API endpoints for user management',
                'status' => 'in_progress',
                'priority' => 'medium',
                'project_id' => $projects['Mobile App Development'] ?? 1,
                'assigned_to' => $users->where('username', 'mikewilson')->first()?->id ?? 1,
                'due_date' => now()->addDays(15)
            ],
            [
                'title' => 'Database Schema Design',
                'description' => 'Design database schema for customer portal',
                'status' => 'todo',
                'priority' => 'high',
                'project_id' => $projects['Customer Portal Enhancement'] ?? 1,
                'assigned_to' => $users->where('username', 'emmadavis')->first()?->id ?? 1,
                'due_date' => now()->addDays(10)
            ],
            [
                'title' => 'Security Vulnerability Scan',
                'description' => 'Perform comprehensive security vulnerability scan',
                'status' => 'in_progress',
                'priority' => 'critical',
                'project_id' => $projects['Security Audit & Implementation'] ?? 1,
                'assigned_to' => $users->where('username', 'superadmin')->first()?->id ?? 1,
                'due_date' => now()->addDays(3)
            ]
        ];

        foreach ($tasks as $taskData) {
            DB::table('tasks')->updateOrInsert(
                ['title' => $taskData['title'], 'project_id' => $taskData['project_id']],
                array_merge($taskData, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
            
            $this->command->info("   ✅ Created task: {$taskData['title']}");
        }
    }

    private function createOrganizations()
    {
        $this->command->info('🏢 Creating sample organizations...');

        $organizations = [
            [
                'name' => 'TechCorp Solutions',
                'description' => 'Leading technology solutions provider',
                'type' => 'company',
                'industry' => 'Technology',
                'size' => 'medium',
                'website' => 'https://techcorp.example.com',
                'contact_email' => 'contact@techcorp.example.com',
                'phone' => '+1-555-0100',
                'address' => '123 Tech Street, Silicon Valley, CA 94000'
            ],
            [
                'name' => 'InnovateLab Inc',
                'description' => 'Innovation and research laboratory',
                'type' => 'research_institute',
                'industry' => 'Research',
                'size' => 'small',
                'website' => 'https://innovatelab.example.com',
                'contact_email' => 'info@innovatelab.example.com',
                'phone' => '+1-555-0101',
                'address' => '456 Innovation Avenue, Research Park, CA 94001'
            ],
            [
                'name' => 'Global Enterprises',
                'description' => 'Multinational enterprise group',
                'type' => 'enterprise',
                'industry' => 'Consulting',
                'size' => 'large',
                'website' => 'https://globalenterprises.example.com',
                'contact_email' => 'contact@globalenterprises.example.com',
                'phone' => '+1-555-0102',
                'address' => '789 Corporate Blvd, Financial District, NY 10000'
            ]
        ];

        foreach ($organizations as $orgData) {
            DB::table('organizations')->updateOrInsert(
                ['name' => $orgData['name']],
                array_merge($orgData, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
            
            $this->command->info("   ✅ Created organization: {$orgData['name']}");
        }
    }

    private function createDepartments()
    {
        $this->command->info('🏛️ Creating sample departments...');

        $departments = [
            [
                'name' => 'Engineering',
                'description' => 'Software development and technical operations',
                'head_id' => User::where('role', 'manager')->first()?->id ?? 1,
                'budget' => 150000.00,
                'location' => 'Building A, Floor 2'
            ],
            [
                'name' => 'Marketing',
                'description' => 'Brand management and customer acquisition',
                'head_id' => User::where('username', 'emmadavis')->first()?->id ?? 1,
                'budget' => 80000.00,
                'location' => 'Building B, Floor 1'
            ],
            [
                'name' => 'Sales',
                'description' => 'Customer sales and business development',
                'head_id' => User::where('username', 'davidbrown')->first()?->id ?? 1,
                'budget' => 120000.00,
                'location' => 'Building C, Floor 1'
            ],
            [
                'name' => 'Human Resources',
                'description' => 'Employee management and organizational development',
                'head_id' => User::where('role', 'manager')->first()?->id ?? 1,
                'budget' => 60000.00,
                'location' => 'Building D, Floor 1'
            ],
            [
                'name' => 'Finance',
                'description' => 'Financial planning and accounting operations',
                'head_id' => User::where('username', 'roberttaylor')->first()?->id ?? 1,
                'budget' => 70000.00,
                'location' => 'Building E, Floor 2'
            ]
        ];

        foreach ($departments as $deptData) {
            DB::table('departments')->updateOrInsert(
                ['name' => $deptData['name']],
                array_merge($deptData, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
            
            $this->command->info("      ✅ Created department: {$deptData['name']}");
        }
    }

    private function createTeams()
    {
        $this->command->info('👥 Creating sample teams...');

        $teams = [
            [
                'name' => 'Frontend Development',
                'description' => 'Frontend UI/UX development team',
                'department_id' => DB::table('departments')->where('name', 'Engineering')->first()?->id ?? 1,
                'lead_id' => User::where('username', 'mikewilson')->first()?->id ?? 1,
                'size' => 5
            ],
            [
                'name' => 'Backend Development',
                'description' => 'Backend API and database team',
                'department_id' => DB::table('departments')->where('name', 'Engineering')->first()?->id ?? 1,
                'lead_id' => User::where('username', 'mikewilson')->first()?->id ?? 1,
                'size' => 4
            ],
            [
                'name' => 'Digital Marketing',
                'description' => 'Online marketing and SEO team',
                'department_id' => DB::table('departments')->where('name', 'Marketing')->first()?->id ?? 1,
                'lead_id' => User::where('username', 'emmadavis')->first()?->id ?? 1,
                'size' => 3
            ],
            [
                'name' => 'Sales Operations',
                'description' => 'Sales process and customer relations team',
                'department_id' => DB::table('departments')->where('name', 'Sales')->first()?->id ?? 1,
                'lead_id' => User::where('username', 'davidbrown')->first()?->id ?? 1,
                'size' => 6
            ]
        ];

        foreach ($teams as $teamData) {
            DB::table('teams')->updateOrInsert(
                ['name' => $teamData['name']],
                array_merge($teamData, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
            
            $this->command->info("   ✅ Created team: {$teamData['name']}");
        }
    }

    private function createMeetings()
    {
        $this->command->info('📅 Creating sample meetings...');

        $meetings = [
            [
                'title' => 'Team Standup',
                'description' => 'Daily team standup meeting',
                'type' => 'recurring',
                'frequency' => 'daily',
                'start_time' => now()->addDay()->setTime(9, 0),
                'end_time' => now()->addDay()->setTime(9, 30),
                'location' => 'Conference Room A',
                'organizer_id' => User::where('role', 'manager')->first()?->id ?? 1
            ],
            [
                'title' => 'Project Review Meeting',
                'description' => 'Weekly project status review',
                'type' => 'single',
                'start_time' => now()->addDays(2)->setTime(14, 0),
                'end_time' => now()->addDays(2)->setTime(15, 0),
                'location' => 'Conference Room B',
                'organizer_id' => User::where('role', 'admin')->first()?->id ?? 1
            ],
            [
                'title' => 'Quarterly Planning Session',
                'description' => 'Q1 planning and goal setting meeting',
                'type' => 'single',
                'start_time' => now()->addDays(7)->setTime(10, 0),
                'end_time' => now()->addDays(7)->setTime(16, 0),
                'location' => 'Executive Conference Room',
                'organizer_id' => User::where('username', 'johnsmith')->first()?->id ?? 1
            ]
        ];

        foreach ($meetings as $meetingData) {
            DB::table('meetings')->updateOrInsert(
                ['title' => $meetingData['title'], 'start_time' => $meetingData['start_time']],
                array_merge($meetingData, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
            
            $this->command->info("   ✅ Created meeting: {$meetingData['title']}");
        }
    }

    private function createDocuments()
    {
        $this->command->info('📄 Creating sample documents...');

        $documents = [
            [
                'title' => 'Project Charter - E-Commerce Platform',
                'description' => 'Project charter document outlining scope and objectives',
                'type' => 'pdf',
                'size' => 2048576, // 2MB
                'author_id' => User::where('role', 'admin')->first()?->id ?? 1,
                'category' => 'project_documents'
            ],
            [
                'title' => 'API Documentation v2.1',
                'description' => 'Complete API documentation for developers',
                'type' => 'markdown',
                'size' => 1024000, // 1MB
                'author_id' => User::where('username', 'mikewilson')->first()?->id ?? 1,
                'category' => 'technical'
            ],
            [
                'title' => 'Employee Handbook 2024',
                'description' => 'Updated employee handbook with policies and procedures',
                'type' => 'pdf',
                'size' => 5242880, // 5MB
                'author_id' => User::where('role', 'manager')->first()?->id ?? 1,
                'category' => 'hr_documents'
            ],
            [
                'title' => 'Budget Report Q4',
                'description' => 'Quarterly budget and financial report',
                'type' => 'excel',
                'size' => 512000, // 512KB
                'author_id' => User::where('username', 'roberttaylor')->first()?->id ?? 1,
                'category' => 'financial'
            ]
        ];

        foreach ($documents as $docData) {
            DB::table('documents')->updateOrInsert(
                ['title' => $docData['title']],
                array_merge($docData, [
                    'file_path' => 'documents/' . strtolower(str_replace(' ', '_', $docData['title'])) . '.' . $docData['type'],
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
            
            $this->command->info("   ✅ Created document: {$docData['title']}");
        }
    }

    private function createNotifications()
    {
        $this->command->info('🔔 Creating sample notifications...');

        $notifications = [
            [
                'title' => 'Welcome to the Team!',
                'message' => 'Welcome to our organization. Please complete your profile setup.',
                'type' => 'welcome',
                'priority' => 'high',
                'user_id' => User::where('username', 'emmadavis')->first()?->id ?? 1,
                'is_read' => false
            ],
            [
                'title' => 'Project Deadline Approaching',
                'message' => 'Mobile App Development project deadline is approaching in 2 weeks.',
                'type' => 'deadline',
                'priority' => 'medium',
                'user_id' => User::where('role', 'manager')->first()?->id ?? 1,
                'is_read' => false
            ],
            [
                'title' => 'Meeting Scheduled',
                'message' => 'Team Standup meeting is scheduled for tomorrow at 9:00 AM.',
                'type' => 'meeting',
                'priority' => 'low',
                'user_id' => User::where('username', 'mikewilson')->first()?->id ?? 1,
                'is_read' => false
            ],
            [
                'title' => 'System Maintenance Notice',
                'message' => 'Scheduled system maintenance will occur this weekend from 2 AM to 4 AM.',
                'type' => 'system',
                'priority' => 'medium',
                'user_id' => User::where('username', 'superadmin')->first()?->id ?? 1,
                'is_read' => false
            ]
        ];

        foreach ($notifications as $noteData) {
            DB::table('notifications')->updateOrInsert(
                ['title' => $noteData['title'], 'user_id' => $noteData['user_id']],
                array_merge($noteData, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
            
            $this->command->info("   ✅ Created notification: {$noteData['title']}");
        }
    }
}

