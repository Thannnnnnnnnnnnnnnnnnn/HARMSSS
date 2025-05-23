<?php
require_once __DIR__ . '/../../Database.php';  // Adjust as needed, __DIR__ = folder of current file
require_once __DIR__ . '/../../functions.php';
$config = require __DIR__ . '/../../config.php';

$db = new Database($config['database']);
$conn = $db->pdo; // get the PDO connection
?>
