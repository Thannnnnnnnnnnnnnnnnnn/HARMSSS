<?php
// role_permissions.php

return [
    // Full access
    'admin' => [
        'vendor_portal',
        'audit_management',
        'fleet_management',
        'vehicle_reservation',
        'Document_tracking'
    ],

    // Individual role permissions
    'Vendor coordinator' => [
        'vendor_portal',
    ],

    'Audit manager' => [
        'audit_management',
    ],

    'Fleet superviser' => [
        'fleet_management',
    ],

    'Reservation officer' => [
        'vehicle_reservation',
    ],

    'Document approvals' => [
        'Document_tracking',
    ],

    // Add more roles below if needed
    // 'new_role' => ['module_name'],
];
