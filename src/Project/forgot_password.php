<?php
session_start();
include("config1.php");

$msg = '';

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $otp = rand(100000, 999999);

    // Check if email exists
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_otp'] = $otp;

        // You can integrate actual mail function here
        echo "<script>alert('Your OTP is: $otp'); window.location.href='reset_otp_verify.php';</script>";
        exit();
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
