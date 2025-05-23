<?php
class Database {
    private $host = "127.0.0.1";
    private $user = "3206_CENTRALIZED_DATABASE";
    private $pass = "4562526";
    private $connections = [];

    private $databases = [
        "costing" => "cr1_food_and_beverage_costing",
        "inventory" => "cr1_inventory_management",
        "kitchen" => "cr1_kitchen_bar_module",
        "orders" => "cr1_order_management_with_pos",
        "analytics" => "cr1_restaurant_analytics",
        "usm" => "cr1_usm"
    ];

    public function connect($key) {
        if (!isset($this->databases[$key])) {
            die("❌ Unknown database key: '$key'");
        }

        $dbname = $this->databases[$key];

        if (!isset($this->connections[$key])) {
            $conn = new mysqli($this->host, $this->user, $this->pass, $dbname);
            if ($conn->connect_error) {
                die("❌ Connection to $dbname failed: " . $conn->connect_error);
            }
            $this->connections[$key] = $conn;
        }

        return $this->connections[$key];
    }
}
?>