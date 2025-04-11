<?php
session_start();
include "config1.php"; // Database Connection


if (!isset($_SESSION['user_type'])) {
    $_SESSION['redirect_after_login'] = "product_details.php?id=" . $_GET['id'];
    header("Location: login.php");
    exit();
}

// Check if product ID is provided in the URL
if (isset($_GET['id'])) {
    $product_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Fetch Product Details
    $product_query = "SELECT * FROM products1 WHERE id = '$product_id'";
    $product_result = mysqli_query($conn, $product_query);

    if ($product_row = mysqli_fetch_assoc($product_result)) {
        $farmer_id = $product_row['user_id']; // Get Farmer's User ID

        // Fetch Farmer Details including profile image
        $farmer_query = "SELECT * FROM profile_management WHERE user_id = '$farmer_id'";
        $farmer_result = mysqli_query($conn, $farmer_query);
        $farmer_row = mysqli_fetch_assoc($farmer_result);
    } else {
        echo "<p class='text-red-500 text-center text-lg'>Product not found!</p>";
        exit();
    }
} else {
    echo "<p class='text-red-500 text-center text-lg'>Invalid request!</p>";
    exit();
}

// Initialize cart session if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    if ($_SESSION['user_type'] !== 'buyer') {
        $_SESSION['cart_message'] = "Only buyers can add products to cart!";
        header("Location: product_details.php?id=" . $product_id);
        exit();
    }

    $buyer_id = $_SESSION['user_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    $price = $product_row['Price_per_unit'];
    $total_price = $price * $quantity;
    $image = $product_row['image'];
    $available_quantity = $product_row['Available_Quantity'];
    $product_name = $product_row['Product_Name'];
    $farmer_id = $product_row['user_id']; // Ensure correct farmer_id is used

    // Debugging to check if $farmer_id is correctly fetched
    if (!$farmer_id) {
        echo "Error: Farmer ID not found!";
        exit();
    }

    // Check if product already exists in cart
    $cart_check_query = "SELECT * FROM cart WHERE buyer_id = '$buyer_id' AND product_id = '$product_id'";
    $cart_check_result = mysqli_query($conn, $cart_check_query);

    if (mysqli_num_rows($cart_check_result) > 0) {
        // Update quantity instead of inserting new row
        $cart_row = mysqli_fetch_assoc($cart_check_result);
        $new_quantity = $cart_row['quantity'] + $quantity;
        if ($new_quantity > $available_quantity) {
            $_SESSION['cart_message'] = "Not enough stock available!";
        } else {
            $update_cart_query = "UPDATE cart SET 
                quantity = '$new_quantity', 
                total_price = '$price' * '$new_quantity' 
                WHERE buyer_id = '$buyer_id' AND product_id = '$product_id'";
            
            if (!mysqli_query($conn, $update_cart_query)) {
                echo "Error updating cart: " . mysqli_error($conn);
                exit();
            }

            $_SESSION['cart_message'] = "✅ Quantity updated in the cart!";
        }
    } else {
        // Insert new entry if product is not in cart
        $insert_cart_query = "INSERT INTO cart 
            (buyer_id, product_id, prod_name1, quantity, price, total_price, available_quantity, image, farmer_id, created_at) 
            VALUES 
            ('$buyer_id', '$product_id', '$product_name', '$quantity', '$price', '$total_price', '$available_quantity', '$image', '$farmer_id', NOW())";

        if (!mysqli_query($conn, $insert_cart_query)) {
            echo "Error inserting into cart: " . mysqli_error($conn);
            exit();
        }

        $_SESSION['cart_message'] = "✅ Successfully added to the cart!";
    }

    header("Location: product_details.php?id=" . $product_id);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="stylesheet" href="../output.css">
</head>

<body class="bg-green-100">
<!-- Back Button -->
<!-- <div class=" top-5 left-5 fixed">
    <a href="index.php" class="bg-gray-800 text-white px-4 py-2 rounded-lg shadow-md hover:bg-gray-700 transition-all">
        ⬅ Back
    </a>
</div> -->

    <!-- Main Container -->
    <div class="max-w-5xl mx-auto py-12 px-6">

        <!-- Success message after adding to cart -->
        <?php if (isset($_SESSION['cart_message'])) { ?>
            <p class="text-green-500 text-center text-lg">
                <?php echo $_SESSION['cart_message'];
                unset($_SESSION['cart_message']); ?></p>
        <?php } ?>

        <!-- Product Details -->
        <div
            class=" shadow-lg rounded-2xl p-8 border border-gray-200 hover:shadow-2xl transition-all duration-300">
            <div class="relative">
                <img src="uploaded_img/<?php echo htmlspecialchars($product_row['image']); ?>" alt="Product Image"
                    class="w-full h-72 object-cover rounded-2xl shadow-md">
                <span class="absolute top-3 right-3 bg-green-700 text-white px-3 py-1 rounded-lg text-sm font-semibold">
                    <?php echo htmlspecialchars($product_row['Category']); ?>
                </span>
            </div>

            <h2 class="text-3xl font-bold text-gray-900 mt-6">
                <?php echo htmlspecialchars($product_row['Product_Name']); ?></h2>
            <p class="text-gray-700 text-lg mt-2"><strong>Description:</strong>
                <?php echo htmlspecialchars($product_row['Product_desc']); ?></p>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <p class="text-gray-700 text-lg"><strong>Price per Unit:</strong>
                    ₹<?php echo htmlspecialchars($product_row['Price_per_unit']); ?></p>
                <p class="text-gray-700 text-lg"><strong>Available Quantity:</strong>
                    <?php echo htmlspecialchars($product_row['Available_Quantity']); ?></p>
                <p class="text-gray-700 text-lg"><strong>Location:</strong>
                    <?php echo htmlspecialchars($product_row['Location']); ?></p>
                <p class="text-gray-700 text-lg"><strong>Expected Delivery Date - </strong>
                    <?php
                    if (!empty($product_row['delivery_date'])) {
                        echo date("l, j F", strtotime($product_row['delivery_date']));
                    } else {
                        echo "Not Available";
                    }
                    ?>
                </p>
            </div>

            <!-- Farmer Details -->
            <div class="mt-10 b text-black rounded-2xl p-8">
                <h2 class="text-3xl font-bold text-center mb-6">Farmer Details</h2>

                <?php if ($farmer_row) { ?>
                    <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                        <!-- Farmer Profile Image -->
                        <div class="w-32 h-32 md:w-40 md:h-40">
                            <img src="uploaded_img/<?php echo htmlspecialchars($farmer_row['image']); ?>" alt="Farmer Image"
                                class="w-full h-full object-cover rounded-full border-4 border-white shadow-md">
                        </div>

                        <!-- Farmer Info -->
                        <div class="text-center md:text-left">
                            <p class="text-lg"><strong>Name:</strong> <?php echo htmlspecialchars($farmer_row['name']); ?>
                            </p>
                            <p class="text-lg"><strong>Email:</strong> <?php echo htmlspecialchars($farmer_row['email']); ?>
                            </p>
                            <p class="text-lg"><strong>Phone:</strong> <?php echo htmlspecialchars($farmer_row['phone']); ?>
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <p class="text-lg"><strong>Village:</strong>
                                    <?php echo htmlspecialchars($farmer_row['Village']); ?></p>
                                <p class="text-lg"><strong>District:</strong>
                                    <?php echo htmlspecialchars($farmer_row['District']); ?></p>
                                <p class="text-lg"><strong>State:</strong>
                                    <?php echo htmlspecialchars($farmer_row['State']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <p class="text-lg text-center mt-4">No Farmer details found.</p>
                <?php } ?>
            </div>
        </div>
<!-- Add to Cart Form -->
<form method="post" class="flex items-center justify-center space-x-4 mt-6">
    <label for="quantity" class="text-lg font-semibold">Quantity:</label>
    <select name="quantity" id="quantity" class="border border-gray-300 rounded-lg p-2 text-gray-700">
        <?php for ($i = 1; $i <= $product_row['Available_Quantity']; $i++) { ?>
            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
        <?php } ?>
    </select>

    <button type="submit" name="add_to_cart"
        class="bg-yellow-400 hover:bg-yellow-500 text-black py-3 px-6 rounded-lg text-lg shadow-md transition-all duration-300">
        🛒 Add to Cart
    </button>
</form>



    </div>
</body>

</html>