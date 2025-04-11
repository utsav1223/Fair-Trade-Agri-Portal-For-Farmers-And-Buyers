
<?php
include("config1.php");
$id = $_GET['id'] ?? 0;
$buyer = null;
$successMessage = "";
// Fetch Existing Data
if ($id) {
    $result = $conn->query("SELECT * FROM profile_management1 WHERE id = $id");
    if ($result->num_rows > 0) {
        $buyer = $result->fetch_assoc();
    } else {
        die("Profile not found!");
    }
}
// Update Data in Database
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_no = $_POST['phone_no'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip_code = $_POST['zip_code'];
    $country = $_POST['country'];

    $sql = "UPDATE profile_management1 SET 
            name1='$name', email1='$email', phone_no='$phone_no', 
            address='$address', city='$city', state='$state', 
            zip_code='$zip_code', country='$country' 
            WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        $successMessage = "Profile updated successfully!";
    } else {
        $successMessage = "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../output.css">
    <script>
        // Message Auto-Close Function
        function hideMessage() {
            setTimeout(() => {
                let messageBox = document.getElementById("successMessage");
                if (messageBox) {
                    messageBox.style.display = "none";
                }
            }, 3000); // 3 seconds
        }
    </script>
</head>
<body class="bg-gray-100 text-gray-900" onload="hideMessage()">
    <div class="max-w-4xl mx-auto p-6">
        <div class="bg-white p-6 rounded-lg shadow mb-6 text-center border-b-4 border-blue-500">
            <h1 class="text-3xl font-bold">Edit Profile</h1>
        </div>

        <!-- Success Message (Auto Disappearing) -->
        <?php if (!empty($successMessage)) { ?>
            <div id="successMessage" class="text-center text-green-600 font-bold mb-4 bg-green-100 p-2 rounded-lg">
                <?= $successMessage ?>
            </div>
        <?php } ?>

        <div class="bg-white shadow rounded-lg p-6 border border-gray-300">
            <h2 class="text-xl font-semibold mb-4">Update Profile Information</h2>
            <form class="grid grid-cols-1 md:grid-cols-2 gap-4" method="POST">
                <input type="text" name="name" value="<?= $buyer['name1'] ?>" class="p-3 border rounded-lg focus:outline-blue-500" required>
                <input type="email" name="email" value="<?= $buyer['email1'] ?>" class="p-3 border rounded-lg focus:outline-blue-500" required>
                <input type="tel" name="phone_no" value="<?= $buyer['phone_no'] ?>" class="p-3 border rounded-lg focus:outline-blue-500" required>
                <input type="text" name="address" value="<?= $buyer['address'] ?>" class="p-3 border rounded-lg focus:outline-blue-500" required>
                <input type="text" name="city" value="<?= $buyer['city'] ?>" class="p-3 border rounded-lg focus:outline-blue-500" required>
                <input type="text" name="state" value="<?= $buyer['state'] ?>" class="p-3 border rounded-lg focus:outline-blue-500" required>
                <input type="text" name="zip_code" value="<?= $buyer['zip_code'] ?>" class="p-3 border rounded-lg focus:outline-blue-500" required>
                <input type="text" name="country" value="<?= $buyer['country'] ?>" class="p-3 border rounded-lg focus:outline-blue-500" required>
                <button type="submit" name="update" class="bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-600 col-span-1 md:col-span-2">Update Profile</button>
            </form>
        </div>
        
        <div class="text-center mt-4">
            <a href="buyer_dashboard.php" class="text-blue-500 hover:underline">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
