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

    function getAllCustomers()
    {
        $query = "SELECT * FROM customers ORDER BY customer_id ASC";
	    $stmt  = $this->conn->query($query);
        return $stmt;
    }

    function addOrderWithItems($orderData, $orderItemsData) {
	    $stmt = $this->conn->query("INSERT INTO orders (customer_id, order_date) VALUES (".$orderData['customer'].", '".date('Y-m-d')."')");

	    // Obtener el ID de la orden recién creada
	    $order_id = $this->conn->insert_id;

	    // Insertar elementos de orden en la tabla "order_items"
	    foreach ($orderItemsData as $item) {
	    	$id = filter_var($item['id'], FILTER_SANITIZE_STRING);
	    	$quantity = filter_var($item['quantity'], FILTER_SANITIZE_STRING);
	    	$subtotal = filter_var($item['subtotal'], FILTER_SANITIZE_STRING);
	        $stmt = $this->conn->query("INSERT INTO orderitems (order_id, product_id, quantity, subtotal) VALUES (".$order_id.", '".$id."', '".$quantity."', '".$subtotal."')");
	    }

	    return $order_id; // Devuelve el ID de la orden creada
	}
}
?>
