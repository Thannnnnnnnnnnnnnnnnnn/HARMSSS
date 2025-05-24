<?php
class Database {
    private $host = "127.0.0.1";
    private $user = "3206_CENTRALIZED_DATABASE";
    private $pass = "4562526";
    private $conn;
    
    public function connect($dbname) {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $dbname);

        if ($this->conn->connect_error) {
            die("Connection to $dbname failed: " . $this->conn->connect_error);
        }
        return $this->conn; 
    }
    
}
?>
