<?php
include 'email.php';
//Démarrer la session
session_start();
//Redirection vers la page des produits au cas ou le client est déjà connecté
if(isset($_SESSION['id']) && !empty($_SESSION['id'])) {header('location: products.php');exit();}

//Declaration d'un tableau pour stocker les erreurs 
$errors = array(); 

// Connexion à la base de données
$db = mysqli_connect('localhost', 'id12799307_root', 'rootroot', 'id12799307_musicaland');

//si la connexion échoue
if (!$db) die('Could not connect: ' . mysql_error());

//Fonction qui verifie l'email
function IsEmail ($email,&$errors) {
    if (!preg_match_all('/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+.[a-zA-Z]{2,4}/im',$email)) 
        array_push($errors, "Email is invalid");
}

//Fonction qui verifie le mot de passe
function Mdp($password,&$errors) {
    
    if(strlen( $password) < 8) array_push($errors,"Password must be at least 8 caracters");
    if(!preg_match('/\d/',$password)) array_push($errors,"Password must have at least one number");
    if(!preg_match('/[A-Z]+/',$password)) array_push($errors,"Password must have at least one upper case letter");
}

//Fonction qui verifie le numéro de téléphone
function IsPhone($tel,&$errors) {
    
    if (!preg_match('#(\+212|0)([ \-_/]*)(\d[ \-_/]*){9}#',$tel)) 
        array_push($errors, "Phone number is invalid");
}


// Si l'utilisateur clique sur le bouton register
if (isset($_POST['signup'])) {
  
  //Récuperer les champs et échapper les caractères spéciaux des requêtes SQL
  $fname = mysqli_real_escape_string($db, $_POST['fname']);
  $lname = mysqli_real_escape_string($db, $_POST['lname']);
  $phone = mysqli_real_escape_string($db, $_POST['phone']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  //Verifier les champs
  if (empty($fname) || !isset($fname)) { array_push($errors, "First name is required"); }
  if (empty($lname) || !isset($lname)) { array_push($errors, "Last name is required"); }
  if (empty($phone) || !isset($phone)) { array_push($errors, "Phone number is required"); }
  else IsPhone($phone,$errors);
  if (empty($email) || !isset($email)) { array_push($errors, "Email is required"); }
  else IsEmail($email,$errors);
  if (empty($password) || !isset($password)) { array_push($errors, "Password is required"); }
  else Mdp($password,$errors);

  // Vérifier si l'email existe déjà
  $user_check_query = "SELECT * FROM user WHERE email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  if ($user) { 
      array_push($errors, "Email already exists");
  }

  // S'il n'y a pas d'erreurs
  if (empty($errors)) {
    
    //Crypter le mot de passe
  	$password = md5($password);

    //Creer la requete d'insertion dans la table user
  	$query = "INSERT INTO user (name,LastName,phone, email, password) 
    VALUES('$fname','$lname','$phone' ,'$email', '$password')";
    //Executer la requete
    $res = mysqli_query($db, $query);
    //Si le user est inseré , le rediriger vers la page des produits
    if($res){
        //définir un cookie qui stocke le prénom qui durera 30 jours
        setcookie("clientName", $fname, time()+(86400 * 30), "/");
        $_SESSION['id'] = $db->insert_id;
        sendMail($email,$fname,$lname);
  	    header('Location: products.php');
  	    exit();
    }
    //Sinon afficher le message d'erreur
    else echo "<div class='message'><div class='isa_error'>Error signing up</div></div>";
    
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/signup.css">
    <title>MUSICALAND | SIGNUP</title>
</head>

<body>
    <!-- Menu responsive -->
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
            <li><a href="../index.php#instruments">Instruments</a></li>
            <li><a href="../index.php#pop">Products</a></li>
            <li><a href="../index.php#sec4">About</a></li>
            <li class="active">
                <a href="#">Signup</a>
            </li>
        </ul>
    </nav>


    <section class="signup">
        <form action="#" method="POST">
            <h2>CREATE AN ACCOUNT</h2>
            <label for="fname">First name</label>
            <input type="text" name="fname" placeholder="Jhon">
            <br>
            <label for="lname">Last name</label>
            <input type="text" name="lname" placeholder="Doe">
            <br>
            <label for="phone">Phone Number</label>
            <input type="text" name="phone">
            <br>
            <label for="email">Email</label>
            <input type="email" name="email">
            <br>
            <label for="password">Password</label>
            <input type="password" name="password">
            <br>
            <input type="submit" value="Signup" name="signup">
        </form>
        <a href="login.php">I already have an account</a>
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
        </div>
    </section>
</body>

</html>



