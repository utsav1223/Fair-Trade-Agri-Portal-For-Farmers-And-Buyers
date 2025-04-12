<?php
session_start();
include("config1.php");

$msg = '';

if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
    header("Location: forgot_password.php");
    exit();
}

if (isset($_POST['reset'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        $email = $_SESSION['reset_email'];

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $new_password, $email);


        if ($stmt->execute()) {
            // Clear reset session data
            session_unset();
            session_destroy();
            header("Location: login.php?reset=success");
            exit();
        } else {
            $msg = "Something went wrong. Try again.";
        }
    } else {
        $msg = "Passwords do not match!";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="../output.css">
</head>

<body class="bg-green-50 flex justify-center items-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl font-semibold text-center text-green-700 mb-6">Set New Password</h2>

        <?php if ($msg): ?>
            <p class="text-red-500 text-center mb-4"><?php echo $msg; ?></p>
        <?php endif; ?>

        <form method="POST">
            <label class="block mb-2 text-green-800 font-medium">New Password</label>
            <input type="password" name="new_password" required class="w-full px-4 py-2 mb-4 border rounded-lg">

            <label class="block mb-2 text-green-800 font-medium">Confirm Password</label>
            <input type="password" name="confirm_password" required class="w-full px-4 py-2 mb-4 border rounded-lg">

            <button name="reset" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">Reset
                Password</button>
        </form>
    </div>
</body>

</html>