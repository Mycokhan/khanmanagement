<?php
session_start();

include "connect.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($full_name) || empty($email) || empty($phone_number) || empty($password) || empty($confirm_password)) {

        $error = "All fields are required.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Invalid email address.";

    } elseif ($password != $confirm_password) {

        $error = "Passwords do not match.";

    } else {

        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->execute([$email]);

        if($check->rowCount()>0){

            $error = "Email already exists.";

        }else{

            $hashed_password = password_hash($password,PASSWORD_DEFAULT);

            $role="user";

            $stmt=$conn->prepare("INSERT INTO users(full_name,email,phone_number,password,role)
            VALUES(?,?,?,?,?)");

            if($stmt->execute([
                $full_name,
                $email,
                $phone_number,
                $hashed_password,
                $role
            ])){

                // MABORESHO: Muishe mtumiaji kwenye Session hapa hapa akimaliza kujisajili
                $new_user_id = $conn->lastInsertId(); // Tunapata ID yake mpya kutoka kwenye database
                
                $_SESSION['user_id'] = $new_user_id;
                $_SESSION['full_name'] = $full_name;
                $_SESSION['role'] = $role;

                // Mpeleke moja kwa moja kwenye chatbox ili akifika huko dot iwe ya kijani papo hapo!
                header("Location: chatbox.php");
                exit();

            }else{

                $error="Registration Failed.";

            }

        }

    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>User Registration</title>

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial,Helvetica,sans-serif;
}

body{

background:#f1f3f6;

display:flex;

justify-content:center;

align-items:center;

height:100vh;

}

.container{

width:420px;

background:#fff;

padding:30px;

border-radius:10px;

box-shadow:0 0 15px rgba(0,0,0,.2);

}

h2{

text-align:center;

margin-bottom:20px;

color:#0d6efd;

}

label{

font-weight:bold;

display:block;

margin-top:12px;

}

input{

width:100%;

padding:12px;

margin-top:6px;

border:1px solid #ccc;

border-radius:5px;

font-size:15px;

}

button{

width:100%;

margin-top:20px;

padding:12px;

background:#0d6efd;

color:white;

border:none;

border-radius:5px;

font-size:16px;

cursor:pointer;

}

button:hover{

background:#084298;

}

.success{

background:#d1e7dd;

color:#0f5132;

padding:12px;

margin-bottom:15px;

border-radius:5px;

}

.error{

background:#f8d7da;

color:#842029;

padding:12px;

margin-bottom:15px;

border-radius:5px;

}

.footer{

text-align:center;

margin-top:20px;

}

.footer a{

color:#0d6efd;

text-decoration:none;

font-weight:bold;

}

.footer a:hover{

text-decoration:underline;

}

</style>

</head>

<body>

<div class="container">

<h2>User Registration</h2>

<?php

if(!empty($message)){

echo "<div class='success'>$message</div>";

}

if(!empty($error)){

echo "<div class='error'>$error</div>";

}

?>

<form method="POST">

<label>Full Name</label>

<input
type="text"
name="full_name"
required>

<label>Email</label>

<input
type="email"
name="email"
required>

<label>Phone Number</label>

<input
type="number"
name="phone_number"
required>

<label>Password</label>

<input
type="password"
name="password"
required>

<label>Confirm Password</label>

<input
type="password"
name="confirm_password"
required>

<button type="submit">

Register

</button>

</form>

<div class="footer">

Already have an account?

<a href="login.php">Login Here</a>

</div>

</div>

</body>

</html>
