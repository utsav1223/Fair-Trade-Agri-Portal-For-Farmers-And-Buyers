<?php
session_start();
include("config1.php");
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$msg = '';

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $otp = rand(100000, 999999);

    // Check email in DB
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_otp'] = $otp;

        // Setup PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'in-v3.mailjet.com'; // Mailjet SMTP host
            $mail->SMTPAuth   = true;
            $mail->Username   = 'a47bc0083eea25ca5e15d82ed8d31ebd'; // Your Mailjet API Key
            $mail->Password   = '339bcda7cb2cd7eeb82ac21ae879419c'; // Your Mailjet Secret Key
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Enable debug output
            $mail->SMTPDebug = 2;  // Show detailed debug information
            $mail->Debugoutput = 'html';  // Show debug output in HTML format

            // Recipients
            $mail->setFrom('utsavjha93030@gmail.com', 'Agri Portal');  // Replace with verified email
            $mail->addAddress($email); // Add recipient email

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP for Password Reset';
            $mail->Body    = "<h3>Reset OTP: <strong>$otp</strong></h3><p>Use this OTP to reset your password.</p>";

            // Send the email
            $mail->send();

            // Redirect to OTP verification page
            header("Location: reset_otp_verify.php");
            exit();
        } catch (Exception $e) {
            // If sending fails, show the error message
            $msg = "OTP sending failed. Error: {$mail->ErrorInfo}";
        }
    } else {
        $msg = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../output.css">
</head>
<body class="bg-green-50 flex justify-center items-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl font-semibold text-center text-green-700 mb-6">Forgot Password</h2>

        <?php if ($msg): ?>
            <p class="text-red-500 text-center mb-4"><?php echo $msg; ?></p>
        <?php endif; ?>

        <form method="POST">
            <label class="block mb-2 text-green-800 font-medium">Enter your Email</label>
            <input type="email" name="email" required class="w-full px-4 py-2 mb-4 border rounded-lg focus:ring-2 focus:ring-green-400">
            <button name="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">Send OTP</button>
        </form>
    </div>
</body>
</html>
