<?php
// reset_password.php
session_start();
require_once 'config.php';

$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Verify token and check expiration
        $stmt = $pdo->prepare("SELECT reset_token, reset_expires FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && $user['reset_token'] && password_verify($token, $user['reset_token'])) {
            if (new DateTime() <= new DateTime($user['reset_expires'])) {
                // Token is valid and not expired
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Update password and clear reset token using email instead of id
                $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE email = ?");
                $stmt->execute([$hashed_password, $email]);
                
                $success = "Your password has been successfully reset. You can now login with your new password.";
            } else {
                $error = "Reset token has expired. Please request a new password reset.";
            }
        } else {
            $error = "Invalid or expired reset token.";
        }
    }
} else {
    // Verify token when page loads
    if ($token && $email) {
        $stmt = $pdo->prepare("SELECT reset_token, reset_expires FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user || !$user['reset_token'] || !password_verify($token, $user['reset_token'])) {
            $error = "Invalid reset token.";
        } elseif (new DateTime() > new DateTime($user['reset_expires'])) {
            $error = "Reset token has expired. Please request a new password reset.";
        }
    } else {
        $error = "Invalid reset link.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - EducareHub</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #a8e6cf 0%, #88d8c0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #20b2aa;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
            background-color: #fafafa;
        }

        input[type="password"]:focus {
            outline: none;
            border-color: #20b2aa;
            background-color: white;
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #20b2aa 0%, #17a2b8 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(32, 178, 170, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .links {
            margin-top: 25px;
            font-size: 14px;
        }

        .links a {
            color: #20b2aa;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .links a:hover {
            color: #17a2b8;
            text-decoration: underline;
        }

        .alert {
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .separator {
            margin: 0 10px;
            color: #999;
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
                margin: 10px;
            }
            
            .logo {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">EducareHub</div>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <div class="links">
                <a href="user_login.php">Go to Login</a>
            </div>
        <?php elseif ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
            <div class="links">
                <a href="forgot_password.php">Request New Reset</a>
                <span class="separator">|</span>
                <a href="user_login.php">Back to Login</a>
            </div>
        <?php else: ?>
            <form method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                
                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter new password" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required minlength="6">
                </div>
                
                <button type="submit" class="btn">Reset Password</button>
            </form>
            
            <div class="links">
                <a href="user_login.php">Back to Login</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>