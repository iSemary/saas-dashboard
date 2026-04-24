<?php

return [
    'name' => 'Ticket',
    'enabled' => true,
    'description' => 'Ticket management system for customer support',
    'version' => '1.0.0',
    'author' => 'SaaS Dashboard',
    
    // Ticket configuration
    'default_status' => 'open',
    'auto_assign' => false,
    'allow_attachments' => true,
    'max_attachment_size' => 10240, // 10MB in KB
    'allowed_attachment_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt'],
    
    // Notification settings
    'notify_on_create' => true,
    'notify_on_update' => true,
    'notify_on_close' => true,
    
    // Priority levels
    'priorities' => [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent'
    ],
    
    // Status options
    'statuses' => [
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'pending' => 'Pending',
        'resolved' => 'Resolved',
        'closed' => 'Closed'
    ]
];
