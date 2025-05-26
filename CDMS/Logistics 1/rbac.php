<?php
// rbac.php

$role_modules = [
    'Procurement Officer' => ['procurement'],
    'Asset Manager' => ['asset_management'],
    'Fleet Officer' => ['fleet_management'],
    'Project Manager' => ['project_management'],
    'Admin' => ['procurement', 'asset_management', 'fleet_management', 'project_management', 'user_management']
];
?>
