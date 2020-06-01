<?php
/*
 *  PDO DATABASE CLASS
 */

if (is_dir("config")) {

    require_once 'config/config.php';

} else {

    require_once '../config/config.php';

}

class Database
{
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $connection;
    private $error;
    private $stmt;
    private $dbconnected = false;

    public function __construct()
    {

        // Set PDO Connection
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        );

        // Create a new PDO instanace
        try {
            $this->connection = new PDO($dsn, $this->user, $this->pass, $options);
            $this->dbconnected = true;

        } // Catch any errors
         catch (PDOException $e) {
            $this->error = $e->getMessage() . PHP_EOL;
        }
    }

    //Get the Error Message
    public function getError()
    {
        return $this->error;
    }

    public function isConnected()
    {
        return $this->dbconnected;
    }

    // Prepare statement with query
    public function query($query)
    {
        $this->stmt = $this->connection->prepare($query);
    }

    // Execute the prepared statement
    public function execute()
    {
        return $this->stmt->execute();
    }

    // Get result set as array of objects
    public function resultset()
    {
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Get record row count
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

    // Get single record as object
    public function single()
    {
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    // Bind values
    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }
}
