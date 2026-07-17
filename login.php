<?php
session_start();

include "connect.php";

// Kama tayari ame-login
if (isset($_SESSION['user_id'])) {

    if ($_SESSION['role'] == "admin") {
        header("Location: admin.php");
    } else {
        header("Location:dashboard.php");
    }

    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {

        $error = "Please enter email and password.";

    } else {

        $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$email]);

        // MABORESHO 1: Kulazimisha PDO ifetch kwa kutumia Column Names pekee (FETCH_ASSOC)
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {

            if (password_verify($password, $user['password'])) {

                // MABORESHO 2: Kulazimisha ID ihifadhiwe kama namba halisi (Integer) kwenye Session
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] == "admin") {

                    header("Location: admin.php");

                } else {

                    header("Location: dashboard.php");

                }

                exit();

            } else {

                $error = "Incorrect password.";

            }

        } else {

            $error = "Email does not exist.";

        }

    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>System Login</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, Helvetica, sans-serif;
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

    margin-bottom:25px;

    color:#0d6efd;

}

label{

    display:block;

    margin-top:12px;

    font-weight:bold;

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

    padding:12px;

    margin-top:20px;

    border:none;

    border-radius:5px;

    background:#0d6efd;

    color:white;

    font-size:16px;

    cursor:pointer;

}

button:hover{

    background:#084298;

}

.error{

    background:#f8d7da;

    color:#842029;

    padding:12px;

    border-radius:5px;

    margin-bottom:15px;

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

<h2>System Login</h2>

<?php

if(!empty($error)){

    echo "<div class='error'>$error</div>";

}

?>

<form method="POST">

<label>Email</label>

<input
type="email"
name="email"
required>

<label>Password</label>

<input
type="password"
name="password"
required>

<button type="submit">

Login

</button>

</form>

<div class="footer">

Don't have an account?

<a href="register.php">

Register Here

</a>

</div>

</div>

</body>
</html>
