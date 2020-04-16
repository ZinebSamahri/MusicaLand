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

function IsPhone($tel) {
    
    if (preg_match('/(\+212|0)([ \-_/]*)(\d[ \-_/]*){9}/',$tel)) 
        return TRUE;
    else 
        return FALSE;
}


// REGISTER USER
if (isset($_POST['signup'])) {
  
    //get inputs
  $fname = mysqli_real_escape_string($db, $_POST['fname']);
  $lname = mysqli_real_escape_string($db, $_POST['lname']);
  $phone = mysqli_real_escape_string($db, $_POST['phone']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

    //get errors
  if (empty($fname) || !isset($fname)) { array_push($errors, "First name is required"); }
  if (empty($lname) || !isset($lname)) { array_push($errors, "Last name is required"); }
  if (empty($phone) || !isset($lname)) { array_push($errors, "Phone number is required"); }
  else if(!IsPhone($phone)){array_push($errors, "Phone number is invalid");}
  if (empty($email) || !isset($email)) { array_push($errors, "Email is required"); }
  else if(!IsEmail($email)){array_push($errors, "Email is invalid");}
  if (empty($password) || !isset($password)) { array_push($errors, "Password is required"); }
  else if(!Mdp($password)){array_push($errors, "Password is invalid");}

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

  	$query = "INSERT INTO user (name,LastName,phone, email, password) 
  			  VALUES('$fname',$lname,$phone ,'$email', '$password')";
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
            <input type="submit" value="signup" name="signup">
        </form>
        <a href="login.php">I already have an account</a>
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



