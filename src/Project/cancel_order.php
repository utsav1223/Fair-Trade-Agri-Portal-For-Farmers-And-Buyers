<?php
require('vendor/autoload.php'); // Razorpay SDK
include("config.php");  // Contains RAZORPAY_KEY_ID and SECRET
include("config1.php"); // DB connection

use Razorpay\Api\Api;

$api = new Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);

echo "<div style='max-width: 800px; margin: 50px auto; font-family: sans-serif;'>";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    // Get payment ID from orders table
    $query = "SELECT payment_id FROM orders WHERE id = $order_id";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);

    if ($data && !empty($data['payment_id'])) {
        $payment_id = $data['payment_id'];

        try {
            // Fetch the payment details
            $payment = $api->payment->fetch($payment_id);

            if ($payment->status === 'captured') {
                // ✅ Proceed with refund
                $refund = $payment->refund();

                // Fetch products for that order
                $products_query = "SELECT prod_name, quantity, total_price 
                                   FROM order_items 
                                   WHERE order_id = $order_id";
                $products_result = mysqli_query($conn, $products_query);

                $cancelled_items = [];
                $total_refund_amount = 0;

                while ($row = mysqli_fetch_assoc($products_result)) {
                    $cancelled_items[] = $row;
                    $total_refund_amount += $row['total_price'];
                }

                // Update order_items table
                $update_items = "UPDATE order_items 
                                 SET delivery_status = 'Cancelled', 
                                     cancelled_by = 'buyer' 
                                 WHERE order_id = $order_id";
                mysqli_query($conn, $update_items);

                // Update orders table
                $update_order = "UPDATE orders 
                                 SET refunded = 1 
                                 WHERE id = $order_id";
                mysqli_query($conn, $update_order);

                // ✅ UI Success Message
                echo "<div style='background: #e6ffed; padding: 20px; border: 1px solid #b2f5ea; border-radius: 8px;'>";
                echo "<h2 style='color: #2f855a;'>✅ Refund Initiated Successfully</h2>";
                echo "<p>Refund has been processed via Razorpay for <strong>Order ID #$order_id</strong>.</p>";

                echo "<h3 style='margin-top: 20px; color: #2c5282;'>Cancelled Products:</h3>";
                echo "<table style='width:100%; border-collapse: collapse; margin-top: 10px;'>
                        <tr style='background: #edf2f7;'>
                            <th style='padding: 10px; border: 1px solid #cbd5e0;'>Product</th>
                            <th style='padding: 10px; border: 1px solid #cbd5e0;'>Quantity</th>
                            <th style='padding: 10px; border: 1px solid #cbd5e0;'>Total Price (₹)</th>
                        </tr>";

                foreach ($cancelled_items as $item) {
                    echo "<tr>
                            <td style='padding: 10px; border: 1px solid #e2e8f0;'>{$item['prod_name']}</td>
                            <td style='padding: 10px; border: 1px solid #e2e8f0;'>{$item['quantity']} Quintal</td>
                            <td style='padding: 10px; border: 1px solid #e2e8f0;'>₹" . number_format($item['total_price'], 2) . "</td>
                          </tr>";
                }

                echo "<tr style='background: #f7fafc; font-weight: bold;'>
                        <td colspan='2' style='padding: 10px; border: 1px solid #e2e8f0;'>Total Refund Amount</td>
                        <td style='padding: 10px; border: 1px solid #e2e8f0;'>₹" . number_format($total_refund_amount, 2) . "</td>
                      </tr>";
                echo "</table>";

                echo "<p style='margin-top: 20px;'>Refund Reference ID: <strong>{$refund->id}</strong></p>";
                echo "</div>";
            } else {
                // ⚠️ Refund not allowed yet
                echo "<div style='background: #fff5f5; padding: 20px; border: 1px solid #feb2b2; border-radius: 8px;'>";
                echo "<h2 style='color: #c53030;'>❌ Refund Cannot Be Processed</h2>";
                echo "<p>The payment is still in <strong>{$payment->status}</strong> status. Refunds are only allowed once the payment is captured.</p>";
                echo "<p>Please wait a few minutes or ensure Auto Capture is set to immediate in Razorpay Dashboard.</p>";
                echo "</div>";
            }

        } catch (Exception $e) {
            echo "<div style='background: #fff5f5; padding: 20px; border: 1px solid #feb2b2; border-radius: 8px;'>";
            echo "<h2 style='color: #c53030;'>❌ Refund Failed</h2>";
            echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "</div>";
        }

    } else {
        echo "<div style='background: #fff5f5; padding: 20px; border: 1px solid #feb2b2; border-radius: 8px;'>";
        echo "<h2 style='color: #c53030;'>❌ Payment ID Not Found</h2>";
        echo "<p>Order ID <strong>#$order_id</strong> does not have a valid payment ID. Please contact support.</p>";
        echo "</div>";
    }
} else {
    echo "<div style='background: #fefcbf; padding: 20px; border: 1px solid #faf089; border-radius: 8px;'>";
    echo "<h2 style='color: #b7791f;'>⚠️ Invalid Request</h2>";
    echo "<p>No order ID provided or wrong method used.</p>";
    echo "</div>";
}

echo "</div>";


echo "<div style='text-align: center; margin-top: 20px;'>
        <a href='buyer_dashboard.php' 
           style='display: inline-block; padding: 10px 20px; background-color: #2b6cb0; color: white; text-decoration: none; border-radius: 5px;'>
           ⬅️ Back to Dashboard
        </a>
      </div>";








?>