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
            overflow-x: hidden;
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

        .mobile-menu-toggle {
            display: none;
            flex-direction: column;
            justify-content: center;
            gap: 4px;
            background: #0d6efd;
            border: none;
            border-radius: 6px;
            padding: 8px;
            cursor: pointer;
            margin-right: 12px;
        }

        .mobile-menu-toggle span {
            display: block;
            width: 22px;
            height: 2px;
            background: #fff;
            border-radius: 2px;
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

        @media (max-width: 768px) {
            body {
                margin-left: 0;
            }

            .mobile-menu-toggle {
                display: flex;
            }

            .navbar {
                padding: 15px 20px;
                height: auto;
                justify-content: flex-start;
            }

            .navbar .logo {
                margin-left: 8px;
            }

            .sidebar {
                display: block;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1100;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .mobile-nav-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.35);
                z-index: 1000;
                display: none;
            }

            .mobile-nav-overlay.show {
                display: block;
            }

            .navbar .logo {
                font-size: 18px;
            }

            .hero-container {
                padding: 20px 15px;
                align-items: flex-start;
            }

            .hero-card {
                padding: 24px 18px;
            }

            .hero-card h1 {
                font-size: 24px;
            }

            .hero-card p {
                font-size: 15px;
            }

            .system-features {
                flex-direction: column;
                gap: 10px;
                padding: 12px;
            }

            .feature-item {
                text-align: center;
            }

            .action-buttons {
                flex-direction: column;
                gap: 10px;
            }

            .btn {
                max-width: none;
            }
        }
   
    </style>
</head>
<body>

    <div class="mobile-nav-overlay" id="mobileNavOverlay"></div>

    <div class="navbar">
        <button class="mobile-menu-toggle" id="mobileMenuToggle" type="button" aria-label="Open menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('mobileMenuToggle');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('mobileNavOverlay');

            if (!toggle || !sidebar || !overlay) {
                return;
            }

            toggle.addEventListener('click', function () {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('show');
            });

            overlay.addEventListener('click', function () {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            });

            sidebar.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('show');
                });
            });
        });
    </script>

</body>
</html>
