<?php
include "config1.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buyer_email'])) {
    $buyer_email = mysqli_real_escape_string($conn, $_POST['buyer_email']);

    // Get buyer_id using email
    $buyer_result = mysqli_query($conn, "SELECT user_id FROM profile_management1 WHERE email1 = '$buyer_email'");
    if ($buyer_result && mysqli_num_rows($buyer_result) > 0) {
        $buyer_data = mysqli_fetch_assoc($buyer_result);
        $buyer_id = $buyer_data['user_id'];

        $farmer_id = $_SESSION['user_id'];

        // ✅ Only update items which are: Pending AND NOT Cancelled
        $update_query = "
            UPDATE order_items oi
            JOIN orders o ON oi.order_id = o.id
            SET oi.delivery_status = 'Shipped'
            WHERE o.buyer_id = '$buyer_id'
            AND oi.farmer_id = '$farmer_id'
            AND oi.delivery_status = 'Pending'
            AND (oi.cancelled_by IS NULL OR oi.cancelled_by = '')
        ";

        if (mysqli_query($conn, $update_query)) {
            echo "<script>alert('Selected buyer’s pending orders marked as Shipped.'); window.location.href = 'fardashboard.php';</script>";
        } else {
            echo "<p class='text-red-500 font-semibold'>Error: " . mysqli_error($conn) . "</p>";
        }

    } else {
        echo "<p class='text-red-500 font-semibold'>Buyer not found.</p>";
    }
}
?>
