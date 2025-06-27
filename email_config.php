<?php
// email_config.php - Fixed Sender, Any Recipient
// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once 'vendor/autoload.php';

// SENDER ACCOUNT (Fixed - No Need to Change)
$mail_config = [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'username' => 'dindocedro236@gmail.com',     // Fixed sender
    'password' => 'hjai jddi sonz lsuf',         // Fixed password
    'from_email' => 'dindocedro236@gmail.com',   // Fixed sender
    'from_name' => 'EducareHub',
    'encryption' => 'tls'
];

// Function to send password reset to ANY email address
function sendPasswordReset($recipient_email, $reset_link) {
    global $mail_config;
    
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = $mail_config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $mail_config['username'];
        $mail->Password = $mail_config['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $mail_config['port'];
        
        // Fix SSL issues for localhost/development
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // FROM (always the same)
        $mail->setFrom($mail_config['from_email'], $mail_config['from_name']);
        
        // TO (can be any email address)
        $mail->addAddress($recipient_email);
        
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset - EducareHub';
        $mail->Body = getEmailTemplate($reset_link);
        $mail->AltBody = getPlainTextEmail($reset_link);
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

// Simple email template
function getEmailTemplate($reset_link) {
    return '
    <!DOCTYPE html>
    <html>
    <body style="font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;">
        <div style="max-width: 500px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px;">
            <h2 style="color: #007bff; text-align: center;">Password Reset</h2>
            <p>Hello,</p>
            <p>Click the button below to reset your password:</p>
            <div style="text-align: center; margin: 30px 0;">
                <a href="' . $reset_link . '" style="background: #007bff; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">Reset Password</a>
            </div>
            <p><strong>Link expires in 10 minutes.</strong></p>
            <p>If you didn\'t request this, ignore this email.</p>
        </div>
    </body>
    </html>';
}

// Plain text version
function getPlainTextEmail($reset_link) {
    return "Password Reset - EducareHub\n\n" .
           "Click this link to reset your password:\n" .
           $reset_link . "\n\n" .
           "Link expires in 10 minutes.\n" .
           "If you didn't request this, ignore this email.";
}

// Quick connection test
function testConnection() {
    global $mail_config;
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = $mail_config['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $mail_config['username'];
    $mail->Password = $mail_config['password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $mail_config['port'];
    
    try {
        $mail->smtpConnect();
        echo "✅ Email connection successful";
        $mail->smtpClose();
        return true;
    } catch (Exception $e) {
        echo "❌ Connection failed: " . $e->getMessage();
        return false;
    }
}
?>