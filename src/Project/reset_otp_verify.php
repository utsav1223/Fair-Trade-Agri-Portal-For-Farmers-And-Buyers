<?php
session_start();
$msg = '';

if (!isset($_SESSION['reset_otp'])) {
    header("Location: forgot_password.php");
    exit();
}

if (isset($_POST['submit'])) {
    $entered_otp = $_POST['otp'];

    if ($entered_otp == $_SESSION['reset_otp']) {
        header("Location: reset_password.php");
        exit();
    } else {
        $msg = "Invalid OTP!";
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
        <h2 class="text-xl font-semibold text-center text-green-700 mb-6">Enter OTP</h2>

        <?php if ($msg): ?>
            <p class="text-red-500 text-center mb-4"><?php echo $msg; ?></p>
        <?php endif; ?>

        <form method="POST">
            <label class="block mb-2 text-green-800 font-medium">OTP</label>
            <input type="text" name="otp" required class="w-full px-4 py-2 mb-4 border rounded-lg focus:ring-2 focus:ring-green-400">

            <button name="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">Verify OTP</button>
        </form>
    </div>
</body>
</html>
