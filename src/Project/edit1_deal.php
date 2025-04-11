
<?php
require 'admin_config1.php'; // Ensure database connection is included

if (!isset($_GET['id'])) {
    die("Invalid request.");
}
$deal_id = $_GET['id'];

// Debugging: Check if connection is established
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
// Fetch deal details with error handling
$query = "SELECT * FROM deal WHERE id = $deal_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) !== 1) {
    die("Deal not found.");
}
$deal = mysqli_fetch_assoc($result);
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = mysqli_real_escape_string($conn, $_POST['Product_Name']);
    $product_desc = mysqli_real_escape_string($conn, $_POST['Product_desc']);
    $price = (float) $_POST['Price_per_unit'];
    $product_link = mysqli_real_escape_string($conn, $_POST['Product_Link']);

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploaded_img/";
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_image_name = $target_dir . "deal_" . time() . "." . $file_extension; // Unique image name

        if (is_uploaded_file($_FILES['image']['tmp_name']) && move_uploaded_file($_FILES['image']['tmp_name'], $new_image_name)) {
            $image = $new_image_name;
        } else {
            echo "Error uploading image.";
            $image = $deal['image']; // Keep old image if upload fails
        }
    } else {
        $image = $deal['image']; // Keep old image if no new image is uploaded
    }

    // Update query without bind_param
    $update_query = "UPDATE deal SET 
                        Product_Name='$product_name', 
                        Product_desc='$product_desc', 
                        Price_per_unit=$price, 
                        pd_link='$product_link', 
                        image='$image' 
                    WHERE id=$deal_id";

    if (mysqli_query($conn, $update_query)) {
        header("Location: admin.php"); // Redirect to admin panel after update
        exit();
    } else {
        echo "Error updating deal: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Deal</title>
    <link rel="stylesheet" href="../output.css">
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen p-6">
    <div class="max-w-3xl w-full bg-white p-12 rounded-lg shadow-lg">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Edit Deal</h2>
        <form method="POST" enctype="multipart/form-data" class="space-y-6">

            <!-- Product Name -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Product Name</label>
                <input type="text" name="Product_Name" value="<?= htmlspecialchars($deal['Product_Name']) ?>"
                    class="w-full p-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
            </div>

            <!-- Product Description -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Product Description</label>
                <textarea name="Product_desc"
                    class="w-full p-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                    required><?= htmlspecialchars($deal['Product_desc']) ?></textarea>
            </div>

            <!-- Price -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Price (₹)</label>
                <input type="number" name="Price_per_unit" value="<?= htmlspecialchars($deal['Price_per_unit']) ?>"
                    class="w-full p-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
            </div>

            <!-- Product Link -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Product Link</label>
                <input type="url" name="Product_Link" value="<?= htmlspecialchars($deal['pd_link']) ?>"
                    class="w-full p-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
            </div>

            <!-- Product Image -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Product Image</label>
                <input type="file" name="image" class="w-full p-3 border rounded-lg">
                <div class="mt-3">
                    <span class="text-gray-600 text-sm">Current Image:</span>
                    <img src="<?= $deal['image'] ?>" alt="Current Image"
                        class="h-64 w-full object-cover mt-2 rounded-lg shadow-md">
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-between items-center mt-6">
                <a href="admin.php" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition">
                    Cancel
                </a>
                <button type="submit"
                    class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition">
                    Update Deal
                </button>
            </div>
        </form>
    </div>
</body>

</html>