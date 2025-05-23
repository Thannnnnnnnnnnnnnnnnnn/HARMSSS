<?php
// Role-Based Access Control (RBAC) System

// Define permissions for each role
$role_permissions = [
    'super admin' => [
        'user_management' => ['view', 'add', 'edit', 'delete'],
        'awards' => ['view', 'add', 'edit', 'delete'],
        'recognitions' => ['view', 'add', 'edit', 'delete'],
        'categories' => ['view', 'add', 'edit', 'delete'],
        'audit_trail' => ['view'],
        'log_history' => ['view'],
        'transactions' => ['view']
    ],
    'admin' => [
        'user_management' => ['view', 'add', 'edit', 'delete'],
        'awards' => ['view', 'add', 'edit', 'delete'],
        'recognitions' => ['view', 'add', 'edit', 'delete'],
        'categories' => ['view', 'add', 'edit', 'delete'],
        'audit_trail' => ['view'],
        'log_history' => ['view'],
        'transactions' => ['view']
    ],
    'manager' => [
        'user_management' => ['view', 'add', 'edit'],
        'awards' => ['view', 'add', 'edit'],
        'recognitions' => ['view', 'add', 'edit'],
        'categories' => ['view', 'add', 'edit'],
        'audit_trail' => ['view'],
        'log_history' => ['view'],
        'transactions' => ['view']
    ],
    'staff' => [
        'awards' => ['view','add'],
        'recognitions' => ['view','add'],
        'categories' => ['view','add']
    ]
];

/**
 * Check if a user has permission to perform an action
 * 
 * @param string $module Module to check permission for
 * @param string $action Action to check permission for
 * @param string $role User's role
 * @return bool True if user has permission, false otherwise
 */
function hasPermission($module, $action, $role) {
    global $role_permissions;
    
    // Convert role to lowercase for case-insensitive comparison
    $role = strtolower($role);
    
    // Check if role exists
    if (!isset($role_permissions[$role])) {
        return false;
    }
    
    // Check if module exists for role
    if (!isset($role_permissions[$role][$module])) {
        return false;
    }
    
    // Check if action is allowed for module
    return in_array($action, $role_permissions[$role][$module]);
}

/**
 * Check if current user has permission to perform an action
 * 
 * @param string $module Module to check permission for
 * @param string $action Action to check permission for
 * @return bool True if user has permission, false otherwise
 */
function checkPermission($module, $action) {
    if (!isset($_SESSION['role'])) {
        return false;
    }
    
    return hasPermission($module, $action, $_SESSION['role']);
}

/**
 * Require permission for current user
 * If user doesn't have permission, redirect to error page
 * 
 * @param string $module Module to check permission for
 * @param string $action Action to check permission for
 */
function requirePermission($module, $action) {
    if (!checkPermission($module, $action)) {
        echo "<script>
            Swal.fire({
                title: 'Access Denied',
                text: 'You do not have permission to perform this action.',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'finaltemplate.php';
                }
            });
        </script>";
        exit();
    }
}
?> 