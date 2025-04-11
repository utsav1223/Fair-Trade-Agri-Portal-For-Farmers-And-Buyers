






<?php
session_start();
include "config1.php";

if (!isset($_GET['payment_id'])) {
    header("Location: checkout.php");
    exit();
}

$payment_id = $_GET['payment_id'];
$buyer_id = $_SESSION['user_id'];
$total_amount = $_SESSION['grand_total'];

// insert into orders table
$order_query = "INSERT INTO orders (buyer_id, payment_id, total_amount, status) 
                VALUES ('$buyer_id', '$payment_id', '$total_amount', 'Paid')";

if (mysqli_query($conn, $order_query)) {
    // Order placed successfully
    $order_id = mysqli_insert_id($conn);

    // Step 2: Cart items fetch
    $cart_items_query = "SELECT * FROM cart WHERE buyer_id = '$buyer_id'";
    $result = mysqli_query($conn, $cart_items_query);

    // Step 3: order_items table insert
    include "config1_pdo.php"; // Make sure this file contains valid PDO connection ($pdo)

    while ($item = mysqli_fetch_assoc($result)) {
        $product_id = $item['product_id'];
        $farmer_id = $item['farmer_id'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        $total_price = $item['total_price'];
        $prod_name1 = $item['prod_name1']; // 👈 product name from cart

        // Insert into order_items
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, farmer_id, quantity, price, total_price, prod_name) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$order_id, $product_id, $farmer_id, $quantity, $price, $total_price, $prod_name1]);
    }

    // Step 4: Cart items delete
    mysqli_query($conn, "DELETE FROM cart WHERE buyer_id = '$buyer_id'");

    // Step 5: Redirect to Order Confirmation Page
    header("Location: order_success.php?order_id=$order_id");
    exit();

} else {
    echo "Error: " . mysqli_error($conn);
}
?>
