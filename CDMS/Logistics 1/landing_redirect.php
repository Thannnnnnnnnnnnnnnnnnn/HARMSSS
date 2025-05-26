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
    'procurement' => '../Logistics 1/Procurement/purchase_request.php',
    'warehousing' => '../Logistics 1/Warehousing/warehouses.php',
    'project_management' => '../Logistics 1/PM/projects.php',
    'asset_management' => '../Logistics 1/Asset management/add_asset.php',
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
// Fallback if nothing matches
header("Location: ../Logistics 1/dashboard.php");
exit;
