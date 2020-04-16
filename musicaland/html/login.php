<?php
session_start();
$errors = array(); 

// connect to the database
$db = mysqli_connect('localhost', 'id12799307_root', 'rootroot', 'id12799307_musicaland');

function IsEmail ($email) {
    if (preg_match_all('/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+.[a-zA-Z]{2,4}/im',$email)) 
        return TRUE;
    else 
        return FALSE;
}

function Mdp($password) {
    
    if (strlen( $password) >= 8 && preg_match('/\d/',$password) && preg_match('/[^A-Za-z0-9]/',$password) && preg_match('/[A-Z]+/',$password)) 
        return TRUE;
    else 
        return FALSE;
}

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
  
    if (empty($email)) {
        array_push($errors, "email is required");
    }
    else if(!IsEmail($email)){array_push($errors, "Email is invalid");}
    if (empty($password)) {
        array_push($errors, "Password is required");
    }
    else if(!Mdp($password)){array_push($errors, "Password is invalid");}
  
    if (count($errors) == 0) {
        $password = md5($password);
        $query = "SELECT * FROM user WHERE email='$email' AND password='$password'";
        $results = mysqli_query($db, $query);
        if (mysqli_num_rows($results) == 1) {
            $user = $results->fetch_assoc();
          $_SESSION['fname'] = $user['name'];
          $_SESSION['email'] = $email;
          $_SESSION['id'] = $user['id'];
          header('location: products.php');
        }else {
            array_push($errors, "Wrong email/password combination");
        }
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
            <li><a href="../index.html#instruments">Instruments</a></li>
            <li><a href="../index.html#pop">Products</a></li>
            <li><a href="../index.html#sec4">About</a></li>
            <li ><a href="review.html">Review</a></li>
            <li class="active">
                <a href="login.php">Login</a>
            </li>
            <li>
                <a href="">Cart</a>
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
            <input type="submit" value="login" name ="login">
        </form>
        <a href="signup.php">I don't have an account</a>
                <div>
            <?php  if (count($errors) > 0) : ?>
                <div class="error">
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


