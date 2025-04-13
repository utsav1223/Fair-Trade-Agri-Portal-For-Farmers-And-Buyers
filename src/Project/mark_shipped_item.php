<?php
include "config1.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_item_ids']) && is_array($_POST['order_item_ids'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Session expired. Please log in again.'); window.location.href = 'login.php';</script>";
        exit();
    }

    $farmer_id = $_SESSION['user_id'];
    $item_ids = array_map('intval', $_POST['order_item_ids']); // sanitize

    if (count($item_ids) > 0) {
        $ids_string = implode(',', $item_ids); // convert to comma-separated string

        $update_query = "
            UPDATE order_items 
            SET delivery_status = 'Shipped' 
            WHERE id IN ($ids_string) 
              AND farmer_id = $farmer_id 
              AND delivery_status = 'Pending'
              AND (cancelled_by IS NULL OR cancelled_by = '')
        ";

        if (mysqli_query($conn, $update_query)) {
            echo "<script>alert('Selected order items marked as Shipped.'); window.location.href = 'fardashboard.php';</script>";
        } else {
            echo "<p class='text-red-500 font-semibold'>Error: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<script>alert('No items selected.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.history.back();</script>";
}
?>
