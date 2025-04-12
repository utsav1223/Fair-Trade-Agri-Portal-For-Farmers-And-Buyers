<?php
session_start();
$msg = '';

if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_otp'])) {
    header("Location: forgot_password.php");
    exit();
}

if (isset($_POST['verify'])) {
    $user_otp = $_POST['otp'];
    if ($user_otp == $_SESSION['reset_otp']) {
        // OTP verified successfully
        $_SESSION['otp_verified'] = true;
        header("Location: reset_password.php");
        exit();
    } else {
        $msg = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
    <link rel="stylesheet" href="../output.css">
</head>
<body class="bg-green-50 flex justify-center items-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl font-semibold text-center text-green-700 mb-6">OTP Verification</h2>

        <?php if ($msg): ?>
            <p class="text-red-500 text-center mb-4"><?php echo $msg; ?></p>
        <?php endif; ?>

        <form method="POST">
            <label class="block mb-2 text-green-800 font-medium">Enter OTP sent to your Email</label>
            <input type="text" name="otp" required class="w-full px-4 py-2 mb-4 border rounded-lg focus:ring-2 focus:ring-green-400">

            <button name="verify" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">Verify OTP</button>
        </form>
    </div>
</body>
</html>
