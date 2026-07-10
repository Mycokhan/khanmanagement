<?php
session_start();

include "connect.php";

// Hakikisha ame-login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Hakikisha ni user
if ($_SESSION['role'] != "user") {
    header("Location: ../login.php");
    exit();
}

// Hakikisha ID ipo
if (!isset($_GET['id'])) {
    die("Message ID not found.");
}

$message_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Chukua message
$sql = "SELECT
            messages.*,
            users.full_name AS sender_name
        FROM messages
        INNER JOIN users
            ON users.id = messages.sender_id
        WHERE messages.id = ?
        AND (messages.receiver_id = ?
             OR messages.receiver_id IS NULL)";

$stmt = $conn->prepare($sql);
$stmt->execute([$message_id, $user_id]);

$message = $stmt->fetch();

if (!$message) {
    die("Message not found.");
}

// Angalia kama tayari amesoma
$sql = "SELECT *
        FROM message_reads
        WHERE message_id = ?
        AND user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->execute([$message_id, $user_id]);

if ($stmt->rowCount() == 0) {

    $sql = "INSERT INTO message_reads(message_id,user_id)
            VALUES(?,?)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$message_id, $user_id]);

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>View Message</title>

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

.header{
    background:#198754;
    color:white;
    text-align:center;
    padding:20px;
    font-size:26px;
    font-weight:bold;
}

.container{
    width:80%;
    max-width:900px;
    margin:40px auto;
}

.card{
    background:white;
    padding:30px;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,.15);
}

h2{
    color:#198754;
    margin-bottom:20px;
}

.info{
    margin-bottom:15px;
    font-size:16px;
}

.info b{
    color:#198754;
}

.message-box{
    margin-top:25px;
    background:#f8f9fa;
    border-left:5px solid #198754;
    padding:20px;
    border-radius:6px;
    line-height:1.8;
    font-size:16px;
}

.actions{
    margin-top:30px;
}

.actions a{
    display:inline-block;
    text-decoration:none;
    background:#198754;
    color:white;
    padding:10px 18px;
    border-radius:5px;
    margin-right:10px;
}

.actions a:hover{
    background:#146c43;
}

.footer{
    text-align:center;
    margin-top:30px;
    color:#777;
}

</style>

</head>

<body>

<div class="header">

📨 View Message

</div>

<div class="container">

<div class="card">

<h2>Message Details</h2>

<div class="info">
<b>Title:</b>
<?php echo htmlspecialchars($message['title']); ?>
</div>

<div class="info">
<b>From:</b>
<?php echo htmlspecialchars($message['sender_name']); ?>
</div>

<div class="info">
<b>Date:</b>
<?php echo $message['created_at']; ?>
</div>

<div class="message-box">

<?php echo nl2br(htmlspecialchars($message['message'])); ?>

</div>

<div class="actions">

<a href="inbox.php">📥 Back to Inbox</a>

<a href="../logout.php">🚪 Logout</a>

</div>

</div>

<div class="footer">

&copy; <?php echo date("Y"); ?> Messaging System

</div>

</div>

</body>

</html>