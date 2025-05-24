<?php

$databases = [

    // HR Part 1-2
    "hr_1&2_new_hire_onboarding_and_employee_self-service",
    "hr_1&2_performance_management_system",
    "hr_1&2_recruitment_applicant_management",
    "hr_1&2_social_recognition",
    "hr_1&2_succession_planning",
    "hr_1&2_competency_management",
    "hr_1&2_learning_management_and_training_management",
    "hr_1&2_usm",
    "hr34_usm",
    "hr_integrated_db",


    // Logistics 2
    "logs2_document_tracking_system",
    "logs2_fleet_management",
    "logs2_vehicle_reservation_system",
    "logs2_vendor_portal",

    // Core Transaction 1
    // "cr1_inventory_management",
    // "cr1_kitchen_bar_module",
    // "cr1_order_management_with_pos",
    // "cr1_restaurant_analytics",
    "cr1_usm",

    // Core Transaction 2
    // "cr2_front_office",
    // "cr2_housekeeping/laundry_management",
    // "cr2_room_facilities",
    // "cr2_supplier_management",

    // Financial Transactions
    // "fin_accounts_payable",
    // "fin_budget_management",
    // "fin_collection",
    // "fin_disbursement",
    // "fin_general_ledger",

    // User management
    "user_management",
    "fin_usm",
    "logs2_usm",

];

$connections = [];

foreach ($databases as $db) {
    $connection = mysqli_connect("127.0.0.1", "3206_CENTRALIZED_DATABASE", "4562526", $db);
    if (!$connection) {
        die("Connection failed for $db: " . mysqli_connect_error());
    } else {
    }
    $connections[$db] = $connection;
}
