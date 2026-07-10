<?php
session_start();

include "connect.php";

// Hakikisha ame-login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Hakikisha ni admin
if ($_SESSION['role'] != "admin") {
    header("Location: ../login.php");
    exit();
}

// Chukua users wote isipokuwa admin aliyelogin
$sql = "SELECT * FROM users
        WHERE role='user'
        ORDER BY full_name ASC";

$stmt = $conn->prepare($sql);
$stmt->execute();

$users = $stmt->fetchAll();

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $receiver = $_POST['receiver'];
    $title = trim($_POST['title']);
    $body = trim($_POST['message']);

    if (empty($title) || empty($body)) {
        $error = "Please fill all fields.";
    } else {
        // Message kwa wote
        if ($receiver == "all") {
            $sql = "INSERT INTO messages
                    (sender_id, receiver_id, title, message)
                    VALUES (?, NULL, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $_SESSION['user_id'],
                $title,
                $body
            ]);
        } else {
            // Message kwa user mmoja
            $sql = "INSERT INTO messages
                    (sender_id, receiver_id, title, message)
                    VALUES (?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $_SESSION['user_id'],
                $receiver,
                $title,
                $body
            ]);
        }
        $message = "Message sent successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Message - Admin Panel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        body {
            background: #f4f6f9;
            color: #333;
        }

        /* Top Header Navigation */
        .header {
            background: #0d6efd;
            color: #fff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 22px;
            font-weight: 600;
        }

        /* Main Container */
        .container {
            width: 90%;
            max-width: 700px;
            margin: 40px auto;
        }

        /* Card Layout */
        .card {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }

        h2 {
            color: #0d6efd;
            margin-bottom: 5px;
            font-size: 24px;
        }

        .user-welcome {
            color: #6c757d;
            margin-bottom: 20px;
            font-size: 15px;
        }

        /* Navigation Links Inside Card */
        .nav-links {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .nav-links a {
            text-decoration: none;
            color: #0d6efd;
            font-weight: bold;
            font-size: 15px;
            transition: 0.2s;
        }

        .nav-links a:hover {
            text-decoration: underline;
        }

        .nav-links .separator {
            color: #ccc;
            margin: 0 10px;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #495057;
            font-size: 15px;
        }

        input[type="text"], select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 15px;
            background-color: #fff;
            transition: border-color 0.15s ease-in-out;
        }

        input[type="text"]:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
        }

        textarea {
            resize: vertical;
        }

        /* Button Styling */
        button {
            width: 100%;
            padding: 12px;
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
        }

        button:hover {
            background: #0b5ed7;
        }

        button:active {
            transform: scale(0.98);
        }

        /* Alert Status Updates */
        .alert {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 15px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Messaging System - Admin Panel</h1>
    </div>

    <div class="container">
        <div class="card">
            
            <h2>Send Message</h2>
            <p class="user-welcome">Welcome, <b><?php echo htmlspecialchars($_SESSION['full_name']); ?></b></p>
            
            <div class="nav-links">
                <a href="admin.php">🏠 Dashboard</a>
                <span class="separator">|</span>
                <a href="../logout.php" style="color: #dc3545;">🚪 Logout</a>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="receiver">Recipient</label>
                    <select name="receiver" id="receiver">
                        <option value="all">🌐 All Users</option>
                        <?php foreach($users as $user): ?>
                            <option value="<?php echo $user['id']; ?>">
                                👤 <?php echo htmlspecialchars($user['full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" placeholder="Enter message title..." required>
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea name="message" id="message" rows="6" placeholder="Type your message here..." required></textarea>
                </div>

                <button type="submit">🚀 Send Message</button>
            </form>

        </div>

        <div class="footer">
            &copy; <?php echo date("Y"); ?> Messaging System. All Rights Reserved.
        </div>
    </div>

</body>
</html>