
<?php
session_start();
include("config1.php");
$error = '';
if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_type'] = $row['user_type'];
        if ($row['user_type'] == 'farmer') {
            header("Location: fardashboard.php");
            $_SESSION['logged_in'] === true;
        } else if ($row['user_type'] == 'buyer') {
            header("Location: buyer_dashboard.php");
            $_SESSION['logged_in'] === true;
        } else {
            header("Location: admin.php");
            $_SESSION['logged_in'] === true;
        }
        exit();
    } else {
        $error = "Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="min-h-screen flex justify-center items-center px-6 bg-green-50">
        <div class="bg-white w-full max-w-md p-12 rounded-2xl shadow-2xl text-green-900">
            <h3 class="text-center text-3xl font-bold mb-8 text-green-800">Login to Your Account</h3>

            <form action="login.php" class="w-full" method="post">
                <!-- Displaying error message -->
                <?php if (isset($error) && $error): ?>
                    <p class="text-red-600 text-center mb-4"><?php echo $error; ?></p>
                <?php endif; ?>

                <div class="mb-6">
                    <label for="email" class="block mb-2 font-medium text-green-900">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter Your Email Address"
                        class="w-full px-4 py-3 border border-green-300 bg-green-100 text-green-900 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none"
                        required>
                </div>

                <div class="mb-6 relative">
                    <label for="password" class="block mb-2 font-medium text-green-900">Enter Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter Your Password"
                        class="w-full px-4 py-3 border border-green-300 bg-green-100 text-green-900 rounded-lg pr-10 focus:ring-2 focus:ring-green-400 focus:outline-none"
                        required>
                    <span id="togglePassword" class="absolute right-3 top-10 cursor-pointer">
                        <i class="fa fa-eye text-green-600"></i>
                    </span>
                </div>

                <button type="submit"
                    class="w-full mt-4 bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition"
                    name="submit">Login</button>

                <p class="mt-4 text-center text-green-800">Don't have an account?
                    <a href="form.php" class="text-green-700 font-semibold hover:underline">Register</a>
                </p>
                <p class="mt-4 text-center text-green-800">
                    <a href="forgot_password.php" class="text-green-700 font-semibold hover:underline">Forgot
                        Password?</a>
                </p>

            </form>
        </div>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            let password = document.getElementById('password');
            password.type = password.type === 'password' ? 'text' : 'password';
        });
    </script>
</body>


</html>