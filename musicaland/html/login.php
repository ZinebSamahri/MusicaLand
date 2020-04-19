<?php
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
function IsEmail ($email) {
    if (preg_match_all('/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+.[a-zA-Z]{2,4}/im',$email)) 
        return TRUE;
    else 
        return FALSE;
}

// Si l'utilisateur clique sur le bouton login
if (isset($_POST['login'])) {
    //Récuperer les champs et échapper les caractères spéciaux des requêtes SQL
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
  
    //Verifier les champs
    if (empty($email)) array_push($errors, "email is required");
    else if(!IsEmail($email)){array_push($errors, "Email is invalid");}
    if (empty($password)) array_push($errors, "Password is required");
    
    // S'il n'y a pas d'erreurs
    if (empty($errors)) {
        //Crypter le mot de passe
        $password = md5($password);
        //Creer la requete de récupération du client
        $query = "SELECT * FROM user WHERE email='$email' AND password='$password'";
        //Executer la requete
        $results = mysqli_query($db, $query);
        //si l'utilisateur existe
        if (mysqli_num_rows($results) == 1) {
            //recuperer les infos du client et les stocker dans la session
            $user = $results->fetch_assoc();
            $_SESSION['fname'] = $user['name'];
            $_SESSION['id'] = $user['id'];
            header('Location: products.php');
            exit();
        }
        //Sinon , ajouter un erreur 
        else array_push($errors, "Wrong email/password combination");       
    }
  }
  
  ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/login.css">
    <title>MUSICALAND | LOGIN</title>
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
            <li><a href="../index.php#instruments">Instruments</a></li>
            <li><a href="../index.php#pop">Products</a></li>
            <li><a href="../index.php#sec4">About</a></li>
            <li class="active">
                <a href="login.php">Login</a>
            </li>
        </ul>
    </nav>

    <section class="login">
        <form action="login.php" method="POST">
            <h2>LOGIN</h2>
            <label for="email">Email</label>
            <input type="email" name="email">
            <br><br>
            <label for="password">Password</label>
            <input type="password" name="password">
            <br><br>
            <input type="submit" value="Log In" name ="login">
        </form>
        <a href="signup.php">I don't have an account</a>
        <div>
            <!-- Afficher les erreurs s'il y en a -->
            <?php  if (count($errors) > 0) : ?>
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


