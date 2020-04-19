<?php
//Démarrer la session
session_start();
//Redirection vers la page d'acceuil au cas ou le client n'est pas connecté
if(!isset($_SESSION['id']) && empty($_SESSION['id'])) {header('location: ../index.php');exit();}
//Declaration d'un tableau pour stocker les erreurs 
$errors = array(); 

// Connexion à la base de données
$db = mysqli_connect('localhost', 'id12799307_root', 'rootroot', 'id12799307_musicaland');

//Récuperer l id du client de la session
$user_id = $_SESSION["id"];

//Declaration d'une variable pour stocker le message de succés
$success = null;


//Si le cient clique sur submit
if (isset($_POST['Submit'])) {
    //Récuperer les champs about et comment
    $about = mysqli_real_escape_string($db, $_POST['about']);
    $comment = mysqli_real_escape_string($db, $_POST['comment']);
    
    //Si le client n'as rien saisi
    if (empty($about))  array_push($errors, "About is required");
    if (empty($comment)) array_push($errors, "Comment is required");
    
    //Si il y en a pas d'erreur
    if (count($errors) == 0) {
        //Creer la requete d'insertion du commentaire
        $sql_comment ="insert into comment(user_id,about,comment) values('$user_id','$about','$comment')";
        //Executer la requete
        $result=$db->query($sql_comment);
        //Cas d'erreur d'insertion
        if (!$result) trigger_error('Invalid query: ' . $db->error);
        //Cas de succés
        else $success ="Thank you for your support !";
    }
  }
  
  ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/comment.css">
    <title>MUSICALAND | Leave a comment</title>
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
			<li><a href="orders.php">My orders</a></li>
			<li><a href="profile.php">My profile</a></li>
			<li class="active"><a href="">Leave a comment</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <!-- Affichage de message de succés -->
    <?php  if (isset($success) && !empty($success)):?>
        <div class="message">
            <div class="isa_success">
                <?php echo $success ?>
            </div>
        </div>
    <?php  endif ?>
    <section class="login">
        <form action="comment.php" method="POST">
            <h2>Leave a comment</h2>
            <label for="about">About</label>
            <br>
            <input type="about" name="about">
            <br><br>
            <label for="comment">Comment</label>
            <br>
            <textarea name="comment" id="" cols="40" rows="10"></textarea>
            <br><br>
            <input type="submit" value="Submit" name ="Submit">
        </form>
        <div>
            <!-- Affichage des erreurs -->
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