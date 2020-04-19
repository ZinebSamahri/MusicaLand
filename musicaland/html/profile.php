<?php 
//Démarrer la session
session_start();

//Redirection vers la page d'acceuil au cas ou le client n'est pas connecté
if(!isset($_SESSION['id']) && empty($_SESSION['id'])) {header('location: ../index.php');exit();}

//Declaration d'un tableau pour stocker les erreurs 
$errors = array(); 


// Connexion à la base de données
$db = mysqli_connect('localhost', 'id12799307_root', 'rootroot', 'id12799307_musicaland');

// créer la requete de récupération des informations de l'utilisateur connecté
$user = "select * from user where id = ".$_SESSION['id']."";
//si la connexion échoue
if (!$db) die('Could not connect: ' . mysql_error());




$name ="";
$lastname = "";
$phone = "";
$email = "";

	//Executer la requete de récuperation de profile
    $user_infos = $db->query($user);
    if ($user_infos->num_rows > 0) {
    // récupération des informations de l'utilisateur connecté
    while($myrow = $user_infos->fetch_assoc()) {
        $name = $myrow["name"];
        $lastname = $myrow["LastName"];
        $phone = $myrow["phone"];
        $email = $myrow["email"];
    }
}

//Fonction qui verifie l'email
function IsEmail ($submitted_email,&$errors) {
    if (!preg_match_all('/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+.[a-zA-Z]{2,4}/im',$submitted_email)) 
        array_push($errors, "Email is invalid");
}

//Fonction qui verifie le mot de passe
function Mdp($password,&$errors) {
    
    if(strlen( $password) < 8) array_push($errors,"Password must be at least 8 caracters");
    if(!preg_match('/\d/',$password)) array_push($errors,"Password must have at least one number");
    if(!preg_match('/[A-Z]+/',$password)) array_push($errors,"Password must have at least one upper case letter");
}

//Fonction qui verifie le numéro de téléphone
function IsPhone($submitted_phone,&$errors) {
    
    if (!preg_match('#(\+212|0)([ \-_/]*)(\d[ \-_/]*){9}#',$submitted_phone)) 
        array_push($errors, "Phone number is invalid");
}

// changer les information de l'utilisateur
if (isset($_POST['modify'])) {
  //Récuperer les champs et échapper les caractères spéciaux des requêtes SQL
  $submitted_fname = mysqli_real_escape_string($db, $_POST['name']);
  $submitted_lname = mysqli_real_escape_string($db, $_POST['lastname']);
  $submitted_phone = mysqli_real_escape_string($db, $_POST['phone']);
  $submitted_email = mysqli_real_escape_string($db, $_POST['email']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  //vérifier les champs
  IsPhone($phone,$errors);
  IsEmail($email,$errors);
  Mdp($password,$errors);

  //Verifier l'email s'il existe dejà
  $user_check_query = "SELECT * FROM user WHERE email='$submitted_email' and email != '$email'";

  $result = $db->query($user_check_query);

  if ($result->num_rows > 0) { 
      array_push($errors, "Email already exists");
  }

  
  // S'il n'y a pas d'erreurs
  if (empty($errors)) {
    
    //Crypter le mot de passe
  	$password = md5($password);
    $id = $_SESSION['id'];
    //Creer la requete de mise a jour dans la table user
  	$query = "UPDATE user set name = '$submitted_fname', LastName = '$submitted_lname',phone = '$submitted_phone',email = '$submitted_email', password = '$password' 
      where id = '$id'";
    //Executer la requete
    $res = mysqli_query($db, $query);
    //Si le user est modifieé , le rediriger vers la page des produits
    
  }

}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile page</title>
    <link rel="stylesheet" href="../css/profile.css">

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
            <li><a href="products.php">Products</a></li>
			<li><a href="orders.php">My orders</a></li>
			<li class="active"><a href="">My profile</a></li>
			<li><a href="comment.php">Leave a comment</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul> 
    </nav>
    <h5 class="myprofile">My profile</h5>
    <br>
    <div class="profile-container">
    <div>
            <!-- Afficher les erreurs s'il y en a -->
            <?php  if (!empty($errors)) : ?>
                <div class="message">
                    <?php foreach ($errors as $error) : ?>
                        <div class="isa_error">
                            <?php echo $error ?>
                        </div>
                    <?php endforeach ?>
                </div>
            <?php  endif ?>
            <?php
                if($res){
                    //afficher message de succès
                    echo '<div class="valid">Vos informations ont bien modifiées</div>';
                }
                //Sinon afficher le message d'erreur
                else array_push($errors, "Error de modification");
            ?>
        </div>
    <form action="profile.php" method="POST">
            <label for="name">First name</label>
            <br>
            <input type="text" name="name" value=<?php echo $name ?> required>
            <br>
            <label for="lastname">Last name</label>
            <br>
            <input type="text" name="lastname" value=<?php echo $lastname ?> required>
            <br>
            <label for="phone">Phone</label>
            <br>
            <input type="text" name="phone" value=<?php echo $phone ?> required>
            <br>
            <label for="email">Email</label>
            <br>
            <input type="email" name="email" value=<?php echo $email ?> required>
            <br>
            <label for="password">Password</label>
            <br>
            <input type="password" name="password" required>
            <br>
            <input type="submit" value="Modify" name="modify">
    </form> 
           
    </div>
    
			
	<br />

</body>
</html>