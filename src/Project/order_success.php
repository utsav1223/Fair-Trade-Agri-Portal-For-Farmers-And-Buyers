<?php
session_start();
include("config1.php");

if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = intval($_GET['order_id']);

// Fetch product details for this order
$items_sql = "SELECT prod_name, quantity FROM order_items WHERE order_id = $order_id";
$result_items = mysqli_query($conn, $items_sql);

$items = [];
if ($result_items && mysqli_num_rows($result_items) > 0) {
    while ($row = mysqli_fetch_assoc($result_items)) {
        $items[] = [
            'name' => $row['prod_name'],
            'quantity' => $row['quantity']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Successful</title>
    <link rel="stylesheet" href="../output.css">
</head>
<body class="bg-gradient-to-br from-green-50 to-blue-50 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-10 shadow-2xl rounded-2xl w-full max-w-2xl text-center border border-green-300">
        <h1 class="text-4xl font-bold text-green-700">🎉 Order Placed Successfully!</h1>
        <p class="text-xl text-gray-800 mt-3">Your Order ID: <span class="font-semibold text-blue-600">#<?php echo htmlspecialchars($order_id); ?></span></p>
        
        <div class="mt-6 text-left">
            <h2 class="text-2xl font-semibold text-green-600 mb-2">🛒 Ordered Items:</h2>
            <ul class="list-disc pl-6 text-gray-700 space-y-1 text-left">
                <?php foreach ($items as $item): ?>
                    <li><span class="font-medium"><?= htmlspecialchars($item['name']) ?></span> — <?= htmlspecialchars($item['quantity']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <p class="mt-6 text-gray-700">Thank you for shopping with us! You will receive an update once your order is shipped.</p>

        <a href="index.php" class="mt-8 inline-block bg-blue-600 hover:bg-blue-500 text-white px-6 py-3 rounded-lg transition-all duration-200 shadow-md">
            Back to Home
        </a>
    </div>
</body>
</html>
