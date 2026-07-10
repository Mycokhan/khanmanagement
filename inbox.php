<?php
session_start();

include "connect.php";

// Hakikisha ame-login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Hakikisha ni user
if ($_SESSION['role'] != "user") {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT
            messages.*,
            users.full_name AS sender_name
        FROM messages
        INNER JOIN users
            ON users.id = messages.sender_id
        WHERE receiver_id = ?
           OR receiver_id IS NULL
        ORDER BY created_at ASC";

$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);

$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Inbox</title>

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
    color:#fff;
    padding:20px;
    text-align:center;
    font-size:26px;
    font-weight:bold;
}

/* Container */

.container{
    width:90%;
    margin:30px auto;
}

/* Card */

.card{
    background:#fff;
    padding:25px;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,.15);
}

/* Welcome */

h2{
    color:#198754;
    margin-bottom:15px;
}

.user{
    margin-bottom:20px;
    font-size:17px;
}

/* Navigation */

.nav{
    margin-bottom:20px;
}

.nav a{
    text-decoration:none;
    background:#198754;
    color:#fff;
    padding:10px 18px;
    border-radius:5px;
    margin-right:10px;
    display:inline-block;
}

.nav a:hover{
    background:#146c43;
}

/* Table */

table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
}

table th{
    background:#198754;
    color:#fff;
    padding:12px;
}

table td{
    padding:12px;
    border:1px solid #ddd;
    text-align:center;
}

table tr:nth-child(even){
    background:#f9f9f9;
}

table tr:hover{
    background:#eef8f2;
}

/* Open Button */

.open-btn{
    background:#0d6efd;
    color:white;
    padding:8px 15px;
    border-radius:5px;
    text-decoration:none;
}

.open-btn:hover{
    background:#084298;
}

/* No Messages */

.no-message{
    background:#fff3cd;
    color:#856404;
    padding:15px;
    border-radius:5px;
    text-align:center;
    margin-top:20px;
}

/* Footer */

.footer{
    text-align:center;
    margin-top:25px;
    color:#777;
}

</style>

</head>

<body>

<div class="header">

📥 Messaging System - Inbox

</div>

<div class="container">

<div class="card">

<h2>Inbox</h2>

<p class="user">

Welcome,
<b><?php echo htmlspecialchars($_SESSION['full_name']); ?></b>

</p>

<div class="nav">

<a href="dashboard.php">🏠 Dashboard</a>

<a href="logout.php">🚪 Logout</a>

</div>

<?php

if(count($messages)==0){

?>

<div class="no-message">

No messages found.

</div>

<?php

}else{

?>

<table>

<tr>

<th>ID</th>

<th>Title</th>

<th>From</th>

<th>Date</th>

<th>Action</th>

</tr>

<?php foreach($messages as $row){ ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo htmlspecialchars($row['title']); ?></td>

<td><?php echo htmlspecialchars($row['sender_name']); ?></td>

<td><?php echo $row['created_at']; ?></td>

<td>

<a class="open-btn"
href="view_message.php?id=<?php echo $row['id']; ?>">

Open

</a>

</td>

</tr>

<?php } ?>

</table>

<?php } ?>

</div>

<div class="footer">

&copy; <?php echo date("Y"); ?> Messaging System

</div>

</div>

</body>

</html>