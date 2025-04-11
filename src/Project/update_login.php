
<?php
include("config1.php");
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Fetch the user's current login details
$query_user = "SELECT * FROM `users` WHERE id = '$user_id'";
$result = mysqli_query($conn, $query_user);

if (mysqli_num_rows($result) < 1) {
    // If no user is found, redirect to the dashboard
    header("Location: fardashboard.php");
    exit();
}

$user = mysqli_fetch_assoc($result); // Fetch user data as an associative array
$msg = ""; // Initialize the message variable

// Handle the form submission for updating details
if (isset($_POST['update_login'])) {
    $old_password = $_POST['old_password']; // Retrieve the old password entered by the user
    $email = htmlspecialchars($_POST['email']); // New email
    $password = $_POST['password']; // New password
    $cpassword = $_POST['cpassword']; // Confirm password

    // Validate the old password
    if ($old_password !== $user['password']) {
        $msg = "Old password is incorrect!";
    } elseif ($password != $cpassword) {
        $msg = "New passwords do not match!";
    } else {
        // Update the user's email and password in the database
        $update_query = "UPDATE `users` SET email = '$email', password = '$password' WHERE id = '$user_id'";

        if (mysqli_query($conn, $update_query)) {
            // Redirect back to the dashboard after successful update
            header("Location: fardashboard.php?success=1");
            exit();
        } else {
            $msg = "Failed to update login details. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Login Details</title>
    <link rel="stylesheet" href="../output.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Update Login Details</h2>

        <!-- Display error/success messages -->
        <?php if (!empty($msg)): ?>
            <p class="text-red-500 text-center mb-4"><?php echo $msg; ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-4">
                <label for="email" class="block text-gray-600 font-medium">New Email</label>
                <input type="email" id="email" name="email" required
                    value="<?php echo htmlspecialchars($user['email']); ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="mb-4">
                <label for="old_password" class="block text-gray-600 font-medium">Old Password</label>
                <input type="password" id="old_password" name="old_password" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-600 font-medium">New Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="mb-6">
                <label for="cpassword" class="block text-gray-600 font-medium">Confirm Password</label>
                <input type="password" id="cpassword" name="cpassword" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <button type="submit" name="update_login"
                class="w-full bg-blue-500 hover:bg-blue-600  px-6  text-lg shadow-md transition-all duration-300 text-white font-medium py-2 rounded-lg">
                Update Login Details
            </button>
        </form>
    </div>
</body>
</html>

