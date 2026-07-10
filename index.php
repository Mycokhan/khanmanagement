<?php
session_start();
include 'navigation.php';

// Kama mtumiaji alikuwa tayari ameingia, msukume kwenda kwenye dashboard yake husika
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == "admin") {
        header("Location: admin.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - MYCOKHAN OFFICE MANAGEMENT</title>
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
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin-left: 270px; /* Adjust for sidebar width */
        }

        /* Top Navigation Bar */
    /* Top Navigation Bar */
.navbar {
    background: #fff;
    padding: 15px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    justify-items: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    position: sticky; 
    top: 0;
    z-index: 1000;
    width: 100%; 
    height: 60px;
}

        .navbar .logo {
            font-size: 22px;
            font-weight: bold;
            color: #0d6efd;
            letter-spacing: 0.5px;
        }

        .navbar .nav-links a {
            text-decoration: none;
            color: #495057;
            font-weight: 600;
            margin-left: 20px;
            transition: 0.2s;
        }

        .navbar .nav-links a:hover {
            color: #0d6efd;
        }

        /* Hero Section (Katikati) */
        .hero-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .hero-card {
            background: #fff;
            max-width: 650px;
            width: 100%;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            text-align: center;
            border-top: 5px solid #0d6efd;
        }

        .hero-card h1 {
            font-size: 32px;
            color: #212529;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .hero-card p {
            color: #6c757d;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        /* Features/Services list inside card */
        .system-features {
            display: flex;
            justify-content: space-around;
            margin-bottom: 35px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        .feature-item {
            font-size: 14px;
            font-weight: 500;
            color: #495057;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn {
            display: inline-block;
            flex: 1;
            max-width: 200px;
            padding: 14px 20px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-login {
            background: #0d6efd;
            color: #fff;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
        }

        .btn-login:hover {
            background: #0b5ed7;
            transform: translateY(-2px);
        }

        .btn-register {
            background: #fff;
            color: #0d6efd;
            border: 2px solid #0d6efd;
        }

        .btn-register:hover {
            background: #eef5ff;
            transform: translateY(-2px);
        }

        /* Footer */
        .footer {
            background: #212529;
            color: #a8aeb4;
            text-align: center;
            padding: 20px;
            font-size: 14px;
        }
   
    </style>
</head>
<body>

    <div class="navbar">
        <div class="logo">💼 MYCOKHAN</div>
        <!-- <div class="nav-links">
            <a href="login.php">Login</a>
            <a href="register.php" style="color: #0d6efd;">Sign Up</a>
        </div> -->
    </div>

    <div class="hero-container">
        <div class="hero-card">
            <h1>MYCOKHAN OFFICE MANAGEMENT</h1>
            <p>
                Karibu kwenye mfumo rasmi wa usimamizi wa ofisi wa MYCOKHAN. Mfumo huu unakuwezesha kuwasiliana, kupokea taarifa, na kurahisisha utendaji kazi wa kila siku wa ofisi kwa usalama na ufanisi wa hali ya juu.
            </p>

            <div class="system-features">
                <div class="feature-item">📥 Secure Inbox</div>
                <div class="feature-item">📢 Broadcasts</div>
                <div class="feature-item">👥 User Panel</div>
            </div>

            <div class="action-buttons">
                <a href="login.php" class="btn btn-login">🔑 Sign In</a>
                <a href="register.php" class="btn btn-register">📝 Register</a>
            </div>
        </div>
    
    </div>
     
    <div class="footer">
        &copy; <?php echo date("Y"); ?> MYCOKHAN Office Management System. All Rights Reserved.
    </div>

</body>
</html>