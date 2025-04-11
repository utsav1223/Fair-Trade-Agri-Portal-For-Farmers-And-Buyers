
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "database");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $village = mysqli_real_escape_string($conn, $_POST['Village']);
    $district = mysqli_real_escape_string($conn, $_POST['District']);
    $state = mysqli_real_escape_string($conn, $_POST['State']);
    $pincode = mysqli_real_escape_string($conn, $_POST['Pincode']);
    $farm_size = mysqli_real_escape_string($conn, $_POST['Farm_Size']);
    $type_of_crops = mysqli_real_escape_string($conn, $_POST['Type_of_crops']);

    // Handle Image Upload
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploaded_img/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

        // Update query with image
        $query = "UPDATE profile_management SET 
                    name='$name', email='$email', phone='$phone', 
                    Village='$village', District='$district', State='$state', 
                    Pincode='$pincode', Farm_Size='$farm_size', Type_of_crops='$type_of_crops', 
                    image='$image_name'
                  WHERE user_id='$user_id'";
    } else {
        // Update without changing image
        $query = "UPDATE profile_management SET 
                    name='$name', email='$email', phone='$phone', 
                    Village='$village', District='$district', State='$state', 
                    Pincode='$pincode', Farm_Size='$farm_size', Type_of_crops='$type_of_crops'
                  WHERE user_id='$user_id'";
    }

    if (mysqli_query($conn, $query)) {
        header("Location: fardashboard.php?success=1");
        exit();
    } else {
        echo "Error updating profile: " . mysqli_error($conn);
    }
}

$query = "SELECT * FROM profile_management WHERE user_id='$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="../output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 py-10">
<div class="max-w-4xl mx-auto bg-white p-10 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-6 text-center">Edit Profile</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="grid grid-cols-1 gap-4">
                
                <label class="block">
                    <span class="text-gray-700">Profile Image</span>
                    <input type="file" name="image" class="border p-2 w-full rounded">
                </label>
                <?php if (!empty($user['image'])): ?>
                    <img src="uploaded_img/<?= htmlspecialchars($user['image']) ?>" alt="Profile Picture" class="w-40 h-40 rounded-full object-cover mx-auto">
                <?php endif; ?>

                <label class="block">
                    <span class="text-gray-700">Name</span>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="border p-2 w-full rounded" required>
                </label>

                <label class="block">
                    <span class="text-gray-700">Email</span>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="border p-2 w-full rounded" required>
                </label>

                <label class="block">
                    <span class="text-gray-700">Phone</span>
                    <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" class="border p-2 w-full rounded" required>
                </label>

                <label class="block">
                    <span class="text-gray-700">Village</span>
                    <input type="text" name="Village" value="<?= htmlspecialchars($user['Village']) ?>" class="border p-2 w-full rounded">
                </label>

                <label class="block">
                    <span class="text-gray-700">District</span>
                    <input type="text" name="District" value="<?= htmlspecialchars($user['District']) ?>" class="border p-2 w-full rounded">
                </label>

                <label class="block">
                    <span class="text-gray-700">State</span>
                    <input type="text" name="State" value="<?= htmlspecialchars($user['State']) ?>" class="border p-2 w-full rounded">
                </label>

                <label class="block">
                    <span class="text-gray-700">Pincode</span>
                    <input type="text" name="Pincode" value="<?= htmlspecialchars($user['Pincode']) ?>" class="border p-2 w-full rounded">
                </label>

                <label class="block">
                    <span class="text-gray-700">Farm Size</span>
                    <input type="text" name="Farm_Size" value="<?= htmlspecialchars($user['Farm_Size']) ?>" class="border p-2 w-full rounded">
                </label>

                <label class="block">
                    <span class="text-gray-700">Type of Crops</span>
                    <input type="text" name="Type_of_crops" value="<?= htmlspecialchars($user['Type_of_crops']) ?>" class="border p-2 w-full rounded">
                </label>

            </div>
            <div class="flex justify-end mt-4">
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Update</button>
            </div>
        </form>
    </div>

</body>
</html>

<?php mysqli_close($conn); ?>
