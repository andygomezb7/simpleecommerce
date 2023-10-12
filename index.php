<?php
session_start();
require_once("global.php");

$globalC = new GlobalClass();
$getAction = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$getCode = filter_input(INPUT_GET, 'code', FILTER_SANITIZE_STRING);
$newOrder = filter_input(INPUT_POST, 'newOrder', FILTER_SANITIZE_STRING);
$message = '';

$customerList = $globalC->getAllCustomers();

if (!empty($newOrder)) {

	$orderData = array(
		'customer' => filter_input(INPUT_POST, 'customer', FILTER_SANITIZE_STRING)
	);

	$orderItemsData = $_POST['itemdata'];
	$order_id = $globalC->addOrderWithItems($orderData, $orderItemsData);

	if ($order_id) {
	    $message = "Order created successfully.";
	    unset($_SESSION["cart_item"]);
	} else {
	    $message = "Failed to create the order.";
	}
}

if (!empty($getAction)) {
    switch ($getAction) {
        case "add":
        	$postQuantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_STRING);
            if (!empty($postQuantity)) {
                $productCode = $getCode;
                $product = $globalC->getProductByCode($productCode);

                if ($product) {
                    $itemArray = array(
                        $productCode => array(
                            'id' => $product["product_id"],
                            'name' => $product["product_name"],
                            'code' => $productCode,
                            'quantity' => $postQuantity,
                            'price' => $product["price"],
                            'image' => $product["image"],
                            'discount' => $product['discount']
                        )
                    );

                    if (!empty($_SESSION["cart_item"])) {
                        if (array_key_exists($productCode, $_SESSION["cart_item"])) {
                            $_SESSION["cart_item"][$productCode]["quantity"] += $postQuantity;
                        } else {
                            $_SESSION["cart_item"] = array_merge($_SESSION["cart_item"], $itemArray);
                        }
                    } else {
                        $_SESSION["cart_item"] = $itemArray;
                    }
                }
            }
            break;
        case "remove":
            if (!empty($getCode) && !empty($_SESSION["cart_item"])) {
                $productCode = $getCode;
                unset($_SESSION["cart_item"][$productCode]);
                if (empty($_SESSION["cart_item"])) {
                    unset($_SESSION["cart_item"]);
                }
            }
            break;
        case "empty":
            unset($_SESSION["cart_item"]);
            break;
    }
}

$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);

$pageTitle = 'Products';
include('header.php');
?>
	<!-- Small boxes (Stat box) -->
	<?php echo ($message ? "<div class='alert alert-info mb-3'>$message</div>" : ''); ?>
	<div class="row">
		<div class="col-md-8">
		  <div class="card">
			  <div class="card-header">
			      <div class="card-title"><i class="fas fa-list"></i> <?php echo ($search ? 'Results' : 'Products'); ?></div>
			  </div>
		      <div class="card-body">
			      <table id="product-grid" class="table table-bordered table-hover">
			          <thead>
			              <tr>
			                  <th>Image</th>
			                  <th>Product Name</th>
			                  <th>Price</th>
			                  <th>Quantity</th>
			              </tr>
			          </thead>
			          <tbody>
			              <?php
			              $product_array = $globalC->getAllProducts($search);
			              if (!empty($product_array)) {
			                  foreach ($product_array as $product) {
			              ?>
			                      <tr>
			                          <td>
			                              <img src="<?php echo $product["image"]; ?>" class="cart-item-image" />
			                          </td>
			                          <td><?php echo $product["product_name"]; ?></td>
			                          <td><?php echo "$" . $product["price"]; ?></td>
			                          <td>
			                              <form method="post" action="index.php?action=add&code=<?php echo $product["code"]; ?>">
											  <div class="input-group">
				                                  <input type="number" class="form-control product-quantity" name="quantity" value="1" size="2" required/>
				                                  <button type="submit" class="btn btn-primary btnAddAction"><i class="fas fa-plus"></i> Add to cart</button>
				                              </div>
			                              </form>
			                          </td>
			                      </tr>
			              <?php
			                  }
			              }
			              ?>
			          </tbody>
			      </table>
		      </div>
		  </div>
		</div>

		<div class="col-md-4">
		  <div class="card">
		    <div class="card-header">
		        <div class="card-title">
		            <i class="fas fa-shopping-cart"></i> Shopping Cart
		        </div>
		        <div class="card-tools">
		            <a class="btn btn-danger" href="index.php?action=empty"><i class="fas fa-trash"></i> Empty Cart</a>
		        </div>
		    </div>

		    <form method="post" action="index.php">
			    <div class="card-body">
				    <?php
				    if (isset($_SESSION["cart_item"])) {
				        $total_quantity = 0;
				        $total_price = 0;
	                    $discounted_total = $globalC->calculateDiscountedTotal($_SESSION["cart_item"]);
				    ?>
				        <table id="cart" class="table table-hover">
				            <thead>
				                <tr>
				                    <th>Name</th>
				                    <!-- <th>Code</th> -->
				                    <th class="text-right">Quantity</th>
				                    <th class="text-right">Unit Price</th>
				                    <th class="text-right">Price</th>
				                    <th class="text-center">Remove</th>
				                </tr>
				            </thead>
				            <tbody>
				                <?php
				                foreach ($_SESSION["cart_item"] as $item) {
				                    $item_price = $item["quantity"] * $item["price"];
				                    $item_price = number_format($item_price, 2);
				                ?>
				                    <tr <?php echo ($getCode == $item['code'] ? 'class="blinking"' : ''); ?>>
				                        <td>
				                        	<input type="hidden" name="itemdata[<?php echo $item['code']; ?>][id]" value="<?php echo $item['id']; ?>" />
				                        	<input type="hidden" name="itemdata[<?php echo $item['code']; ?>][quantity]" value="<?php echo $item['quantity']; ?>" />
				                        	<input type="hidden" name="itemdata[<?php echo $item['code']; ?>][subtotal]" value="<?php echo $item_price; ?>" />
				                            <!-- <img src="<?php echo $item["image"]; ?>" class="cart-item-image" /> -->
				                            <?php echo $item["name"]; ?>
				                        </td>
				                        <!-- <td><?php echo $item["code"]; ?></td> -->
				                        <td class="text-right"><?php echo $item["quantity"]; ?></td>
				                        <td class="text-right"><?php echo "$ " . $item["price"]; ?></td>
				                        <td class="text-right"><?php echo "$ " . $item_price; ?></td>
				                        <td class="text-center">
				                            <a href="index.php?action=remove&code=<?php echo $item["code"]; ?>" class="btn btn-danger btnRemoveAction">
				                                <i class="fas fa-trash"></i>
				                            </a>
				                        </td>
				                    </tr>
				                    <?php
				                    $total_quantity += $item["quantity"];
				                    $total_price += ($item["price"] * $item["quantity"]);
				                }
				                ?>
				                <tr>
				                    <td colspan="2" class="text-right">Discount total:</td>
				                    <td class="text-right" colspan="2"><strong><?php echo "$ " . number_format($total_price - $discounted_total, 2); ?></strong></td>
				                    <td></td>
				                </tr>
				                <tr>
				                    <td colspan="1" class="text-right">Total:</td>
				                    <td class="text-right"><?php echo $total_quantity; ?></td>
				                    <td class="text-right" colspan="2"><strong><?php echo "$ " . number_format($discounted_total, 2); ?></strong></td>
				                    <td></td>
				                </tr>
				            </tbody>
				        </table>
				    <?php
				    } else {
				    ?>
				        <div class="no-records">Your Cart is Empty</div>
				    <?php
				    }
				    ?>
				</div>
				<div class="card-footer text-center">
					<div class="input-group">
						<select class="form-control mr-2" name="customer" required>
							<option value="">Select a customer</option>
							<?php
								foreach ($customerList AS $customer) {
									echo '<option value="'.$customer['customer_id'].'">'.$customer['first_name'].' '.$customer['last_name'].'</option>';
								}
							?>
						</select>
		                <button type="submit" value="Checkout" name="newOrder" class="btn btn-lg btn-success"><i class="fas fa-check"></i> Checkout</button>
					</div>
				</div>
			</form>
		  </div>
		</div>

		<div class="col-md-8">
		  <div class="card">
			  <div class="card-header">
			      <div class="card-title"><i class="fas fa-star"></i> Top Selling Products</div>
			  </div>
		      <div class="card-body">
			      <table id="product-grid" class="table table-bordered table-hover">
			          <thead>
			              <tr>
			                  <th>Image</th>
			                  <th>Product Name</th>
			                  <th>Price</th>
			                  <th>Quantity</th>
			              </tr>
			          </thead>
			          <tbody>
			              <?php
			              $orders = $globalC->getAllOrders();
						  $products = $globalC->getAllProducts();

						  $N = 5;
						  $topSellingProducts = $globalC->getTopSellingProducts($orders, $products, $N);
			              if (!empty($product_array)) {
			                  foreach ($product_array as $product) {
			              ?>
			                      <tr>
			                          <td>
			                              <img src="<?php echo $product["image"]; ?>" class="cart-item-image" />
			                          </td>
			                          <td><?php echo $product["product_name"]; ?></td>
			                          <td><?php echo "$" . $product["price"]; ?></td>
			                          <td>
			                              <form method="post" action="index.php?action=add&code=<?php echo $product["code"]; ?>">
											  <div class="input-group">
				                                  <input type="number" class="form-control product-quantity" name="quantity" value="1" size="2" required/>
				                                  <button type="submit" class="btn btn-primary btnAddAction"><i class="fas fa-plus"></i> Add to cart</button>
				                              </div>
			                              </form>
			                          </td>
			                      </tr>
			              <?php
			                  }
			              }
			              ?>
			          </tbody>
			      </table>
		      </div>
		  </div>
		</div>

	</div>
<?php include('footer.php'); ?>