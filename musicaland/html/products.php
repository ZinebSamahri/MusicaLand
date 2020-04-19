<?php 
//Démarrer la session
session_start();

//Redirection vers la page d'acceuil au cas ou le client n'est pas connecté
if(!isset($_SESSION['id']) && empty($_SESSION['id'])) {header('location: ../index.php');exit();}

// Connexion à la base de données
$db = mysqli_connect('localhost', 'id12799307_root', 'rootroot', 'id12799307_musicaland');

//si la connexion échoue
if (!$db) die('Could not connect: ' . mysql_error());

//Creer la requête de récupération des produits de la table products
$sql = "select * from products";

//si la variable d'action est définie
//cela signifie qu'un produit a été ajouté ou retiré du panier
if(isset($_GET["action"])){
	//Si le produit est retiré du panier
	if($_GET["action"] == "delete") {
		//Parcourer la variable de session du panier
		foreach($_SESSION["shopping_cart"] as $keys => $values) {
			//Si on trouve l'id du produit a supprimer 
			if($values["item_id"] == $_GET["id"]) {
				//on le retire de la variable de session du panier
				unset($_SESSION["shopping_cart"][$keys]);
				//Nous informons le client que le produit est retiré
				echo '<script>alert("Product was removed from cart")</script>';
				//Redirection vers la page des produits 
				echo '<script>window.location="products.php"</script>';
			}
		}
	}

	//Si le produit est ajouté du panier
	if($_GET["action"] == "add") {
		//si le client clique sur le bouton ajouter au panier
		if(isset($_POST["add_to_cart"])) {
			//Si le panier est definie
			if(isset($_SESSION["shopping_cart"])){
				//la fonction array_column retourne tous les id des produits présent dans le panier
				$item_array_id = array_column($_SESSION["shopping_cart"], "item_id");
				//Si le produit a ajouter ne se trouve pas dans le panier
				if(!in_array($_GET["id"], $item_array_id)){
					//stocker la taille du panier (nombre d'articles)
					$count = count($_SESSION["shopping_cart"]);
					//creer un objet qui stocke le produit avec la quantité
					$item_array = array(
						'item_id'			=>	$_GET["id"],
						'item_name'			=>	$_POST["hidden_name"],
						'item_price'		=>	$_POST["hidden_price"],
						'item_quantity'		=>	$_POST["quantity"]
					);
					//l'ajouter au panier
					$_SESSION["shopping_cart"][$count] = $item_array;
				}
				//Si le produit a ajouter existe déjà dans le panier
				else echo '<script>alert("Item Already Added")</script>';
				
			}
			//Si le panier n'est pas definie
			else{
				//creer un objet qui stocke le produit avec la quantité
				$item_array = array(
					'item_id'			=>	$_GET["id"],
					'item_name'			=>	$_POST["hidden_name"],
					'item_price'		=>	$_POST["hidden_price"],
					'item_quantity'		=>	$_POST["quantity"]
				);
				//creer la variable session panier et l'ajouter dans la première case
				$_SESSION["shopping_cart"][0] = $item_array;
			}
		}
	}
}

//Si on veut ajouter les produits du panier dans la base de données
//clic sur le bouton checkout
if(isset($_POST["checkout"])){
	//Récuperer l id du client de la session
	$id = $_SESSION['id'];
	//Creer la requete d'insertion dans la table de commandes
	$sql_order ="insert into orders(user_id) values('$id')";
	//Executer la requete
	$result=$db->query($sql_order);
	//si l'exécution échoue
	if (!$result) {
		trigger_error('Invalid query: ' . $db->error);
	}
	//Recuperer l'id de la commande inserée
	$order_id = $db->insert_id;
	//Parcourir le panier (pour chaque produit)
	foreach($_SESSION["shopping_cart"] as $keys => $values) {
		//Récuperer l'id du produit
		$prod_id = $values["item_id"];
		//Récuperer la quantité du produit
		$quantity = $values["item_quantity"];
		//Creer la requete d'insertion du produit courant dans la ligne de commande
		$query = "insert into order_line(order_id,product_id,quantity) values('$order_id','$prod_id','$quantity')";
		//Executer la requete
		$result=$db->query($query);
		//si l'exécution échoue
		if (!$result) {
			trigger_error('Invalid query: ' . $db->error);
		}
	}
	//vider le panier apres l'insertion dans la base de données
	unset($_SESSION["shopping_cart"]);
	//Redirection
	header('Location: orders.php');
	exit();
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
            <li><a href="../index.php">Home</a></li>
            <li class="active"><a href="">Products</a></li>
			<li  ><a href="orders.php">My orders</a></li>
			<li  ><a href="profile.php">My profile</a></li>
			<li  ><a href="comment.php">Leave a comment</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <h5 class="ourproducts">
		Hi <?php if (isset($_COOKIE['clientName'])) echo $_COOKIE['clientName']?>, check out our products
	</h5>
    <br>
    <div class="product-container">
	<?php
		//Executer la requete de récuperation des produits
		$result = $db->query($sql);
		//S'il existe des produits dans la table
        if ($result->num_rows > 0) {
            // tant qu'il y a un produit
            while($row = $result->fetch_assoc()) { ?>
                <div class="product-item">
					<!-- Formulaire qui envoie les données du produit à 
					ce fichier en cours, ces données incluent les variables
					action et id , ici l'action est add et l'ID du produit
					est ajouté dynamiquement -->
                    <form action="products.php?action=add&id=<?php echo $row["id"]; ?>" method="post">
						<div class="img" style="position:relative; width:max-content">
							<!-- Champ pour saisir la quantité d'un produit -->
							<input type="number" name="quantity" value="1" class="form-control" />
							<!-- Bouton pour ajouter le produit au panier -->
							<button class="add-to" type="submit" name="add_to_cart">
								<img src="../Assets/plus.png" width="10" alt="">
							</button>
							<!-- Image du produit -->
							<?php echo '<img src="../Assets/products/'.$row["picture"].'" width="280"; margin:0 20px">';?>
						</div>
						<!-- Nom et prix du produit --> 
                        <?php
                        	echo '<h5>'.$row["name"].'</h5>';
                        	echo '<h4>'.$row["price"].'$</h4>';
						?>
						<!-- Champs cachés pour stocker le nom et le prix du produit -->
                        <input type="hidden" name="hidden_name" value="<?php echo $row["name"]; ?>" />
                        <input type="hidden" name="hidden_price" value="<?php echo $row["price"]; ?>" />
                    </form>
                </div> 
    <?php }}
		//S'il y a pas de produits a afficher 
		else echo "<h5>No products available<h5>";
    ?>
    </div>

	<!-- Affichage du panier -->
    <div class="orders">
		<!-- Si la variable session panier existe -->
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
						//Variable pour calculer le total de chaque commande
						$total = 0;
						//Parcourir les éléments du panier
						foreach($_SESSION["shopping_cart"] as $keys => $values)
						{
					?>
					<tbody>
					<tr>
						<!-- Nom du produit -->
						<td><?php echo $values["item_name"]; ?></td>
						<!-- Quantité du produit -->
						<td><?php echo $values["item_quantity"]; ?></td>
						<!-- Prix du produit -->
						<td>$ <?php echo $values["item_price"]; ?></td>
						<!-- Calcul du prix * quantité (total de chaque produit) -->
						<td>$ <?php echo number_format($values["item_quantity"] * $values["item_price"], 2);?></td>
						<!-- Lien de suppression d'un produit du panier ou l'action est delete et id : id du produit -->
						<td><a href="products.php?action=delete&id=<?php echo $values["item_id"]; ?>"><span>Remove</span></a></td>
					</tr>
					<!-- Calcul du total de la commande -->
					<?php
							$total = $total + ($values["item_quantity"] * $values["item_price"]);
						}
					?>
					<tr>
						<td colspan="3" align="right">Total</td>
						<td align="right">$ <?php echo number_format($total, 2); ?></td>
						<td align="right">
							<!-- Bouton de checkout -->
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