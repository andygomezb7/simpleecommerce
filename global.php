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

    function getAllOrders()
    {
        $query = "SELECT * FROM orders ORDER BY order_id ASC";
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

	    return $order_id;
	}

	function calculateDiscountedTotal($cart) {
	    $total = 0;
	    
	    foreach ($cart as $item) {
	        $price = $item['price'];
	        $discountPercentage = intval(@$item['discount']);
	        $quantity = $item['quantity'];
	        
	        $discountedPrice = $price - ($price * ($discountPercentage / 100));
	        
	        $total += $discountedPrice * $quantity;
	    }
	    
	    return $total;
	}

	function getTopSellingProducts($orders, $products, $N) {
	    $productSales = array();
	    
	    foreach ($products as $product) {
	        $productSales[$product['product_id']] = 0;
	    }
	    
	    foreach ($orders as $order) {
	        $product_id = $order['product_id'];
	        $quantity = $order['quantity'];
	        $productSales[$product_id] += $quantity;
	    }
	    
	    arsort($productSales);

	    $topProducts = array_slice($productSales, 0, $N);
	    
	    return $topProducts;
	}

	// API INTEGRATION

	// Function to send an HTTP GET request to the API
	function getEmployees() {
	    $url = "http://dummy.restapiexample.com/api/v1/employees";
	    
	    $ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    
	    $response = curl_exec($ch);
	    curl_close($ch);
	    
	    return $response;
	}

	// Function to send an HTTP GET request to retrieve a single employee's data
	function getEmployeeById($id) {
	    $url = "http://dummy.restapiexample.com/api/v1/employee/$id";
	    
	    $ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    
	    $response = curl_exec($ch);
	    curl_close($ch);
	    
	    return $response;
	}

	// Function to send an HTTP POST request to create a new record
	function createEmployee($data) {
	    $url = "http://dummy.restapiexample.com/api/v1/create";
	    
	    $ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	    
	    // Set headers to specify JSON content
	    $headers = array('Content-Type: application/json');
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    
	    $response = curl_exec($ch);
	    curl_close($ch);
	    
	    return $response;
	}

	function countHighEarningEmployees($salaryThreshold) {
	    $employees = json_decode(getEmployees(), true);
	    
	    $count = 0;
	    foreach ($employees as $employee) {
	        if (isset($employee['employee_salary']) && (floatval($employee['employee_salary']) > $salaryThreshold)) {
	            $count++;
	        }
	    }
	    
	    return $count;
	}

	function getUserIdByName($name) {
	    $employees = json_decode(getEmployees(), true);
	    
	    foreach ($employees as $employee) {
	        if (isset($employee['employee_name']) && $employee['employee_name'] === $name) {
	            return $employee['id'];
	        }
	    }
	    
	    return null;
	}

}
?>
