<?php

$databases = [

    // Core Transaction 1
    "cr1_food_and_beverage_costing",
    "cr1_inventory_management",
    "cr1_kitchen/bar_module",
    "cr1_order_management_with_pos",
    "cr1_restaurant_analytics",

  
];

$connections = []; 

foreach ($databases as $db) {
    $connection = mysqli_connect("127.0.0.1", "root", "", $db);
    if (!$connection) {
        die("Connection failed for $db: " . mysqli_connect_error());
    }else{
    }
    $connections[$db] = $connection;
}

?>
