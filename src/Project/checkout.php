<?php
session_start();
include "config1.php";
include "config.php"; // Secure API keys import

// Ensure user is logged in as a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: interfarepage.php");
    exit();
}

$buyer_id = $_SESSION['user_id'];

// Fetch user's cart
$cart_query = "SELECT c.*, p.Available_Quantity FROM cart c JOIN products1 p ON c.product_id = p.id WHERE c.buyer_id = '$buyer_id'";
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

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php"); 
    exit();
}

// Calculate order summary
$subtotal = array_reduce($_SESSION['cart'], function ($sum, $item) {
    return $sum + ($item['price'] * ($item['quantity'] ?? 1));
}, 0);

$total_quantity = array_sum(array_column($_SESSION['cart'], 'quantity'));
$gst_rate = 0.05;
$gst = $subtotal * $gst_rate;
$delivery_charge = 50;
$grand_total = $subtotal + $gst + $delivery_charge;

$_SESSION['grand_total'] = $grand_total;

// Fetch user profile details
$profile_query = "SELECT * FROM profile_management1 WHERE user_id = '$buyer_id'";
$profile_result = mysqli_query($conn, $profile_query);
$profile_data = mysqli_fetch_assoc($profile_result) ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="../output.css">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>
        .fixed-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            padding: 15px 0;
        }
        .content {
            margin-top: 80px;
        }

        body{
            background-image: url('../Components/checkout.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="fixed-header text-4xl font-bold text-gray-900 text-center shadow-md p-3 rounded-lg">Checkout</div>

    <div class="max-w-3xl mx-auto py-12 px-6 sm:w-full content">
        <div class="bg-white shadow-xl rounded-xl p-6 space-y-6 sm:w-full">
            <h3 class="text-2xl font-semibold text-gray-800">Order Summary</h3>
            <ul class="divide-y divide-gray-200">
                <?php foreach ($_SESSION['cart'] as $item) { ?>
                    <li class="py-3 flex justify-between items-center">
                        <div>
                            <p class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($item['name']); ?></p>
                            <p class="text-gray-600 text-sm">Quantity: <?php echo htmlspecialchars($item['quantity']); ?> Quintal</p>
                        </div>
                        <p class="text-lg font-semibold text-gray-900">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                    </li>
                <?php } ?>
            </ul>

            <div class="border-t border-gray-300 pt-4">
                <p class="text-lg font-semibold text-gray-900">Total: ₹<?php echo number_format($grand_total, 2); ?></p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800">Shipping Address</h3>
                <p class="text-gray-700 mt-2"><?php echo htmlspecialchars($profile_data['name1'] ?? 'N/A'); ?></p>
                <p class="text-gray-700"><?php echo htmlspecialchars($profile_data['address'] ?? 'N/A'); ?></p>
                <p class="text-gray-700"><?php echo htmlspecialchars($profile_data['city'] ?? 'N/A'); ?>, <?php echo htmlspecialchars($profile_data['state'] ?? 'N/A'); ?></p>
                <p class="text-gray-700">Phone: <?php echo htmlspecialchars($profile_data['phone_no'] ?? 'N/A'); ?></p>
            </div>

            <div class="text-center">
                <button id="razorpay-btn" class="w-full bg-blue-600 text-white py-3 text-lg font-medium rounded-lg hover:bg-blue-700 transition duration-300">
                    Pay with Razorpay
                </button>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('razorpay-btn').onclick = function () {
            fetch('razorpay_order.php')
                .then(response => response.json())
                .then(orderData => {
                    if (orderData.error) {
                        alert('Error: ' + orderData.error);
                        return;
                    }
                    var options = {
                        "key": "<?php echo RAZORPAY_KEY_ID; ?>", // Secure API key
                        "amount": orderData.amount,
                        "currency": orderData.currency,
                        "name": "Fair Trade Agri-Portal",
                        "description": "Order Payment",
                        "image": "https://yourwebsite.com/logo.png", // Correct logo URL
                        "order_id": orderData.id,
                        "handler": function (response) {
                            alert("Payment Successful! Payment ID: " + response.razorpay_payment_id);
                            window.location.href = "payment_success.php?payment_id=" + response.razorpay_payment_id;
                        },
                        "prefill": {
                            "name": "<?php echo htmlspecialchars($profile_data['name1'] ?? ''); ?>",
                            "email": "buyer@example.com",
                            "contact": "<?php echo htmlspecialchars($profile_data['phone_no'] ?? ''); ?>"
                        },
                        "theme": {
                            "color": "#3399cc"
                        }
                    };
                    var rzp1 = new Razorpay(options);
                    rzp1.open();
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert('There was an error creating the payment order.');
                });
        };
    </script>
</body>
</html>
