<?php
//Démarrer la session
session_start();

//Redirection vers la page d'acceuil au cas ou le client n'est pas connecté
if(!isset($_SESSION['id']) && empty($_SESSION['id'])) {header('location: ../index.php');exit();}

// Connexion à la base de données
$db = mysqli_connect('localhost', 'id12799307_root', 'rootroot', 'id12799307_musicaland');

//si la connexion échoue
if (!$db) die('Could not connect: ' . mysql_error());

//Recuperer l'id du client
$user_id = $_SESSION["id"];

//Creer la requête de récupération des id des commandes du client
$sql1 = "select id from orders where user_id = '$user_id' order by id desc";
//Executer la requete
$result1 = $db->query($sql1);

//si la variable d'action est définie
//cela signifie qu'une commande a été retiré de la base de données
if(isset($_GET["action"])){
    if($_GET["action"] == "delete") {
        //Supprimer les ordres de la table ligne de commande
        $delete = "delete from order_line where order_id = ".$_GET['id']."";
        $res = mysqli_query($db, $delete);
        // supprimer les ordres de la table commande
        $delete = "delete from orders where id = ".$_GET['id']."";
        $res = mysqli_query($db, $delete);
        header('Location: orders.php');
        exit();

    }
}
        
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
        <h1 class="logo"><a href="../index.php">MUSICALAND</a></h1>
        <div class="nav-btn">
            <label for="nav-check">
              <span></span>
              <span></span>
              <span></span>
            </label>
        </div>
        <ul class="main-nav">
            <li><a href="../index.php">Home</a></li>
            <li><a href="products.php">Products</a></li>
			<li class="active"><a href="">My orders</a></li>
			<li><a href="profile.php">My profile</a></li>
			<li><a href="comment.php">Leave a comment</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul> 
    </nav>
    <h5 class="myorders">My orders</h5>
    <?php 
        //S'il existe des commandes les afficher
        if(mysqli_num_rows($result1) >0){
            while($order = $result1->fetch_assoc()){   
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
                    //Variable pour calculer le total de chaque commande
                    $total = 0;
                    //Variable pour stocker l'id de chaque commande
                    $order_id = $order["id"];
                    //Requete pour recuperer les produits de la ligne de commande
                    $sql2 = "select order_id, product_id , quantity from order_line where order_id = '$order_id'";
                    $result2 = $db->query($sql2);
                    //Parcourir les lignes de commandes
                    while($order_line = $result2->fetch_assoc()){
                        //Variable pour stocker l'id de chaque produit
                        $product_id = $order_line["product_id"];
                         //Requete pour recuperer les caracteristiques de chaque produit
                        $sql3 = "select name , price , picture from products where id = '$product_id'";
                        $result3 = $db->query($sql3);
                        while($product = $result3->fetch_assoc()){
                            echo "<tr>
                                    <td>".$product['name']."</td>
                                    <td>".$order_line['quantity']."</td>
                                    <td>$".$product['price']."</td>
                                    </tr>
                                ";
                            //calculer le total
                            $total = $total + ($order_line['quantity'] * $product['price']);
                        }

            
                    }
                    echo "<tr>
                            <td colspan='2' align='right'>Total</td>
                            <td align='right'> $".number_format($total, 2)."</td>
                        </tr>
                    </tbody></table>";
                    echo '<br>';
                    $orderid = $order['id'];
                    ?>
                    <!-- lien pour supprimer une commande de la table orders -->
                    <a href="orders.php?action=delete&id=<?php echo $orderid; ?>"><span class="remove-order">Remove this order</span></a>
                    <?php
            }
        }
        else echo "<div class='noOrders'>No orders found</div>";
    ?>
    <br><br><br>
</body>
</html>