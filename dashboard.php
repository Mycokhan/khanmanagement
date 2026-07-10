<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] != "user") {
    header("Location: ../login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>User Dashboard</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, Helvetica, sans-serif;
}

body{
    background:#f4f6f9;
}

/* Header */

.header{
    background:#198754;
    color:white;
    text-align:center;
    padding:20px;
    font-size:26px;
    font-weight:bold;
    padding: 15px 30px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    display: flex;
    justify-content: space-between; 
    align-items: center;
}

/* Main Container */

.container{
    width:90%;
    margin:30px auto;
}

/* Card */

.card{
    background:white;
    padding:30px;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,.15);
}

/* Heading */

h2{
    color:#198754;
    margin-bottom:15px;
}

/* User Text */

.user{
    font-size:18px;
    margin-bottom:25px;
}

/* Menu */

.menu a{

    display:block;

    background:#198754;

    color:white;

    text-decoration:none;

    padding:15px;

    margin-bottom:12px;

    border-radius:6px;

    transition:.3s;

    font-size:17px;

}

.menu a:hover{

    background:#146c43;

    transform:translateX(5px);

}

/* Footer */

.footer{

    text-align:center;

    margin-top:30px;

    color:#666;

}
/* Google Font kwa muonekano wa kisasa */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

.connect {
    font-family: 'Poppins', sans-serif;
    background: #ffffff;
    max-width: 450px;
    margin: 30px auto;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    text-align: center;
}

.connect {
    font-family: 'Poppins', sans-serif;
    background: #ffffff;
    max-width: 800px; 
    margin: 30px auto;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    text-align: center;
}


.buttons-container {
    display: flex;
    flex-direction: row; 
    flex-wrap: wrap;    
    justify-content: center; 
    gap: 15px;       
    margin-bottom: 25px;
}
.action-buttons {
    display: inline-block;
}

.btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px; 
    padding: 14px 20px;
    text-decoration: none;
    font-weight: 500;
    font-size: 15px;
    border-radius: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
}

.btn-chat {
    background-color: #e8f5e9;
    color: #2e7d32;
}

.btn-group {
    background-color: #e3f2fd;
    color: #1565c0;
}

.btn-broadcast {
    background-color: #fff3e0;
    color: #ef6c00;
}

.btn-inbox {
    background-color: #f3e5f5;
    color: #283593;
}

.btn-notify {
    background-color: #eceff1;
    color: #37474f;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    filter: brightness(0.95); 
}


.king {
    border-top: 1px solid #f0f0f0;
    padding-top: 15px;
}

.king p {
    color: #7f8c8d;
    font-size: 13px;
    line-height: 1.6;
    margin: 0;
}
.btn-logout {
    background-color: #ff4d4d; 
    border: none;
    border-radius: 6px;
    padding: 8px 16px;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-logout:hover {
    background-color: #ff1a1a; 
}

.btn-logout a {
    color: white;
    text-decoration: none;
    font-weight: bold;
    display: block;
}

</style>

</head>

<body>

<div class="header">
    <span>Messaging System - User Panel</span>
    <button class="btn-logout"><a href="../logout.php">🚪 Logout</a></button>
</div>

<div class="container">

<div class="card">

<h2>

Welcome,

<?php echo htmlspecialchars($_SESSION['full_name']); ?>

</h2>

<p class="user">

You are logged in as <b>User</b>

</p>

<div class="menu">

<a href="inbox.php">

📥 Inbox

</a>

<a href="profile.php">

👤 My Profile

</a>



</div>

</div>



<div class="connect">
    <h2>CONNECT WITH OTHER PEOPLE</h2>
    
    <div class="buttons-container">
        <div class="action-buttons">
            <a href="chatbox.php" class="btn btn-chat">💬 Chat</a>
        </div>
        <div class="action-buttons">
            <a href="groupchat.php" class="btn btn-group">👥 Group Chat</a>   
        </div>
        <div class="action-buttons">
            <a href="" class="btn btn-broadcast">📢 Broadcast</a>
        </div>
        <div class="action-buttons">
            <a href="inbox.php" class="btn btn-inbox">📥 Inbox</a>
        </div>  
        <div class="action-buttons">
            <a href="" class="btn btn-notify">📝 Notifications</a>
        </div>
    </div>

    <div class="king">
        <p>mycokhan makes you to connect with others, everywhere in the world</p>   
    </div>
</div>
<div class="footer">

&copy; <?php echo date("Y"); ?> Messaging System

</div>
</html>