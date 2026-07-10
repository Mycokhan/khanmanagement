<?php
// Washa ripoti ya makosa kwa ajili ya usalama wakati wa matengenezo
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Hakikisha faili la kuunganisha database lipo
if (file_exists("connect.php")) {
    include "connect.php";
} else {
    die("Database connection file (connect.php) is missing.");
}

// 1. Hakikisha mtumiaji ameingia (Logged In)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Hakikisha mtumiaji ni Admin
if ($_SESSION['role'] !== "admin") {
    header("Location: login.php");
    exit();
}

// 3. Pata idadi ya watumiaji (Kurekebisha kosa la SQL COUNT)
try {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_users FROM users WHERE role = 'user'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $number_of_users = $result['total_users'] ?? 0;
} catch (PDOException $e) {
    $number_of_users = "Error fetching data";
}

try {
    $stmt = $conn->prepare("SELECT id, full_name, email, phone_number, role FROM users ORDER BY full_name DESC");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $users = [];
}
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Messaging System</title>
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

        .logout-btn {
            background: #dc3545;
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            transition: 0.3s;
        }

        .logout-btn:hover {
            background: #b02a37;
        }

        /* Main Container */
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
        }

        .welcome-text {
            margin-bottom: 25px;
            font-size: 24px;
            color: #212529;
        }

        .welcome-text span {
            color: #0d6efd;
        }

        /* Dashboard Grid System */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        /* Card Styling */
        .card {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border-top: 4px solid #0d6efd;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card h3 {
            font-size: 16px;
            color: #6c757d;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .card .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #212529;
            margin-bottom: 15px;
        }

        /* Action Buttons / Navigation Menu */
        .menu-card {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .menu-card h3 {
            font-size: 18px;
            color: #212529;
            margin-bottom: 20px;
            border-bottom: 2px solid #f4f6f9;
            padding-bottom: 10px;
        }

        .menu-links {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .menu-links a {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            background: #0d6efd;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            transition: 0.3s;
            font-size: 16px;
            font-weight: 500;
        }

        .menu-links a:hover {
            background: #0b5ed7;
            transform: translateY(-2px);
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 50px;
            color: #6c757d;
            font-size: 14px;
        }
        .chatbox{
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            align-items: center;
            justify-content: center;
            margin-top: 20px;

        }
        .btn btn-chat{
            border-radius: 6px;

        }
        .btn btn-chat:hover{
            background: #0b5ed7;
            transform: translateY(-2px);
        }
        .users-toggle {
            background: #198754;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            margin-top: 10px;
        }
        .users-section {
            display: none;
            margin-top: 15px;
            overflow-x: auto;
        }
        .users-section.active {
            display: block;
        }
        .users-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        .users-table th,
        .users-table td {
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: left;
        }
        .users-table th {
            background: #f8f9fa;
        }
        .users-toggle:hover {
            background: #145a32;
        }
  
    </style>
    <script>
        function toggleUsersTable() {
            const section = document.getElementById('usersSection');
            section.classList.toggle('active');
            const btn = document.querySelector('.users-toggle');
            if (section.classList.contains('active')) {
                btn.textContent = '📋 Hide User List';
            } else {
                btn.textContent = '📋 Show User List';
            }
        }
    </script>
</head>
<body>

    <div class="header">
        <h1>Messaging System - Admin Panel</h1>
        <a href="logout.php" class="logout-btn">🚪 Logout</a>
    </div>

    <div class="container">
        
        <h2 class="welcome-text">
            Welcome back, <span><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Admin'); ?></span>!
        </h2>

        <div class="dashboard-grid">
            <div class="card">
                <h3>Total Registered Users</h3>
                <div class="stat-number"><?php echo $number_of_users; ?></div>
            </div>
            </div>

        <div class="menu-card">
            <h3>Quick Actions / Navigation</h3>
            <div class="menu-links">
                <a href="send_message.php">✉️ Send New Message</a>
                </div>
        </div>
       
    </div>
     <div class = "chatbox">
        <h2>Chat with Support</h2><br>
        <button class="btn btn-chat"><a href="chatbox.php" class="btn btn-chat">💬 Open Chat</a></button>
        <br><br>
        <button type="button" class="users-toggle" onclick="toggleUsersTable()">📋 Show User List</button>

        <div id="usersSection" class="users-section">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo (int) $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone_number'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($user['role'] ?? 'user'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="footer">
            &copy; <?php echo date("Y"); ?> Messaging System. All Rights Reserved.
        </div>

      

    </div>
   
    <script>
        function toggleUsersTable() {
            const section = document.getElementById('usersSection');
            const button = document.querySelector('.users-toggle');
            section.classList.toggle('active');
            button.textContent = section.classList.contains('active') ? '📋 Hide User List' : '📋 Show User List';
        }
    </script>

</body>
</html>