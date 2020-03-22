<?php
session_start();

$errors = array(); 

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'musicaland');

// REGISTER USER
if (isset($_POST['signup'])) {
  
    //get inputs
  $fname = mysqli_real_escape_string($db, $_POST['fname']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

    //get errors
  if (empty($fname) || !isset($fname)) { array_push($errors, "Full name is required"); }
  if (empty($email) || !isset($email)) { array_push($errors, "Email is required"); }
  if (empty($password) || !isset($password)) { array_push($errors, "Password is required"); }

  // check if user already exists
  $user_check_query = "SELECT * FROM user WHERE email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  
  if ($user) { 
      array_push($errors, "email already exists");
  }

  // no errors ,register user
  if (count($errors) == 0) {
  	$password = md5($password);//encrypt the password 

  	$query = "INSERT INTO user (name, email, password) 
  			  VALUES('$fname', '$email', '$password')";
  	mysqli_query($db, $query);
  	$_SESSION['fname'] = $fname;
  	$_SESSION['success'] = "You are now logged in";
  	header('location: products.php');
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
            <li><a href="review.html">Review</a></li>
            <li class="active">
                <a href="#">Signup</a>
            </li>
            <li>
                <a href="">Cart</a>
            </li>
        </ul>
    </nav>
    <div>
    <?php  if (count($errors) > 0) : ?>
        <div class="error">
            <?php foreach ($errors as $error) : ?>
            <p><?php echo $error ?></p>
            <?php endforeach ?>
        </div>
    <?php  endif ?>
    </div>

    <section class="signup">
        <form action="#" method="POST">
            <h2>CREATE AN ACCOUNT</h2>
            <label for="fname">Full name</label>
            <input type="text" name="fname" placeholder="Jhon doe">
            <br><br>
            <label for="email">Email</label>
            <input type="email" name="email">
            <br><br>
            <label for="password">Password</label>
            <input type="password" name="password">
            <br><br>
            <input type="submit" value="signup" name="signup">
        </form>
        <a href="login.php">I already have an account</a>
    </section>
</body>

</html>



