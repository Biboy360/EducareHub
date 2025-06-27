<?php
session_start();
require_once 'config.php';
require_once 'email_config.php';

use PHPMailer\PHPMailer\PHPMailer;
require 'vendor/autoload.php';

$message = '';
$message_type = '';

if ($_POST) {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    
    if (!$email) {
        $message = 'Please enter a valid email address.';
        $message_type = 'error';
    } else {
        try {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                // Generate token
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                
                // Save token
                $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
                $stmt->execute([password_hash($token, PASSWORD_DEFAULT), $expires, $email]);
                
                // Send email
                $reset_link = "http://{$_SERVER['HTTP_HOST']}" . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token={$token}&email=" . urlencode($email);
                
                if (sendPasswordReset($email, $reset_link)) {
                    $message = 'Password reset link sent to your email!';
                    $message_type = 'success';
                } else {
                    $message = 'Failed to send email. Please try again.';
                    $message_type = 'error';
                }
            } else {
                // Always show success for security
                $message = 'If that email exists, you will receive a reset link.';
                $message_type = 'success';
            }
        } catch (Exception $e) {
            $message = 'An error occurred. Please try again.';
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password - EducareHub</title>
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

        input[type="email"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
            background-color: #fafafa;
        }

        input[type="email"]:focus {
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

        .message {
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-header {
            margin-bottom: 25px;
        }

        .form-header h2 {
            color: #333;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-header p {
            color: #666;
            font-size: 14px;
            line-height: 1.4;
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
        
        <div class="form-header">
            <h2>🔐 Forgot Password?</h2>
            <p>Enter your email address and we'll send you a link to reset your password.</p>
        </div>
        
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address" required 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            
            <button type="submit" class="btn">Send Reset Link</button>
        </form>
        
        <div class="links">
            <a href="user_login.php">← Back to Login</a>
        </div>
    </div>
</body>
</html>