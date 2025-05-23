<?php
// Helper functions for audit trail department and module mapping

function getDepartmentAffectedName($department_id) {
    // Map department_id to department_affected string as per ENUM values in DB
    $mapping = [
        1 => 'HR part 1 - 2',
        2 => 'HR part 3 - 4',
        3 => 'Core 1',
        4 => 'Core 2',
        5 => 'Core 3',
        6 => 'Log1',
        7 => 'Log2',
        8 => 'financials',
        9 => 'User Management',
        // Add other mappings as needed
    ];

    return $mapping[$department_id] ?? 'Unknown Department';
}

function getModuleAffectedName($module_key) {
    // Map module keys to module_affected string as per ENUM values in DB
    $mapping = [
        'user_management' => 'user management',
        'awards' => 'social recognition',
        'recognitions' => 'social recognition',
        'categories' => 'social recognition',
        'login' => 'login',
        // Add other mappings as needed
    ];

    return $mapping[$module_key] ?? 'performance management'; // Default fallback
}
?>
