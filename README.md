## Practical Excersie
***

![image](https://github.com/andygomezb7/simpleecommerce/assets/9289785/02e11ca6-a288-44cf-a80f-968781f326c9)


## API Integration
***

This task is locate in the api_integration_exercise.php

## SQL Test
***

### Task 2: CRUD Operations
#### Insert a new product with the following details: Product Name: "Laptop", Price: 1000.00,
Stock Quantity: 50.
```
INSERT INTO products (product_name, price, stock_quantity)
VALUES ('Laptop', 1000.00, 50);
```

#### Update the stock quantity of the product with product_id = 3 to 75.
```
UPDATE products
SET stock_quantity = 75
WHERE product_id = 3;
```

#### Delete the order with order_id = 10 and its associated order items.
```
DELETE FROM order_items
WHERE order_id = 10;

DELETE FROM orders
WHERE order_id = 10;
```

#### Retrieve the customer's first and last names who placed the order with order_id = 5.
```
SELECT c.first_name, c.last_name
FROM customers c
JOIN orders o ON c.customer_id = o.customer_id
WHERE o.order_id = 5;
```

#### Calculate the total revenue generated by each product (sum of subtotals from order
items).
```
SELECT p.product_name, SUM(oi.subtotal) AS total_income
FROM products p
JOIN order_items oi ON p.product_id = oi.product_id
GROUP BY p.product_name;
```

## Task 3: Stored Procedures
***

```
DELIMITER //
CREATE PROCEDURE CalculateCustomerRevenue(IN customer_id INT, OUT total_revenue DECIMAL(10, 2))
BEGIN
  SELECT SUM(oi.subtotal)
  INTO total_revenue
  FROM orders o
  JOIN order_items oi ON o.order_id = oi.order_id
  WHERE o.customer_id = customer_id;
END;
//
DELIMITER ;
```
