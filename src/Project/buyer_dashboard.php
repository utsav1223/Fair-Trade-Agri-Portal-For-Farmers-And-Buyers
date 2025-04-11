<?php
session_start();
include("config1.php");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch buyer details for the logged-in user
$stmt = $conn->prepare("SELECT * FROM profile_management1 WHERE user_id = ?");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $buyer = ($result->num_rows > 0) ? $result->fetch_assoc() : null;
    $stmt->close();
} else {
    die("Error preparing buyer fetch statement: " . $conn->error);
}

// Store buyer name in session for welcome message
if (!isset($_SESSION['buyer_name']) && $buyer) {
    $_SESSION['buyer_name'] = $buyer['name1'];
    $_SESSION['show_welcome'] = true;
}

$showWelcome = $_SESSION['show_welcome'] ?? false;
$_SESSION['show_welcome'] = false;

// Handle profile deletion
if (isset($_GET['delete_id']) && $buyer && $_GET['delete_id'] == $buyer['id']) {
    $stmt = $conn->prepare("DELETE FROM profile_management1 WHERE id = ? AND user_id = ?");
    if ($stmt) {
        $stmt->bind_param("ii", $_GET['delete_id'], $user_id);
        $stmt->execute();
        $stmt->close();

        header("Location: buyer_dashboard.php?msg=Profile deleted successfully!&type=success");
        exit();
    } else {
        die("Error preparing delete statement: " . $conn->error);
    }
}

// Handle profile creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save'])) {
    if ($buyer) {
        header("Location: buyer_dashboard.php?msg=You already have a profile!&type=error");
        exit();
    } else {
        $stmt = $conn->prepare("INSERT INTO profile_management1 (user_id, name1, email1, phone_no, address, city, state, zip_code, country) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("issssssss", $user_id, $_POST['name'], $_POST['email'], $_POST['phone_no'], $_POST['address'], $_POST['city'], $_POST['state'], $_POST['zip_code'], $_POST['country']);

            if ($stmt->execute()) {
                header("Location: buyer_dashboard.php?msg=Profile added successfully!&type=success");
                exit();
            } else {
                header("Location: buyer_dashboard.php?msg=Error: " . $conn->error . "&type=error");
                exit();
            }
            // $stmt->close();
        } else {
            die("Error preparing insert statement: " . $conn->error);
        }
    }
}

// ✅ Fetch orders using `buyer_id`
$stmt = $conn->prepare("SELECT * FROM orders WHERE buyer_id = ? ORDER BY created_at DESC");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $orders = $stmt->get_result();
    $stmt->close();
} else {
    die("Error preparing order fetch statement: " . $conn->error);
}

// ✅ Handle case when query fails
if (!$orders) {
    die("Error fetching orders: " . $conn->error);
}

// ✅ Delete old orders if more than 5
$total_orders = $orders->num_rows;
$delete_limit = $total_orders - 5;

if ($delete_limit > 0) {
    $stmt = $conn->prepare("DELETE FROM orders WHERE buyer_id = ? ORDER BY created_at ASC LIMIT ?");
    if ($stmt) {
        $stmt->bind_param("ii", $user_id, $delete_limit);
        $stmt->execute();
        $stmt->close();

        // Fetch orders again after deletion
        $stmt = $conn->prepare("SELECT * FROM orders WHERE buyer_id = ? ORDER BY created_at DESC");
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $orders = $stmt->get_result();
            $stmt->close();
        } else {
            die("Error preparing order re-fetch statement: " . $conn->error);
        }
    } else {
        die("Error preparing order delete statement: " . $conn->error);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Dashboard</title>
    <link rel="stylesheet" href="../output.css">
    <script>
        function hideMessage() {
            setTimeout(() => {
                let messageBox = document.getElementById("successMessage");
                if (messageBox) {
                    messageBox.style.display = "none";
                }
            }, 3000);
        }
    </script>
    <style>
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 80px;
            overflow-x: hidden;
            /* Adjust this value to match the height of your header */
        }
    </style>
</head>
<div class="max-w-7xl mx-auto px-4 p-6 mt-10" onload="hideMessage() ">

    <body>
        <div>
            <header
                class="bg-white shadow-md p-4 flex justify-between items-center border-b-4 border-green-500 fixed top-0 left-0 w-full z-50">
                <h1 class="text-2xl md:text-3xl font-bold text-green-600">Buyer Dashboard</h1>
                <nav>
                    <!-- Mobile Menu Button -->
                    <button class="md:hidden p-2 text-2xl" onclick="toggleMenu()">☰</button>

                    <!-- Navigation Links (Desktop & Mobile) -->
                    <ul id="nav-links"
                        class="hidden flex-col md:flex md:flex-row md:space-x-6 space-y-3 text-lg mt-4 md:mt-0 bg-white md:bg-transparent absolute md:static top-16 left-0 w-full md:w-auto p-4 md:p-0 shadow md:shadow-none z-40">
                        <li><a href="#profile" class="block py-2 px-4 hover:text-green-500">Profile Management</a></li>
                        <li><a href="#orders" class="block py-2 px-4 hover:text-green-500">Order Management</a></li>
                        <li><a href="#rates" class="block py-2 px-4 hover:text-green-500">Current Market Rates</a></li>
                        <li><a href="#help" class="block py-2 px-4 hover:text-green-500">Help</a></li>
                        <li><a href="logout.php"
                                class="block py-2 px-4 bg-yellow-500 text-black font-bold rounded-lg transition-transform transform hover:scale-110 hover:bg-yellow-600">Logout</a>
                        </li>
                        <li><a href="index.php"
                                class="block py-2 px-4 bg-yellow-500 text-black font-bold rounded-lg transition-transform transform hover:scale-110 hover:bg-yellow-600">Home</a>
                        </li>
                    </ul>
                </nav>
            </header>

            <script>
                function toggleMenu() {
                    const menu = document.getElementById('nav-links');
                    menu.classList.toggle('hidden');
                }
            </script>


            <div class="">
                <?php if ($showWelcome) { ?>
                    <div class="bg-green-100 text-green-700 text-center font-bold p-2 rounded-lg mb-4 mt-10"
                        id="welcomeMessage">
                        Welcome, <?= htmlspecialchars($_SESSION['buyer_name']) ?>!
                    </div>
                    <script>
                        setTimeout(() => {
                            let welcomeBox = document.getElementById("welcomeMessage");
                            if (welcomeBox) {
                                welcomeBox.style.display = "none";
                            }
                        }, 5000);
                    </script>
                <?php } ?>

                <div class="bg-white p-6 rounded-lg shadow mb-6 text-center border-b-4 border-green-500 mt-20"
                    id="profile">
                    <h1 class="text-3xl font-bold">Buyer Dashboard</h1>
                    <p class="mt-2 text-gray-600">Manage your profile and connect with sellers.</p>
                </div>

                <!-- Success/Error Message -->
                <?php if (isset($_GET['msg'])) { ?>
                    <div id="successMessage"
                        class="text-center font-bold mb-4 p-2 rounded-lg <?= ($_GET['type'] == 'success') ? 'text-green-500 bg-green-100' : 'text-red-500 bg-red-100' ?>">
                        <?= htmlspecialchars($_GET['msg']) ?>
                    </div>
                <?php } ?>

                <!-- Display Buyer Details -->
                <div class="bg-white shadow rounded-lg p-6 mb-6 border border-gray-300">
                    <h2 class="text-xl font-semibold mb-4">Buyer Details</h2>
                    <?php if ($buyer) { ?>
                        <div class="border p-4 rounded-lg mb-4">
                            <p><strong>Name:</strong> <?= $buyer['name1'] ?></p>
                            <p><strong>Email:</strong> <?= $buyer['email1'] ?></p>
                            <p><strong>Phone:</strong> <?= $buyer['phone_no'] ?></p>
                            <p><strong>Address:</strong> <?= $buyer['address'] ?>, <?= $buyer['city'] ?>,
                                <?= $buyer['state'] ?>,
                                <?= $buyer['zip_code'] ?>, <?= $buyer['country'] ?>
                            </p>
                            <div class="mt-4">
                                <a href="edit_profile1.php?id=<?= $buyer['id'] ?>"
                                    class="bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-600">Edit</a>
                                <a href="?delete_id=<?= $buyer['id'] ?>"
                                    class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600"
                                    onclick="return confirm('Are you sure?')">Delete</a>
                            </div>
                        </div>
                    <?php } else { ?>
                        <p class="text-center text-red-500 font-bold">No Profile Found. Please create one.</p>
                    <?php } ?>
                </div>

                <!-- Profile Form (Always Visible) -->
                <div class="bg-white shadow rounded-lg p-6 mb-6 border border-gray-300">
                    <h2 class="text-xl font-semibold mb-4">Profile Information</h2>
                    <form class="grid grid-cols-1 md:grid-cols-2 gap-4" method="POST">
                        <input type="text" name="name" placeholder="Name"
                            class="p-3 border rounded-lg focus:outline-green-500" required>
                        <input type="email" name="email" placeholder="Email"
                            class="p-3 border rounded-lg focus:outline-green-500" required>
                        <input type="tel" name="phone_no" placeholder="Phone Number"
                            class="p-3 border rounded-lg focus:outline-green-500" required>
                        <input type="text" name="address" placeholder="Address"
                            class="p-3 border rounded-lg focus:outline-green-500" required>
                        <input type="text" name="city" placeholder="City"
                            class="p-3 border rounded-lg focus:outline-green-500" required>
                        <input type="text" name="state" placeholder="State"
                            class="p-3 border rounded-lg focus:outline-green-500" required>
                        <input type="text" name="zip_code" placeholder="Zip Code"
                            class="p-3 border rounded-lg focus:outline-green-500" required>
                        <input type="text" name="country" placeholder="Country"
                            class="p-3 border rounded-lg focus:outline-green-500" required>
                        <button type="submit" name="save"
                            class="bg-green-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-green-600 col-span-1 md:col-span-2">Save
                            Changes</button>
                    </form>
                </div>
            </div>
            <div class="">
                <h1 class="text-3xl font-bold text-center text-green-600 mb-5">Your Orders</h1>

                <div class="overflow-x-auto bg-white p-6 rounded-lg shadow-md">
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-green-500 text-white">
                                <th class="border border-gray-300 p-2">Payment ID</th>
                                <th class="border border-gray-300 p-2">Total Amount</th>
                                <th class="border border-gray-300 p-2">Status</th>
                                <th class="border border-gray-300 p-2">Ordered At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $orders->fetch_assoc()) { ?>
                                <tr class="text-center">
                                    <td class="border border-gray-300 p-2"><?= htmlspecialchars($row['payment_id']) ?></td>
                                    <td class="border border-gray-300 p-2">₹<?= htmlspecialchars($row['total_amount']) ?>
                                    </td>
                                    <td class="border border-gray-300 p-2"><?= htmlspecialchars($row['status']) ?></td>
                                    <td class="border border-gray-300 p-2"><?= htmlspecialchars($row['created_at']) ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
            include("config1.php");

            $user_id = $_SESSION['user_id'];

            // Exclude cancelled orders from results
            $orders_sql = "SELECT oi.order_id, oi.prod_name, oi.quantity, oi.delivery_status, pm.address, pm.city, pm.state, pm.zip_code, pm.country 
FROM order_items oi 
JOIN orders o ON oi.order_id = o.id 
JOIN profile_management1 pm ON o.buyer_id = pm.user_id 
WHERE o.buyer_id = $user_id AND oi.delivery_status NOT IN ('Cancelled', 'Delivered')
ORDER BY oi.order_id DESC";


            $result_orders = mysqli_query($conn, $orders_sql);

            $orders = [];

            if ($result_orders && mysqli_num_rows($result_orders) > 0) {
                while ($row = mysqli_fetch_assoc($result_orders)) {
                    $order_id = $row['order_id'];
                    if (!isset($orders[$order_id])) {
                        $orders[$order_id] = [
                            'products' => [],
                            'delivery_status' => $row['delivery_status'],
                            'address' => "{$row['address']}, {$row['city']}, {$row['state']} - {$row['zip_code']}, {$row['country']}"
                        ];
                    }
                    $orders[$order_id]['products'][] = "{$row['prod_name']} - {$row['quantity']}";
                }
            }
            ?>

            <h2 class="text-3xl font-bold text-center text-green-700 mb-10 mt-10" id="orders">Order Status</h2>

            <?php if (count($orders) > 0): ?>
                <div class="space-y-6">
                    <?php foreach ($orders as $order_id => $order): ?>

                        <?php
                        $status = $order['delivery_status'];
                        $status_class = 'text-yellow-500'; // default
                
                        if ($status === 'Shipped') {
                            $status_class = 'text-blue-600';
                        } elseif ($status === 'Delivered') {
                            $status_class = 'text-green-600';
                        } elseif ($status === 'Cancelled') {
                            $status_class = 'text-red-500 bg-red-100 px-2 py-1 rounded';
                        }
                        ?>

                        <div class="bg-white shadow-lg rounded-2xl p-6 border border-green-300">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-lg font-semibold text-green-800">Order #<?= $order_id ?></h3>
                                <span class="text-sm font-medium <?= $status_class ?>">
                                    Status: <?= $status ?>
                                </span>
                            </div>

                            <p class="text-sm text-gray-700 mb-2"><strong>Delivery
                                    Address:</strong><br><?= nl2br($order['address']) ?></p>

                            <div class="mt-4">
                                <p class="font-medium text-green-700 mb-1">Products:</p>
                                <ul class="list-disc pl-6 text-gray-800">
                                    <?php foreach ($order['products'] as $prod): ?>
                                        <li><?= $prod ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <?php if ($status === 'Shipped'): ?>
                                <form method="post" class="mt-4 text-right">
                                    <input type="hidden" name="delivered_order_id" value="<?= $order_id ?>">
                                    <button type="submit"
                                        class="bg-green-600 hover:bg-green-500 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                        Mark as Delivered
                                    </button>
                                </form>




                                <?php

                                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delivered_order_id'])) {
                                    $order_id = $_POST['delivered_order_id'];

                                    // Update delivery_status to 'Delivered'
                                    $update_query = "UPDATE order_items SET delivery_status = 'Delivered' WHERE order_id = '$order_id'";
                                    if (mysqli_query($conn, $update_query)) {
                                        $_SESSION['status_message'] = "Order #$order_id marked as Delivered.";
                                    } else {
                                        $_SESSION['status_message'] = "Failed to update order.";
                                    }

                                    // header("Location: " . $_SERVER['PHP_SELF']);
                    
                                }
                                ?>
                            <?php endif; ?>
                            <!-- when the status == shipped it hides the cancel order button -->
                            <?php if ($status !== 'Delivered' && $status !== 'Shipped' && $status !== 'Cancelled'): ?>
                                <form method="post" action="cancel_order.php" class="mt-4 text-right"
                                    onsubmit="return confirm('Are you sure you want to cancel and refund this order?');">
                                    <input type="hidden" name="order_id" value="<?= $order_id ?>">
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                        Cancel & Refund
                                    </button>
                                </form>
                            <?php endif; ?>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center text-gray-500 font-medium">You have no orders yet.</p>
            <?php endif; ?>


            <?php
            // Fetch all market rates
            $result = mysqli_query($conn, "SELECT * FROM market_rates ORDER BY category, item_name");
            ?>

            <!-- 🌾 Admin Input Form -->
            <div class="mt-24 p-4 bg-white shadow rounded-lg overflow-x-auto" id="rates">
                <h2 class="text-2xl font-bold mb-4 text-green-700 text-center">🌾 Market Rates</h2>

                <!-- 📊 Display Table -->
                <table
                    class="min-w-full text-sm border border-gray-300 bg-white shadow rounded divide-y divide-gray-200">
                    <thead class="bg-green-600 text-white text-left">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-semibold">Category</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Item</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Unit</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Price (₹)</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Last Updated</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white text-gray-800">
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4"><?= htmlspecialchars($row['category']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($row['item_name']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($row['unit']) ?></td>
                                <td class="px-6 py-4 font-medium">₹ <?= number_format($row['current_price'], 2) ?></td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?= date('d M Y, h:i A', strtotime($row['last_updated'])) ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div>
                <!-- ✅ Green Footer -->
                <section class="bg-green-50 text-gray-800 py-12 px-4 md:px-16 mt-24 rounded-lg shadow-md" id="help">
                    <h2 class="text-3xl font-bold text-green-700 mb-6 text-center">🛟 Support & Help</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                        <!-- 💬 Contact Support -->
                        <div class="bg-white p-6 rounded-lg shadow-sm border border-green-100">
                            <h3 class="text-xl font-semibold text-green-600 mb-2">Contact Support</h3>
                            <p class="text-sm mb-4">If you have any issues with your orders, profile, or payments, feel
                                free to reach out.</p>
                            <ul class="text-sm space-y-1">
                                <li><strong>Email:</strong> support@agriportal.com</li>
                                <li><strong>Phone:</strong> +91 98765 43210</li>
                                <li><strong>Timing:</strong> Mon - Sat, 9 AM to 6 PM</li>
                            </ul>
                        </div>

                        <!-- 📋 FAQs -->
                        <div class="bg-white p-6 rounded-lg shadow-sm border border-green-100">
                            <h3 class="text-xl font-semibold text-green-600 mb-2">Frequently Asked Questions</h3>
                            <ul class="list-disc list-inside text-sm space-y-2">
                                <li><strong>How can I track my order?</strong><br> Go to your <a href="#orders"
                                        class="text-green-600 underline">Order
                                        Management</a> section.</li>
                                <li><strong>How to update my profile?</strong><br> Use the <a href="#profile"
                                        class="text-green-600 underline">Profile
                                        Management</a> page to edit your details.</li>
                                <li><strong>Need help with payments?</strong><br> Reach out to our support team directly
                                    via email.</li>
                            </ul>
                        </div>
                    </div>
                </section>
    </body>
</html>