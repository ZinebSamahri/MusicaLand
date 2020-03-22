<?php
    session_start();

    $db = mysqli_connect('localhost', 'root', '', 'musicaland');

    if (!$db) {
        die('Could not connect: ' . mysql_error());
    }
    $user_id = $_SESSION["id"];
    
    $sql1 = "select id from orders where user_id = '$user_id' order by id desc";
    $result1 = $db->query($sql1);


    
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Musicaland | Orders</title>
    <link rel="stylesheet" href="../css/orders.css">
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
            <li><a href="../index.html#pop">Products</a></li>
            <li  ><a href="review.html">Write A Review</a></li>
			<li  class="active" ><a href="orders.php">Orders</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <h5 class="myorders">My orders</h5>
    <?php 
        if($result1)
        {
            while($order = $result1->fetch_assoc())
            {   
                    echo "<table>
                    <thead>
                        <tr>
                            <th>Product name</th>
                            <th>Quantity</th>
                            <th>Price per unit</th>
                        </tr>
                    </thead>
                    <tbody>
                    ";
                    $total = 0;
                    $order_id = $order["id"];
                    $sql2 = "select product_id , quantity from order_line where order_id = '$order_id'";
                    $result2 = $db->query($sql2);
                    while($order_line = $result2->fetch_assoc())
                    {
                        $product_id = $order_line["product_id"];
                        $sql3 = "select name , price , picture from products where id = '$product_id'";
                        $result3 = $db->query($sql3);
                        while($product = $result3->fetch_assoc())
                        {
                            echo "<tr>
                                    <td>".$product['name']."</td>
                                    <td>".$order_line['quantity']."</td>
                                    <td>$".$product['price']."</td>
                                    </tr>
                                ";
                            $total = $total + ($order_line['quantity'] * $product['price']);
                        }
            
                    }
                    echo "<tr>
                            <td colspan='2' align='right'>Total</td>
                            <td align='right'> $".number_format($total, 2)."</td>
                        </tr>
                    </tbody></table>";
                
            }
        }
    ?>
    <br><br><br>
</body>
</html>