<?php
include('db.php');
session_start();

// Agar admin pehle se logged in hai, toh dashboard par redirect karein
if (isset($_SESSION['admin_uname'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_btn'])) {
    $username = trim($_POST['username']);
    $plain_password = $_POST['password'];

    if (!empty($username) && !empty($plain_password)) {
        // Secure Prepared Statement to query admin by username
        $stmt = mysqli_prepare($conn, "SELECT * FROM admin_login WHERE a_uname = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        if ($res && mysqli_num_rows($res) > 0) {
            $admin = mysqli_fetch_assoc($res);
            $is_authenticated = false;

            // Check if stored password is BCrypt or MD5
            if (password_verify($plain_password, $admin['a_password'])) {
                $is_authenticated = true;
            } elseif (strlen($admin['a_password']) === 32 && md5($plain_password) === $admin['a_password']) {
                $is_authenticated = true;
                
                // Auto-upgrade MD5 hash to secure Bcrypt hash in DB!
                $new_hash = password_hash($plain_password, PASSWORD_BCRYPT);
                $update_stmt = mysqli_prepare($conn, "UPDATE admin_login SET a_password = ? WHERE a_id = ?");
                mysqli_stmt_bind_param($update_stmt, "si", $new_hash, $admin['a_id']);
                mysqli_stmt_execute($update_stmt);
            }

            if ($is_authenticated) {
                $_SESSION['admin_uname'] = $admin['a_uname'];
                $_SESSION['admin_id']    = $admin['a_id'];
                
                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid password. Please try again.';
            }
        } else {
            $error = 'Username not found in system directory.';
        }
    } else {
        $error = 'Please fill out all credentials.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login – Explore India</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800;900&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #09090B;
            --orange: #FF782C;
            --orange-grad: linear-gradient(135deg, #FF782C, #F39C12);
            --border: rgba(255, 255, 255, 0.08);
            --card-bg: rgba(20, 20, 25, 0.65);
        }

        body {
            background: var(--bg);
            font-family: 'Open Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient Glowing Background Blobs */
        .glowing-blob {
            position: absolute;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(255,120,44,0.18) 0%, rgba(255,120,44,0) 70%);
            border-radius: 50%;
            z-index: 1;
            filter: blur(40px);
        }
        .blob-1 { top: 10%; left: 15%; }
        .blob-2 { bottom: 10%; right: 15%; }

        /* Login Container Card */
        .login-wrap {
            width: 100%;
            max-width: 440px;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 28px;
            padding: 40px;
            z-index: 10;
            backdrop-filter: blur(16px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
        }

        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo span { font-size: 32px; display: block; margin-bottom: 8px; }
        .login-logo h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 22px;
            font-weight: 900;
            color: #fff;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .login-logo h1 span {
            display: inline;
            color: var(--orange);
            font-size: 22px;
        }
        .login-logo p {
            font-size: 11px;
            color: rgba(255,255,255,0.4);
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        /* Form styling */
        .form-group {
            margin-bottom: 22px;
            position: relative;
        }
        .form-group label {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            color: rgba(255,255,255,0.4);
            display: block;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        .input-wrap {
            position: relative;
        }
        .input-wrap i {
            position: absolute;
            left: 16px; top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.25);
            font-size: 14px;
        }
        .form-control {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 16px 12px 46px;
            font-size: 14px;
            color: #fff;
            outline: none;
            transition: all 0.25s ease;
            height: auto;
        }
        .form-control:focus {
            background: rgba(0, 0, 0, 0.5);
            border-color: var(--orange);
            box-shadow: 0 0 0 3px rgba(255,120,44,0.15);
            color: #fff;
        }

        /* Button */
        .login-btn {
            background: var(--orange-grad);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px 20px;
            font-family: 'Open Sans', sans-serif;
            font-size: 14px;
            font-weight: 700;
            width: 100%;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(255,120,44,0.2);
        }
        .login-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(255,120,44,0.3);
        }

        /* Errors Alert */
        .error-box {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <!-- Ambient glowing backgrounds -->
    <div class="glowing-blob blob-1"></div>
    <div class="glowing-blob blob-2"></div>

    <div class="login-wrap">
        <!-- Brand logo header -->
        <div class="login-logo">
            <span>🇮🇳</span>
            <h1>Explore <span>India</span></h1>
            <p>Admin Gateway</p>
        </div>

        <!-- Display error box if validation fails -->
        <?php if ($error): ?>
            <div class="error-box">
                <i class="fa fa-exclamation-circle"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- Credentials Form -->
        <form method="POST" action="">
            <div class="form-group">
                <label>Admin Username</label>
                <div class="input-wrap">
                    <i class="fa fa-user"></i>
                    <input type="text" name="username" class="form-control" placeholder="Enter username" required autocomplete="off">
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrap">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                </div>
            </div>

            <button type="submit" name="login_btn" class="login-btn">Secure Login</button>
        </form>
    </div>

</body>
</html>