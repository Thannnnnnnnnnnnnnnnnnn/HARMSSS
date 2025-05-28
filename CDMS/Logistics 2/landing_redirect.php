<?php
session_start();
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die;
}

// dd($_SESSION); // Debugging: Check session data

$role = $_SESSION['Role'];  
$permissions = include 'role_permissions.php';
$allowed_modules = $permissions[$role] ?? [];

// Choose default landing page based on first allowed module
$module_to_landing = [
    'vendor_portal' => '../Logistics 2/vendor portal/vendors.php',
    'audit_management' => '../Logistics 2/Audit management/Audit.php',
    'vehicle_reservation' => '../Logistics 2/Vehicle reservation/reservation.php',
    'fleet_management' => '../Logistics 2/Fleet management/fleet.php',
    // Add more mappings as needed
];

// Find the first module the role is allowed to access
foreach ($allowed_modules as $module) {
    if (($module_to_landing[$module])) {
        header("Location: " . $module_to_landing[$module]);
        exit;
    }
}
// dd('debug');
