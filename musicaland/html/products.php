<?php 
session_start();

$db = mysqli_connect('localhost', 'root', '', 'musicaland');

if (!$db) {
    die('Could not connect: ' . mysql_error());
}

$sql = "select * from products";

if(isset($_GET["action"]))
{
	if($_GET["action"] == "delete") {
		foreach($_SESSION["shopping_cart"] as $keys => $values)
		{
			if($values["item_id"] == $_GET["id"])
			{
				unset($_SESSION["shopping_cart"][$keys]);
				echo '<script>alert("Item Removed")</script>';
				echo '<script>window.location="products.php"</script>';
			}
		}
	}

	if($_GET["action"] == "add") {
		if(isset($_POST["add_to_cart"])) {
			if(isset($_SESSION["shopping_cart"])){
				$item_array_id = array_column($_SESSION["shopping_cart"], "item_id");
				if(!in_array($_GET["id"], $item_array_id))
				{
					$count = count($_SESSION["shopping_cart"]);
					$item_array = array(
						'item_id'			=>	$_GET["id"],
						'item_name'			=>	$_POST["hidden_name"],
						'item_price'		=>	$_POST["hidden_price"],
						'item_quantity'		=>	$_POST["quantity"]
					);
					$_SESSION["shopping_cart"][$count] = $item_array;
				}
				else
				{
					echo '<script>alert("Item Already Added")</script>';
				}
			}
			else{
				$item_array = array(
					'item_id'			=>	$_GET["id"],
					'item_name'			=>	$_POST["hidden_name"],
					'item_price'		=>	$_POST["hidden_price"],
					'item_quantity'		=>	$_POST["quantity"]
				);
				$_SESSION["shopping_cart"][0] = $item_array;
			}
		}
	}
}

if(isset($_POST["checkout"]))
{
	$id = $_SESSION['id'];
	$sql_order ="insert into orders(user_id) values('$id')";
	$result=$db->query($sql_order);
	if (!$result) {
		trigger_error('Invalid query: ' . $db->error);
	}
	$order_id = $db->insert_id;
	foreach($_SESSION["shopping_cart"] as $keys => $values)
	{
		$prod_id = $values["item_id"];
		$quantity = $values["item_quantity"];
		$query = "insert into order_line(order_id,product_id,quantity) values('$order_id','$prod_id','$quantity')";
		$result=$db->query($query);
		if (!$result) {
			trigger_error('Invalid query: ' . $db->error);
		}
	}
	unset($_SESSION["shopping_cart"]);
	echo '<script>alert("Order Completed Successfully !")</script>';
	header('Location: products.php');
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products page</title>
    <link rel="stylesheet" href="../css/products.css">

</head>
<body>
<nav class="header">
        <input type="checkbox" id="nav-check">
        <h1 class="logo"><a href="../index.html">MUSICALAND</a></h1>
        <div class="nav-btn">
            <label for="nav-check">
              <span></span>
              <span></span>
              <span></span>
            </label>
        </div>
        <ul class="main-nav">
            <li><a href="../index.html">Home</a></li>
            <li class="active"><a href="../index.html#pop">Products</a></li>
            <li  ><a href="review.html">Write A Review</a></li>
			<li  ><a href="orders.php">Orders</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <h5 class="ourproducts">Our products</h5>
    <br>
    <div class="product-container">
    <?php
        $result = $db->query($sql);
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                ?>
                <div class="product-item">
                    <form action="products.php?action=add&id=<?php echo $row["id"]; ?>" method="post">
						<div class="img" style="position:relative; width:max-content">
						<input type="number" name="quantity" value="1" class="form-control" />
                            <button class="add-to" type="submit" name="add_to_cart">
                                <img src="../Assets/plus.png" width="10" alt="">
                            </button>
                            <?php echo '<img src="../Assets/products/'.$row["picture"].'" width="280"; margin:0 20px">';?>
                        </div> 
                        <?php
                        echo '<h5>'.$row["name"].'</h5>';
                        echo '<h4>'.$row["price"].'$</h4>';
                        ?>
                        <input type="hidden" name="hidden_name" value="<?php echo $row["name"]; ?>" />
                        <input type="hidden" name="hidden_price" value="<?php echo $row["price"]; ?>" />
                    </form>
                </div> 
        <?php
            }
        } else {
            echo "<h5>No products available<h5>";
        }
    ?>
    </div>


    <div class="orders">
		<?php if(!empty($_SESSION["shopping_cart"])){ ?>
			<div> <h5 class="ourproducts">Order Details</h5></div>
			<div>
				<table>
					<thead>
						<tr>
							<th>Item Name</th>
							<th>Quantity</th>
							<th>Price</th>
							<th>Total</th>
							<th>Action</th>
						</tr>
					</thead>
					<?php
						$total = 0;
						foreach($_SESSION["shopping_cart"] as $keys => $values)
						{
					?>
					<tbody>
					<tr>
						<td><?php echo $values["item_name"]; ?></td>
						<td><?php echo $values["item_quantity"]; ?></td>
						<td>$ <?php echo $values["item_price"]; ?></td>
						<td>$ <?php echo number_format($values["item_quantity"] * $values["item_price"], 2);?></td>
						<td><a href="products.php?action=delete&id=<?php echo $values["item_id"]; ?>"><span>Remove</span></a></td>
					</tr>
					<?php
							$total = $total + ($values["item_quantity"] * $values["item_price"]);
						}
					?>
					<tr>
						<td colspan="3" align="right">Total</td>
						<td align="right">$ <?php echo number_format($total, 2); ?></td>
						<td align="right">
							<form action="products.php" method="post">
								<button type="submit" name="checkout" value="checkout">Checkout</button>
							</form>
						</td>
						
					</tr>
					<?php
					}
					?>
</tbody>
				</table>
			</div>
		</div>
	</div>
	<br />

</body>
</html>