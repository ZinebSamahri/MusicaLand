<?php
session_start();
$errors = array(); 

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'musicaland');

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
  
    if (empty($email)) {
        array_push($errors, "email is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }
  
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
    </section>
</body>

</html>


