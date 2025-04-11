<?php
session_start();
include("config1.php");

$msg = '';
$email = $_SESSION['reset_email'] ?? null;

if (!$email) {
    header("Location: forgot_password.php");
    exit();
}

if (isset($_POST['submit'])) {
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    if ($password != $cpassword) {
        $msg = "Passwords do not match!";
    } else {
        $query = "UPDATE users SET password='$password' WHERE email='$email'";
        if (mysqli_query($conn, $query)) {
            session_unset();
            session_destroy();
            echo "<script>alert('Password successfully reset!'); window.location.href='login.php';</script>";
            exit();
        } else {
            $msg = "Something went wrong!";
        }
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
        <h2 class="text-xl font-semibold text-center text-green-700 mb-6">Reset Your Password</h2>

        <?php if ($msg): ?>
            <p class="text-red-500 text-center mb-4"><?php echo $msg; ?></p>
        <?php endif; ?>

        <form method="POST">
            <label class="block mb-2 text-green-800 font-medium">New Password</label>
            <input type="password" name="password" required class="w-full px-4 py-2 mb-4 border rounded-lg focus:ring-2 focus:ring-green-400">

            <label class="block mb-2 text-green-800 font-medium">Confirm New Password</label>
            <input type="password" name="cpassword" required class="w-full px-4 py-2 mb-6 border rounded-lg focus:ring-2 focus:ring-green-400">

            <button name="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">Reset Password</button>
        </form>
    </div>
</body>
</html>
