<?php
session_start();
include("config1.php");

$msg = '';

// Check if OTP form is submitted
if (isset($_POST['verify'])) {
    $entered_otp = htmlspecialchars($_POST['otp']);
    $email = $_SESSION['email'];

    // Fetch OTP from database (valid for 5 minutes)
    $query = "SELECT * FROM email_otps WHERE email='$email' AND otp='$entered_otp' AND created_at >= NOW() - INTERVAL 5 MINUTE";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Insert into users table
        $name = $_SESSION['name'];
        $password = $_SESSION['password'];
        $user_type = $_SESSION['user_type'];

        $insert = "INSERT INTO users (name, email, password, user_type) VALUES ('$name', '$email', '$password', '$user_type')";
        if (mysqli_query($conn, $insert)) {
            // Optionally delete OTP from table
            $deleteOtp = "DELETE FROM email_otps WHERE email='$email'";
            mysqli_query($conn, $deleteOtp);

            // Clear session
            session_unset();
            session_destroy();

            // Redirect to login
            header("Location: login.php");
            exit();
        } else {
            $msg = "Registration failed!";
        }
    } else {
        $msg = "Invalid or expired OTP!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="../output.css">
</head>
<body class="bg-green-50 min-h-screen flex items-center justify-center">

    <div class="bg-white p-10 rounded-xl shadow-md w-full max-w-md border border-green-300">
        <h2 class="text-2xl font-bold text-green-700 mb-4 text-center">Email Verification</h2>
        <?php if ($msg != ''): ?>
            <p class="text-red-500 text-center mb-4"><?php echo $msg; ?></p>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
            <div>
                <label for="otp" class="block text-green-700 font-medium mb-2">Enter the OTP sent to your email:</label>
                <input type="text" id="otp" name="otp" maxlength="6"
                    class="w-full px-4 py-2 border border-green-300 bg-green-50 text-green-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400"
                    required>
            </div>
            <button type="submit" name="verify"
                class="w-full bg-green-600 text-white py-2 rounded-lg font-semibold hover:bg-green-500 transition">
                Verify OTP
            </button>
        </form>
    </div>

</body>
</html>
