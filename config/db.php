<?php
class Database {
    // DB Params
    private $host = 'localhost'; // Localhost Credentials
    private $db_name = 'chris_emma_blog'; // Localhost Credentials
    private $username = 'root'; // Localhost Credentials
    private $password = 'mysql'; // Localhost Credentials

    // private $host = 'mysql.chrisandemmashow.com'; // Live Server Credentials
    // private $db_name = 'chris_emma_blog'; // Live Server Credentials
    // private $username = 'architect11'; // Live Server Credentials
    // private $password = '{ReowReow11}'; // Live Server Credentials
    
    private $conn;

    // DB Connect
    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name, 
                $this->username, 
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            // STOP! Return JSON error so React knows the truth.
            header("Access-Control-Allow-Origin: *");
            header("Content-Type: application/json");
            echo json_encode(array(
                "message" => "Database Connection Failed", 
                "detail" => $e->getMessage()
            ));
            exit(); 
        }

        return $this->conn;
    }
}
?>