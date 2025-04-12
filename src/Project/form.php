<?php
include("config1.php");
session_start();
$msg = '';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

if (isset($_POST['submit'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $user_type = htmlspecialchars($_POST['user_type']);

    if ($password != $cpassword) {
        $msg = "Passwords do not match!";
    } else {
        $query = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $msg = "User already exists!";
        } else {
            $otp = rand(100000, 999999);

            $checkOtp = mysqli_query($conn, "SELECT * FROM email_otps WHERE email='$email'");
            if (mysqli_num_rows($checkOtp) > 0) {
                mysqli_query($conn, "UPDATE email_otps SET otp='$otp' WHERE email='$email'");
            } else {
                mysqli_query($conn, "INSERT INTO email_otps (email, otp) VALUES ('$email', '$otp')");
            }

            $_SESSION['otp'] = $otp;

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'in-v3.mailjet.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'a47bc0083eea25ca5e15d82ed8d31ebd'; // Your Mailjet API key
                $mail->Password = '339bcda7cb2cd7eeb82ac21ae879419c'; // Your Mailjet Secret Key
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('utsavjha93030@gmail.com', 'Agri Portal'); // Replace with verified email
                $mail->addAddress($email, $name);

                $mail->isHTML(true);
                $mail->Subject = 'Your OTP for Email Verification';
                $mail->Body    = "<p>Dear $name,<br><br>Your OTP for email verification is: <strong>$otp</strong><br><br>Regards,<br>Agri Portal Team</p>";

                $mail->SMTPDebug = 2; // Enable verbose debug output
                $mail->send();
                
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                $_SESSION['password'] = $password;
                $_SESSION['user_type'] = $user_type;
                header('Location: verify_otp.php');
                exit();
            } catch (Exception $e) {
                $msg = "Failed to send OTP. Mailer Error: {$mail->ErrorInfo}";
                echo 'Mailer Error: ' . $mail->ErrorInfo; // Debugging error output
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Form</title>
    <link rel="stylesheet" href="../output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* Terms and conditions */
        .modal {
            display: none;
            position: fixed;
            z-index: 50;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-active {
            display: flex;
        }

        .modal-content {
            max-height: 70vh;
            overflow-y: auto;
        }

        .relative {
            position: relative;
        }

        input[type="password"] {
            padding-right: 1rem;
            /* Space for the eye icon */
        }

        span[id^="toggle"] {
            position: absolute;
            right: 0.75rem;
            top: 72%;
            transform: translateY(-50%);
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen bg-green-50">
<div class="min-h-screen flex justify-center items-center px-6 lg:px-20 bg-green-50">
    <div class="form-container w-full max-w-3xl p-12 rounded-2xl shadow-2xl bg-white text-green-900">
        <h3 class="text-center text-3xl font-extrabold text-green-800 mb-8">Register Your Account</h3>

        <form action="form.php" onsubmit="return validateForm() && showSuccessMessage()" method="post">
            <p class="text-center text-red-600 font-medium mb-4"><?php echo "$msg"; ?></p>

            <!-- Full Name and Email in single column -->
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="name" class="block mb-2 font-semibold text-green-700">Full Name:</label>
                    <input type="text" id="name" name="name" placeholder="Enter Your Full Name"
                        class="w-full px-4 py-3 border border-green-300 bg-green-50 text-green-800 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none"
                        required>
                </div>

                <div>
                    <label for="email" class="block mb-2 font-semibold text-green-700">Email Address:</label>
                    <input type="email" id="email" name="email" placeholder="Enter Your Email"
                        class="w-full px-4 py-3 border border-green-300 bg-green-50 text-green-800 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none"
                        required>
                </div>
            </div>

            <div class="mt-6">
                <label for="user_type" class="block mb-2 font-semibold text-green-700">Select Category</label>
                <select name="user_type" id="user_type"
                    class="w-full p-3 border border-green-300 bg-green-50 text-green-800 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none">
                    <option value="farmer">Farmer</option>
                    <option value="buyer">Buyer</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div class="relative">
                    <label for="password" class="block mb-2 font-semibold text-green-700">Enter Password:</label>
                    <input type="password" id="password" name="password" placeholder="Enter Your Password"
                        class="w-full px-4 py-3 border border-green-300 bg-green-50 text-green-800 rounded-lg pr-10 focus:ring-2 focus:ring-green-400 focus:outline-none"
                        required>
                    <span id="togglePassword" class="absolute right-3 top-10 cursor-pointer">
                        <i class="fa fa-eye text-gray-400"></i>
                    </span>
                </div>

                <div class="relative">
                    <label for="cpassword" class="block mb-2 font-semibold text-green-700">Confirm Password:</label>
                    <input type="password" id="cpassword" name="cpassword" placeholder="Confirm Your Password"
                        class="w-full px-4 py-3 border border-green-300 bg-green-50 text-green-800 rounded-lg pr-10 focus:ring-2 focus:ring-green-400 focus:outline-none"
                        required>
                    <span id="toggleCPassword" class="absolute right-3 top-10 cursor-pointer">
                        <i class="fa fa-eye text-gray-400"></i>
                    </span>
                </div>
            </div>

            <div class="mt-6 flex items-center">
                <input type="checkbox" id="terms" name="terms" value="agree" class="mr-2 accent-green-600">
                <label for="terms" class="text-sm text-green-700">I agree to the <a href="#" id="terms-link"
                        class="text-green-600 font-medium hover:underline">Terms & Conditions</a></label>
            </div>

            <button type="submit"
                class="w-full mt-6 bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-500 transition"
                name="submit">Register</button>
        </form>
    </div>
</div>


<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        let password = document.getElementById('password');
        password.type = password.type === 'password' ? 'text' : 'password';
    });

    document.getElementById('toggleCPassword').addEventListener('click', function () {
        let cpassword = document.getElementById('cpassword');
        cpassword.type = cpassword.type === 'password' ? 'text' : 'password';
    });
</script>


    <!-- Terms and Conditions Tab -->
    <div class="bg-gray-100 flex items-center justify-center h-screen">

        <!-- Modal -->
        <div id="terms-modal" class="modal">
            <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 md:w-1/2 lg:w-1/3 relative">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="text-lg font-semibold">Terms and Conditions</h3>
                    <button id="close-btn" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="mt-4 modal-content">
                    <p class="text-sm text-gray-600">
                        <strong>1. Introduction</strong><br>
                        Welcome to the Fair Trade Agri Portal. Our platform aims to connect farmers and buyers to
                        promote
                        fair trade practices and enhance the agricultural market. By using our website, you agree to
                        abide
                        by the following terms and conditions.<br><br>

                        <strong>2. Acceptance of Terms</strong><br>
                        By accessing or using our services, you agree to these terms and conditions. If you do not
                        agree,
                        you must not use our services.<br><br>
                        <strong>Key Points:</strong><br>
                        - Users must accept terms to use the platform.<br>
                        - Non-acceptance means you cannot use the services.<br><br>

                        <strong>3. User Responsibilities</strong><br>
                        Users are responsible for providing accurate and complete information during registration and
                        maintaining the security of their login credentials. Users must comply with all applicable laws
                        and
                        engage in fair trading practices.<br><br>
                        <strong>Key Points:</strong><br>
                        - Provide accurate and complete information.<br>
                        - Keep login credentials secure.<br>
                        - Comply with all applicable laws.<br>
                        - Engage in fair trading practices.<br><br>

                        <strong>4. Fair Trade Practices</strong><br>
                        We are committed to promoting fair trade practices. Users must adhere to the principles of
                        transparency, fairness, and respect in all transactions. Unacceptable practices include fraud,
                        deceit, and exploitation.<br><br>
                        <strong>Key Points:</strong><br>
                        - Adhere to transparency, fairness, and respect.<br>
                        - Avoid fraud, deceit, and exploitation.<br><br>

                        <strong>5. Payment and Transactions</strong><br>
                        All payments and transactions on our platform are secure and transparent. Users are responsible
                        for
                        any applicable fees and charges. We do not take responsibility for any disputes between buyers
                        and
                        sellers.<br><br>
                        <strong>Key Points:</strong><br>
                        - Secure and transparent payments.<br>
                        - Users are responsible for fees and charges.<br>
                        - No responsibility for buyer-seller disputes.<br><br>

                        <strong>6. Data Privacy and Security</strong><br>
                        We collect and use user data in accordance with our privacy policy. User data is protected and
                        will
                        not be shared with third parties without consent. For more details, please refer to our privacy
                        policy.<br><br>
                        <strong>Key Points:</strong><br>
                        - Data collected and used according to privacy policy.<br>
                        - User data protected and not shared without consent.<br><br>

                        <strong>7. Intellectual Property</strong><br>
                        All content on our website is protected by intellectual property laws. Users must not use, copy,
                        or
                        distribute any content without permission.<br><br>
                        <strong>Key Points:</strong><br>
                        - Content protected by intellectual property laws.<br>
                        - No unauthorized use, copying, or distribution.<br><br>

                        <strong>8. Termination</strong><br>
                        We reserve the right to terminate user accounts for any violations of these terms and
                        conditions.
                        Users may also terminate their accounts at any time.<br><br>
                        <strong>Key Points:</strong><br>
                        - Right to terminate accounts for violations.<br>
                        - Users can terminate accounts at any time.<br><br>

                        <strong>9. Limitation of Liability</strong><br>
                        We are not liable for any damages or losses resulting from the use of our platform. Users use
                        the
                        platform at their own risk.<br><br>
                        <strong>Key Points:</strong><br>
                        - No liability for damages or losses.<br>
                        - Use platform at your own risk.<br><br>

                        <strong>10. Governing Law</strong><br>
                        These terms and conditions are governed by the laws of [Your Country/Region]. Any disputes
                        arising
                        from these terms will be resolved under the jurisdiction of [Your Country/Region].<br><br>
                        <strong>Key Points:</strong><br>
                        - Governed by the laws of [Your Country/Region].<br>
                        - Disputes resolved under [Your Country/Region] jurisdiction.<br><br>

                        <strong>11. Changes to Terms and Conditions</strong><br>
                        We may update these terms and conditions periodically. Users will be notified of any significant
                        changes.<br><br>
                        <strong>Key Points:</strong><br>
                        - Terms may be updated periodically.<br>
                        - Users will be notified of significant changes.<br><br>

                        Feel free to customize this template to better fit the specifics of your platform. It’s always a
                        good idea to consult a legal professional to ensure compliance with all relevant laws and
                        regulations.<br><br>
                        Let me know if you need more details or further adjustments!
                    </p>
                </div>
                <div class="mt-6 text-right">
                    <button id="agree-btn"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Agree</button>
                </div>
            </div>
        </div>
    </div>
    <!-- javascript part goes here -->
    <script>
        function validateForm() {
    // Get the checkbox element
    var checkBox = document.getElementById("terms");

    // If the checkbox is not checked
    if (!checkBox.checked) {
        alert("You must agree to the Terms & Conditions before registering.");
        return false; // Stop further processing
    }

    // Checkbox is checked, allow form submission
    return true;
}

function showSuccessMessage() {
    alert("Successfully Registered! Redirecting to login...");
    // Continue with form submission and redirection
    return true;
}

        //terms and conditions
        const termsLink = document.getElementById('terms-link');
        const termsModal = document.getElementById('terms-modal');
        const closeBtn = document.getElementById('close-btn');
        const agreeBtn = document.getElementById('agree-btn');

        termsLink.addEventListener('click', (event) => {
            event.preventDefault();
            termsModal.classList.add('modal-active');
        });

        closeBtn.addEventListener('click', (event) => {
            event.preventDefault();
            termsModal.classList.remove('modal-active');
        });

        agreeBtn.addEventListener('click', (event) => {
            event.preventDefault();
            termsModal.classList.remove('modal-active');
        });
    </script>
</body>

</html>