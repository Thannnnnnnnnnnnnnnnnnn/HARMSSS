<?php

class Database
{
    public $pdo;
    public function __construct($config, $username = 'root', $password = '')
    {

        // required for the PDO instance. this holds the information of the database itself
        $dsn = 'mysql:' . http_build_query($config, '', ';');

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

?>

<?php

// for host:3307

// class Database
// {
//     public $pdo;

//     public function __construct($config)
//     {
//         // Safely extract the config details
//         $host = $config['host'] ?? 'localhost';
//         $dbname = $config['dbname'] ?? '';
//         $username = $config['username'] ?? 'root';
//         $password = $config['password'] ?? '';
//         $port = $config['port'] ?? '3307';

//         // Properly build the DSN string
//         $dsn = "mysql:host=$host;port=$port;dbname=$dbname";

//         // Connect to the database
//         $this->pdo = new PDO($dsn, $username, $password, [
//             PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
//             PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//         ]);
//     }

//     public function query($query, $params = [])
//     {
//         $statement = $this->pdo->prepare($query);
//         $statement->execute($params);

//         return $statement;
//     }
// }

?>


