<?php
session_start();
include "config1.php";

// Ensure user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: interfarepage.php");
    exit();
}

$buyer_id = $_SESSION['user_id'];

// Fetch cart details for the logged-in buyer
$cart_query = "SELECT c.*, p.Available_Quantity 
               FROM cart c 
               JOIN products1 p ON c.product_id = p.id 
               WHERE c.buyer_id = '$buyer_id'";

$cart_result = mysqli_query($conn, $cart_query);

if (!$cart_result) {
    die("Database Query Failed: " . mysqli_error($conn));
}

$_SESSION['cart'] = [];

while ($row = mysqli_fetch_assoc($cart_result)) {
    $_SESSION['cart'][] = [
        'id' => $row['product_id'],
        'name' => $row['prod_name1'] ?? "Unknown Product",
        'price' => $row['price'],
        'image' => $row['image'],
        'quantity' => $row['quantity'] ?? 1,
        'available_quantity' => $row['Available_Quantity']
    ];
}

// Fetch delivery details from the profile_management1 table for the logged-in user
$profile_query = "SELECT * FROM profile_management1 WHERE user_id = '$buyer_id'";
$profile_result = mysqli_query($conn, $profile_query);
$profile_data = mysqli_fetch_assoc($profile_result);

// Handle item removal from cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_item'])) {
    $remove_id = $_POST['remove_item'];
    mysqli_query($conn, "DELETE FROM cart WHERE buyer_id = '$buyer_id' AND product_id = '$remove_id'");
    $_SESSION['cart'] = array_filter($_SESSION['cart'], function ($item) use ($remove_id) {
        return $item['id'] != $remove_id;
    });
    $_SESSION['cart_message'] = "Item removed successfully!";
    header("Location: cart.php");
    exit();
}

// Handle quantity update in the cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_quantity'])) {
    $update_id = $_POST['update_id'];
    $new_quantity = (int) $_POST['quantity'];

    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $update_id) {
            if ($new_quantity > $item['available_quantity']) {
                $_SESSION['cart_message'] = "Not enough stock available!";
            } else {
                $item['quantity'] = $new_quantity;
                mysqli_query($conn, "UPDATE cart SET quantity = '$new_quantity' WHERE buyer_id = '$buyer_id' AND product_id = '$update_id'");
                $_SESSION['cart_message'] = "Quantity updated successfully!";
            }
            break;
        }
    }
    header("Location: cart.php");
    exit();
}

$subtotal = 0;
$total_quantity = 0;
$gst = 0;
$delivery_charge = 50;
$grand_total = 0;

if (!empty($_SESSION['cart'])) {
    $subtotal = array_reduce($_SESSION['cart'], function ($sum, $item) {
        return $sum + ($item['price'] * ($item['quantity'] ?? 1));
    }, 0);

    $total_quantity = array_sum(array_column($_SESSION['cart'], 'quantity'));
    $gst_rate = 0.05;
    $gst = $subtotal * $gst_rate;
    $grand_total = $subtotal + $gst + $delivery_charge;
}

?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="../output.css">
</head>

<body class="">
    <header class="fixed top-0 left-0 w-full bg-white shadow-md py-4 text-center z-50">
        <h2 class="text-4xl font-extrabold text-gray-900 flex items-center justify-center">
            🛒 <span class="text-blue-600 ml-2">Shopping Cart</span>
        </h2>
    </header>

    <!-- <div class="top-5 left-5 fixed mt-16">
        <a href="index.php" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md text-sm font-semibold">⬅ Back</a>
    </div> -->

    <div class="max-w-6xl mx-auto py-20 px-6">
        <div class="flex flex-col lg:flex-row gap-6 items-start">
            <div class=" shadow-lg rounded-2xl p-6 border border-gray-200 w-full lg:flex-1">
                <?php if (!empty($_SESSION['cart'])) { ?>
                    <?php foreach ($_SESSION['cart'] as $item) { ?>
                        <div class="flex flex-col sm:flex-row items-center justify-between border-b py-4 gap-4">
                            <div class="flex flex-col sm:flex-row items-center space-x-4">
                                <img src="uploaded_img/<?php echo htmlspecialchars($item['image']); ?>" class="w-24 h-24 object-cover rounded-lg shadow-md border border-gray-300">
                                <div class="text-center sm:text-left">
                                    <p class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($item['name']); ?></p>
                                    <p class="text-lg font-bold text-green-600">₹<?php echo number_format($item['price'], 2); ?> per unit</p>
                                    <p class="text-sm text-gray-600">Available: <?php echo htmlspecialchars($item['available_quantity']); ?> units</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center justify-center gap-2 w-full sm:w-auto">
                                <form method="post" class="flex items-center space-x-2">
                                    <input type="hidden" name="update_id" value="<?php echo $item['id']; ?>">
                                    <select name="quantity" class="border p-2 rounded h-10">
                                        <?php for ($i = 1; $i <= $item['available_quantity']; $i++) { ?>
                                            <option value="<?php echo $i; ?>" <?php echo ($item['quantity'] == $i) ? 'selected' : ''; ?>>
                                                <?php echo $i; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <button type="submit" name="update_quantity" class="bg-blue-500 text-white px-4 py-2 rounded h-10 flex items-center justify-center">🔄 Update</button>
                                </form>
                                <form method="post" onsubmit="return confirm('Are you sure you want to remove this item?');">
                                    <button type="submit" name="remove_item" value="<?php echo $item['id']; ?>" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded h-10 flex items-center justify-center">
                                        ❌ <span class="ml-1">Remove</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <p class="text-lg text-center text-gray-700 py-8">Your cart is empty! 🛒</p>
                <?php } ?>
            </div>

            <div class="bg-gray-100 shadow-lg rounded-2xl p-6 border border-gray-200 w-full lg:w-1/3 self-start">
                <h3 class="text-xl font-bold text-gray-900 mb-3 border-b pb-2">Order Summary</h3>
                <?php if (!empty($_SESSION['cart'])) { ?>
                    <div class="text-md font-semibold text-gray-700 mb-4">
                        <p class="text-lg font-semibold text-gray-800">Items in Cart:</p>
                        <ul class="list-disc pl-6">
                            <?php foreach ($_SESSION['cart'] as $item) {
                                echo "<li class='text-gray-600'>{$item['name']} - {$item['quantity']} Quintal</li>";
                            } ?>
                        </ul>
                    </div>
                    <div class="text-md font-semibold text-gray-700">Subtotal: ₹<?php echo number_format($subtotal, 2); ?></div>
                    <div class="text-md font-semibold text-gray-700">GST (5%): ₹<?php echo number_format($gst, 2); ?></div>
                    <div class="text-md font-semibold text-gray-700">Delivery: ₹<?php echo number_format($delivery_charge, 2); ?></div>
                    <div class="text-lg font-bold text-gray-900 mt-3 border-t pt-2">Total: ₹<?php echo number_format($grand_total, 2); ?></div>
                    
                    <div class="mt-4 border-t pt-4">
                        <p class="text-lg font-semibold text-gray-800">Shipping Address:</p>
                        <p class="text-md text-gray-700"><?php echo htmlspecialchars($profile_data['name1']); ?></p>
                        <p class="text-md text-gray-700"><?php echo htmlspecialchars($profile_data['address']); ?></p>
                        <p class="text-md text-gray-700"><?php echo htmlspecialchars($profile_data['city']); ?>, <?php echo htmlspecialchars($profile_data['state']); ?>, <?php echo htmlspecialchars($profile_data['zip_code'] ?? 'N/A'); ?></p>
                        <p class="text-md text-gray-700">Phone: <?php echo htmlspecialchars($profile_data['phone_no'] ?? 'N/A'); ?></p>
                        <p class="text-md text-gray-700">Country: <?php echo htmlspecialchars($profile_data['country'] ?? 'N/A'); ?></p>
                    </div>

                    <a href="checkout.php" class="w-full bg-blue-600 text-white py-3 text-lg font-medium rounded-lg hover:bg-blue-700 transition duration-300 p-3 shadow-md mt-3 flex items-center justify-center">
                        💳 Proceed to Checkout
                    </a>
                <?php } else { ?>
                    <p class="text-md text-gray-700 text-center">Your cart is empty! 🛒</p>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php mysqli_close($conn); ?>