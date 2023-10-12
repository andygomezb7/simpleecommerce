<?php
class GlobalClass
{
    private $host = "localhost";
    private $user = "root";
    private $password = "";
    private $database = "pacifiko";
    private $conn;

    function __construct()
    {
        $this->conn = $this->connectDB();
    }

    function connectDB()
    {
        try {
            $conn = new mysqli($this->host, $this->user, $this->password, $this->database);;
            return $conn;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    function getProductByCode($code)
    {
        $query = "SELECT * FROM products WHERE code = '$code'";
        $stmt = $this->conn->query($query);

        return $stmt->fetch_assoc();
    }

    function getAllProducts($search = "")
    {
    	$search = ($search) ? " WHERE product_name LIKE '%$search%'" : "";
        $query = "SELECT * FROM products $search ORDER BY product_id ASC";
	    $stmt  = $this->conn->query($query);
        return $stmt;
    }
}
?>
