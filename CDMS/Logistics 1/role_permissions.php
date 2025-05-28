<?php
// role_permissions.php

return [
    // Full access
    'admin' => [
        'procurement',
        'asset_management',
        'warehousing',
        'project_management',
    ],

    // Individual role permissions
    'Procurement officer' => [
        'procurement',
    ],

    'Asset facilitator' => [
        'asset_management',
    ],

    'Warehouse manager' => [
        'warehousing',
    ],

    'Project manager' => [
        'project_management',
    ],

    // Add more roles below if needed
    // 'new_role' => ['module_name'],
];
