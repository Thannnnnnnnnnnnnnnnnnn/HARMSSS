<?php

class Database
{
    public $pdo;
    public function __construct($config, $username = '3206_CENTRALIZED_DATABASE', $password = '4562526')
    {

        // required for the PDO instance. this holds the information of the database itself
<<<<<<< HEAD
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname=" . $config['dbname'] . ";charset={$config['charset']}";

=======
        // $dsn = 'mysql:' . http_build_query($config, '', ';');
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4";
>>>>>>> ab4a17a04a52b08c09ca7f1ca12fb1653079ffa5
        // $dsn = "mysql:host=localhost;port=3306;dbname=post";

        $this->pdo = new pdo($dsn, $username, $password, [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }
    // this prepare and execute the query then returns the result.
    public function query($query, $params = [])
    {
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);

        return $statement;
    }
}
