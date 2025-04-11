
<?php
include("config1.php");
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Fetch the product ID from the URL
if (!isset($_GET['id'])) {
    header("Location: fardashboard.php");
    exit();
}
$product_id = $_GET['id'];

// Fetch product details
$query_product = "SELECT * FROM products1 WHERE id='$product_id' AND user_id='$user_id'";
$result_product = mysqli_query($conn, $query_product);

if (mysqli_num_rows($result_product) < 1) {
    header("Location: fardashboard.php");
    exit();
}

$product = mysqli_fetch_assoc($result_product);

// Handle form submission for updating the product
if (isset($_POST['update_product'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $available_quantity = $_POST['available_quantity'];
    $product_desc = $_POST['product_desc'];
    $category = $_POST['category'];
    $location = $_POST['location'];
    $expected_delivery_date = $_POST['expected_delivery'];
    $product_image = $_FILES['product_image']['name'];
    $product_image_tmp_name = $_FILES['product_image']['tmp_name'];
    $product_image_folder = 'uploaded_img/' . $product_image;

    $update_query = "UPDATE products1 
                     SET Product_Name='$product_name', 
                         Price_per_unit='$product_price', 
                         Available_Quantity='$available_quantity', 
                         Product_desc='$product_desc', 
                         Category='$category', 
                         Location='$location',
                         delivery_date='$expected_delivery_date'";

    // Handle image update if a new image is uploaded
    if (!empty($product_image)) {
        $update_query .= ", image='$product_image'";
        move_uploaded_file($product_image_tmp_name, $product_image_folder);
    }

    $update_query .= " WHERE id='$product_id' AND user_id='$user_id'";

    if (mysqli_query($conn, $update_query)) {
        header("Location: fardashboard.php?success=1");// Redirect to dashboard
        exit();
    } else {
        echo "<p class='text-red-500 text-center'>Failed to update the product. Please try again.</p>";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../output.css">
</head>

<body>
    <div class="max-w-7xl mx-auto mt-12 bg-gray-100 p-10 shadow-md rounded-xl">
        <h2 class="text-3xl font-bold text-gray-800 text-center mb-8">Edit Product Details</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Product Name -->
                <div>
                    <label for="product_name" class="block text-lg font-medium text-gray-700 mb-2">Product Name</label>
                    <input type="text" id="product_name" name="product_name"
                        value="<?php echo htmlspecialchars($product['Product_Name']); ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <!-- Price per Unit -->
                <div>
                    <label for="product_price" class="block text-lg font-medium text-gray-700 mb-2">Price per
                        Unit</label>
                    <input type="text" id="product_price" name="product_price"
                        value="<?php echo htmlspecialchars($product['Price_per_unit']); ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <!-- Available Quantity -->
                <div>
                    <label for="available_quantity" class="block text-lg font-medium text-gray-700 mb-2">Available
                        Quantity</label>
                    <input type="number" id="available_quantity" name="available_quantity"
                        value="<?php echo htmlspecialchars($product['Available_Quantity']); ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-lg font-medium text-gray-700 mb-2">Category</label>
                    <input type="text" id="category" name="category"
                        value="<?php echo htmlspecialchars($product['Category']); ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>



                <div>
                    <label class="block text-lg font-medium text-gray-700 mb-2">
                        Expected Delivery Date
                    </label>
                    <input id="deliveryDate"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        type="date" name="expected_delivery"
                        value="<?php echo !empty($product['Expected_Delivery']) ? htmlspecialchars(date('Y-m-d', strtotime($product['Expected_Delivery']))) : ''; ?>"
                        required onchange="formatDate()" />
                    <p id="formattedDate" class="mt-2 text-gray-700 font-semibold">
                        <?php
                        if (!empty($product['Expected_Delivery'])) {
                            echo date('l, j F', strtotime($product['Expected_Delivery']));
                        }
                        ?>
                    </p>
                </div>

                <script>
                    function formatDate() {
                        let dateInput = document.getElementById("deliveryDate").value;
                        if (dateInput) {
                            let dateObj = new Date(dateInput + "T00:00:00"); // Fix timezone issue
                            let options = { weekday: 'long', day: 'numeric', month: 'long' };
                            let formattedDate = dateObj.toLocaleDateString('en-GB', options);
                            document.getElementById("formattedDate").innerText = formattedDate;
                        }
                    }
                </script>





                <!-- Location -->
                <div>
                    <label for="location" class="block text-lg font-medium text-gray-700 mb-2">Location</label>
                    <input type="text" id="location" name="location"
                        value="<?php echo htmlspecialchars($product['Location']); ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <label for="product_desc" class="block text-lg font-medium text-gray-700 mb-2">Description</label>
                <textarea id="product_desc" name="product_desc" rows="4" maxlength="20"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"><?php echo htmlspecialchars($product['Product_desc']); ?></textarea>
            </div>

            <!-- Product Image -->
            <div class="mt-6">
                <label for="product_image" class="block text-lg font-medium text-gray-700 mb-2">Product Image</label>
                <input type="file" id="product_image" name="product_image"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <p class="text-sm text-gray-500 mt-2">Current Image: <?php echo htmlspecialchars($product['image']); ?>
                </p>
            </div>



            <!-- Submit Button -->
            <div class="flex justify-end mt-8">
                <button type="submit" name="update_product"
                    class="bg-green-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-green-700 transition duration-300">
                    Update Product
                </button>
            </div>
        </form>
    </div>
</body>

</html>