
<?php
include("config1.php");
// if the user is not logined in, redirect to login page//
// Start session and check if user is logged in//
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}
// fetch products for specific farmer that what he has added//
$user_id = $_SESSION['user_id']; // Get logged-in user's ID
// Fetch products for the logged-in user
$query_products = "SELECT * FROM products1 WHERE user_id='$user_id'";
$result_products = mysqli_query($conn, $query_products);
// Add Product//
if (isset($_POST['add_product'])) {
  $product_name = $_POST['product_name'];
  $product_price = $_POST['product_price'];
  $available_quantity = $_POST['available_quantity'];
  $category = $_POST['category'];
  $expected_delivery_date = $_POST['expected_delivery'];
  $location = $_POST['location'];
  $product_image = $_FILES['product_image']['name'];
  $product_image_tmp_name = $_FILES['product_image']['tmp_name'];
  $product_image_folder = 'uploaded_img/' . $product_image;
  // insert it the products1 table//
  $insert_product = "INSERT INTO products1 (Product_Name, Price_per_unit, delivery_date, Location, Available_Quantity, Category, image, user_id) 
                       VALUES ('$product_name','$product_price','$expected_delivery_date','$location','$available_quantity','$category','$product_image','$user_id')";

  $_SESSION['message'] = []; // Initialize session messages

  if (mysqli_query($conn, $insert_product)) {
    if (move_uploaded_file($product_image_tmp_name, $product_image_folder)) {
      $_SESSION['message'][] = 'New product added successfully.';
    } else {
      $_SESSION['message'][] = 'Failed to upload product image.';
    }
  } else {
    $_SESSION['message'][] = 'Could not add the product. Error: ' . mysqli_error($conn);
  }
  header("Location: " . $_SERVER['PHP_SELF']);
  exit();
}
// Delete Product
if (isset($_GET['delete'])) {
  $delete_id = $_GET['delete'];
  $delete_product = mysqli_query($conn, "DELETE FROM products1 WHERE id='$delete_id' AND user_id='$user_id'");

  $_SESSION['message'] = []; // Initialize session messages

  if ($delete_product) {
    $_SESSION['message'][] = 'Product deleted successfully.';
  } else {
    $_SESSION['message'][] = 'Could not delete the product.';
  }
  header("Location: " . $_SERVER['PHP_SELF']);
  exit();
}
// Update Profile for farmers//
if (isset($_POST['update_profile'])) {
  $profile_name = $_POST['name'];
  $profile_email = $_POST['email'];
  $profile_phone = $_POST['phone'];
  $profile_village = $_POST['Village'];
  $profile_district = $_POST['District'];
  $profile_state = $_POST['State'];
  $profile_pincode = $_POST['Pincode'];
  $profile_farm_size = $_POST['Farm_Size'];
  $profile_crops = $_POST['Type_of_crops'];
  $profile_bank_account = $_POST['Bank_account'];
  $profile_ifsc = $_POST['Ifsc'];
  $profile_image = $_FILES['image']['name'];
  $profile_image_tmp_name = $_FILES['image']['tmp_name'];
  $profile_image_folder = 'uploaded_img/' . $profile_image;

  $_SESSION['profile_message'] = []; // Initialize session messages

  // Check if a profile exists for the current user
  $query_check = "SELECT * FROM profile_management WHERE user_id='$user_id'";
  $result_check = mysqli_query($conn, $query_check);

  if (mysqli_num_rows($result_check) > 0) {
    $_SESSION['profile_message'][] = 'Only one profile is allowed. Please delete the existing profile before adding a new one.';
  } else {
    $insert_profile = "INSERT INTO profile_management (user_id, name, email, phone, Village, District, State, Pincode, Farm_Size, Type_of_crops, Bank_account, Ifsc, image) 
                           VALUES ('$user_id', '$profile_name','$profile_email','$profile_phone','$profile_village','$profile_district','$profile_state','$profile_pincode','$profile_farm_size','$profile_crops','$profile_bank_account','$profile_ifsc','$profile_image')";

    if (mysqli_query($conn, $insert_profile)) {
      if (move_uploaded_file($profile_image_tmp_name, $profile_image_folder)) {
        $_SESSION['profile_message'][] = 'Profile added successfully.';
      } else {
        $_SESSION['profile_message'][] = 'Failed to upload profile picture.';
      }
    } else {
      $_SESSION['profile_message'][] = 'Database error: ' . mysqli_error($conn);
    }
  }
  header("Location: " . $_SERVER['PHP_SELF']);
  exit();
}
// Delete Profile
if (isset($_POST['delete_profile'])) {
  $query_delete_profile = "DELETE FROM profile_management WHERE user_id='$user_id'";
  if (mysqli_query($conn, $query_delete_profile)) {
    $_SESSION['profile_message'][] = 'Profile deleted successfully. You can now add a new profile.';
  } else {
    $_SESSION['profile_message'][] = 'Error deleting profile: ' . mysqli_error($conn);
  }
  header("Location: " . $_SERVER['PHP_SELF']);
  exit();
}
//fetching the market insights from the admin_database//
include('admin_config1.php');
// Fetch Market Insights from Database
$market_insights = [];
$result = $conn->query("SELECT * FROM market_insights ORDER BY id DESC LIMIT 5");
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $market_insights[] = $row;
  }
}
// Dynamic Icons for Products
$product_icons = [
  "Tomatoes" => "🍅",
  "Rice" => "🌾",
  "Wheat" => "🌿",
  "Corn" => "🌽",
  "Onions" => "🧅",
];
?>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Farmer Dashboard</title>
  <link rel="stylesheet" href="../output.css">
  <!-- for integrating icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="../output.css">
  <style>
    header {
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    html {
      scroll-behavior: smooth;
      scroll-padding-top: 80px;
      overflow-x: hidden;
    }
  </style>
</head>

<body class="bg-green-100">
  <header class="bg-green-900 flex flex-wrap items-center justify-between p-5">
    <div class="text-xl text-white w-full lg:w-auto mb-4 lg:mb-0 flex justify-between items-center">
      <h3>Farmer Dashboard <span class="text-amber-500 p-2"><i class="fa-solid fa-user-tie"></i></span></h3>
      <!-- Menu Button -->
      <button class="text-white lg:hidden" onclick="toggleMenu()">
        <i class="fas fa-bars"></i>
      </button>
    </div>
    <!-- Navigation Menu -->
    <nav class="w-full lg:w-auto flex-grow mb-4 lg:mb-0 hidden lg:flex justify-center" id="mobile-menu">
      <ul class="flex flex-col lg:flex-row gap-4 lg:gap-10 text-white justify-center">
        <li><a href="#profile" class="hover:text-amber-500">Profile</a></li>
        <li><a href="#products" class="hover:text-amber-500">Products</a></li>
        <li><a href="#marketinsights" class="hover:text-amber-500">Market Insights</a></li>
        <li><a href="#orders" class="hover:text-amber-500">Orders</a></li>
        <li><a href="#support" class="hover:text-amber-500">Support</a></li>
        <li class="lg:hidden">
          <a href="logout.php">
            <button
              class="border-2 rounded-2xl p-3 bg-yellow-400 text-green-900 font-semibold hover:bg-yellow-500 hover:text-white transition duration-300 ease-in-out shadow-lg w-full">Logout</button>
          </a>
        </li>
        <li class="lg:hidden">
          <a href="HomePage.php">
            <button
              class="border-2 rounded-2xl p-3 bg-yellow-400 text-green-900 font-semibold hover:bg-yellow-500 hover:text-white transition duration-300 ease-in-out shadow-lg w-full">HomePage</button>
          </a>
        </li>
      </ul>
    </nav>
    <div class="hidden lg:block">
      <a href="logout.php">
        <button
          class="bg-yellow-500 p-2 lg:p-3 mr-3 lg:mr-5 text-black rounded-xl transition delay-150 duration-300 ease-in-out hover:-translate-y-1 hover:scale-110 hover:bg-yellow-400">Logout</button>
      </a>
      <a href="HomePage.php">
        <button
          class="bg-yellow-500 p-2 lg:p-3 mr-3 lg:mr-5 text-black rounded-xl transition delay-150 duration-300 ease-in-out hover:-translate-y-1 hover:scale-110 hover:bg-yellow-400">HomePage</button>
      </a>
    </div>
  </header>
  <main class="max-w-7xl mx-auto px-4 my-10">
    <?php
    include('config1.php');
    // Check if farmer is logged in
    if (isset($_SESSION['user_id'])) {
      $farmer_user_id = $_SESSION['user_id'];
    } else {
      echo "You are not logged in.";
      exit;
    }
    // Total earnings query
    $earning_sql = "
    SELECT SUM(total_price) AS total_earning 
FROM order_items 
WHERE farmer_id = '$farmer_user_id' 
AND delivery_status != 'Cancelled'

";
    $earning_result = mysqli_query($conn, $earning_sql);
    // Check for query error
    if (!$earning_result) {
      die("Earning Query Failed: " . mysqli_error($conn));
    }
    $total_earning = 0;
    if ($earning_row = mysqli_fetch_assoc($earning_result)) {
      $total_earning = $earning_row['total_earning'] ?? 0;
    }
    ?>
    <!-- HTML -->
    <!-- displaying total earning details -->
    <div class="max-w-7xl mx-auto px-4 my-10">
      <div class="bg-green-100 text-green-800 px-6 py-4 rounded-lg shadow-md mb-6 text-center">
        <h2 class="text-2xl font-bold">💰 Total Earnings</h2>
        <p class="text-3xl font-semibold mt-2">₹<?= number_format($total_earning, 2); ?></p>
      </div>
    </div>
    <!-- php code for showing sucessfully updated -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
      <div id="success-message" class=" text-green-600 p-3 rounded mb-4 text-center">
        updated successfully!
      </div>
      <script>
        setTimeout(function () {
          document.getElementById("success-message").style.display = "none";
        }, 3000);
      </script>
    <?php endif; ?>
    <div class="mt-10">
      <h2 class="text-4xl font-extrabold text-center text-gray-800 mb-10">Profile Details</h2>
      <div class="flex justify-center mb-5">
        <button onclick="window.location.href='update_login.php'"
          class="bg-blue-700 p-3 rounded-xl hover:bg-blue-800 text-white">
          Change Password
        </button>
      </div>
      <?php
      // Start session and check if user is logged in
      if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
      }
      // Database connection
      $conn = mysqli_connect("localhost", "root", "", "database");
      if (!$conn) {
        die("<p class='text-red-500 text-center'>Connection failed: " . mysqli_connect_error() . "</p>");
      }
      $user_id = $_SESSION['user_id']; // Get logged-in user's ID
      // Fetch data for the logged-in user
      $query = "SELECT * FROM `profile_management` WHERE user_id='$user_id'";
      $result = mysqli_query($conn, $query);
      if (mysqli_num_rows($result) > 0) {
        echo '<div class="space-y-8">';
        while ($row = mysqli_fetch_assoc($result)) {
          echo '<div class="bg-white shadow-lg border border-gray-200 text-gray-800 rounded-lg overflow-hidden p-6 md:p-8 transform hover:scale-105 hover:shadow-xl transition duration-300 ease-in-out">';

          // Row for Profile Picture (Responsive)
          echo '<div class="flex flex-col md:flex-row md:space-x-8 items-center">';
          echo '<div class="flex-shrink-0 mb-6 md:mb-0">';
          if (!empty($row['image'])) {
            echo '<img src="uploaded_img/' . htmlspecialchars($row['image']) . '" alt="Profile Picture" class="w-40 h-40 sm:w-48 sm:h-48 md:w-56 md:h-56 lg:w-64 lg:h-64 rounded-full object-cover border-2 border-gray-300 shadow-md">';
          } else {
            echo '<div class="w-24 h-24 rounded-full bg-gray-300 flex items-center justify-center text-gray-500 font-semibold text-sm">No Image</div>';
          }
          echo '</div>';
          // Profile Details as Rows
          echo '<div class="text-left w-full space-y-2">';
          echo '<h3 class="text-2xl font-bold">' . htmlspecialchars($row['name']) . '</h3>';
          echo '<div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 gap-x-8">';
          echo '<p><strong>Email:</strong> ' . htmlspecialchars($row['email']) . '</p>';
          echo '<p><strong>Phone:</strong> ' . htmlspecialchars($row['phone']) . '</p>';
          echo '<p><strong>Village:</strong> ' . htmlspecialchars($row['Village']) . '</p>';
          echo '<p><strong>District:</strong> ' . htmlspecialchars($row['District']) . '</p>';
          echo '<p><strong>State:</strong> ' . htmlspecialchars($row['State']) . '</p>';
          echo '<p><strong>Pincode:</strong> ' . htmlspecialchars($row['Pincode']) . '</p>';
          echo '<p><strong>Farm Size:</strong> ' . htmlspecialchars($row['Farm_Size']) . ' acres</p>';
          echo '<p><strong>Type of Crops:</strong> ' . htmlspecialchars($row['Type_of_crops']) . '</p>';
          if (!empty($row['Bank_account'])) {
            echo '<p><strong>Bank Account:</strong> ' . htmlspecialchars($row['Bank_account']) . '</p>';
          }
          if (!empty($row['Ifsc'])) {
            echo '<p><strong>IFSC:</strong> ' . htmlspecialchars($row['Ifsc']) . '</p>';
          }
          echo '</div>'; // End of Grid
          echo '</div>'; // End of Profile Details
          echo '</div>'; // End of Flex Row
          // Delete and Edit Profile Button Section
          // Delete and Edit Profile Button Section
          echo '<div class="mt-6 flex space-x-4 justify-end">';
          echo '<form method="POST" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '" class="inline">';
          echo '<button type="submit" name="delete_profile" class="bg-red-500 text-white px-4 py-2 text-base font-medium rounded-md hover:bg-red-600 transition-all inline-flex items-center">Delete Profile</button>';
          echo '</form>';
          echo '<a href="edit_profile.php" class="bg-blue-500 text-white px-4 py-2 text-base font-medium rounded-md hover:bg-blue-600 transition-all inline-flex items-center">Edit Profile</a>';
          echo '</div>'; // End of Button Section
          echo '</div>'; // End of Profile Card
        }
        echo '</div>';
      } else {
        echo '<div class="text-center text-gray-600 text-lg">No profile data found for your account.</div>';
      }
      // Close the connection
      mysqli_close($conn);
      ?>
    </div>
    <div class="flex items-center justify-center py-12" id="profile">
      <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-7xl">
        <!-- message displaying profile message added or not or existing -->
        <?php
        if (isset($_SESSION['profile_message']) && !empty($_SESSION['profile_message'])) {
          $messages = $_SESSION['profile_message'];
          foreach ($messages as $msg) {
            echo '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>' . htmlspecialchars($msg) . '</p>
                  </div>';
          }
          unset($_SESSION['profile_message']);
        }
        ?>
        <h1 class="text-3xl font-extrabold text-gray-800 mb-8 text-center">Profile Management</h1>
        <p class="text-center text-gray-500 mb-10">This information will be visible to you and buyers for
          transparency.</p>

        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
              <input type="text" id="name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                name="name" required placeholder="Enter your full name" />
            </div>
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
              <input type="email" id="email" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                name="email" required placeholder="Enter your email" />
            </div>
            <div>
              <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
              <input type="text" id="phone" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                name="phone" required placeholder="Enter your phone number" />
            </div>
            <div>
              <label for="village" class="block text-sm font-medium text-gray-700">Village</label>
              <input type="text" id="village" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                name="Village" required placeholder="Enter your village name" />
            </div>
            <div>
              <label for="district" class="block text-sm font-medium text-gray-700">District</label>
              <input type="text" id="district" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                name="District" required placeholder="Enter your district" />
            </div>
            <div>
              <label for="state" class="block text-sm font-medium text-gray-700">State</label>
              <input type="text" id="state" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                name="State" required placeholder="Enter your state" />
            </div>
            <div>
              <label for="pincode" class="block text-sm font-medium text-gray-700">PIN Code</label>
              <input type="text" id="pincode" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                name="Pincode" required placeholder="Enter your PIN code" />
            </div>
            <div>
              <label for="farmsize" class="block text-sm font-medium text-gray-700">Farm Size (in
                acres)</label>
              <input type="text" id="farmsize" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                name="Farm_Size" required placeholder="Enter farm size in acres" />
            </div>
            <div>
              <label for="crops" class="block text-sm font-medium text-gray-700">Type of Crops
                Grown</label>
              <input type="text" id="crops" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                name="Type_of_crops" required placeholder="Enter crops you grow" />
            </div>
            <div>
              <label for="bankaccount" class="block text-sm font-medium text-gray-700">Bank Account Number
                (Optional)</label>
              <input type="text" id="bankaccount"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" name="Bank_account"
                placeholder="Enter your bank account number" />
            </div>
            <div>
              <label for="ifsc" class="block text-sm font-medium text-gray-700">IFSC Code
                (Optional)</label>
              <input type="text" id="ifsc" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                name="Ifsc" placeholder="Enter IFSC code" />
            </div>
            <div>
              <label for="profilepicture" class="block text-sm font-medium text-gray-700">Profile Picture
                (Optional)</label>
              <input type="file" id="profilepicture"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" name="image" />
            </div>
          </div>
          <div class="mt-6">
            <button type="submit"
              class="w-full bg-green-600 text-white py-2 px-4 rounded-md shadow-sm hover:bg-green-700"
              name="update_profile" value="update profile">Add Profile</button>
          </div>
        </form>
      </div>
    </div>
    <!-- product listing management -->
    <div class=" bg-white p-6 rounded-lg shadow-lg" id="products">
      <h1 class="text-3xl font-extrabold text-gray-800 mb-8 text-center">Add Product</h1>

      <form class="grid grid-cols-1 md:grid-cols-2 gap-4" action="<?php $_SERVER['PHP_SELF'] ?>" method="post"
        enctype="multipart/form-data">
        <div>
          <label class="block text-sm font-medium text-gray-700">Product Name</label>
          <input class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" type="text"
            name="product_name" required placeholder="Enter product name" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Upload Image(s)</label>
          <input class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" type="file"
            name="product_image" accept="image/png, image/jpeg, image/jpg" required />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Price per unit (kg/quintal/ton)</label>
          <input class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" type="text"
            name="product_price" required placeholder="Enter price per unit" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Available Quantity</label>
          <input class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" type="text"
            name="available_quantity" required placeholder="Enter available quantity" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Category</label>
          <select class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" name="category" required>
            <option value="Grains">Grains</option>
            <option value="Vegetables">Vegetables</option>
            <option value="Fruits">Fruits</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Expected Delivery Date</label>
          <input id="deliveryDate" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" type="date"
            name="expected_delivery" required onchange="formatDate()" />
          <p id="formattedDate" class="mt-2 text-gray-700 font-semibold"></p>
        </div>
        <!-- in-between writing javascript code for the add product form converting date into that format wednesday-11 April -->
        <script>
          function formatDate() {
            let dateInput = document.getElementById("deliveryDate").value;
            if (dateInput) {
              let dateObj = new Date(dateInput);
              let options = { weekday: 'long', day: 'numeric', month: 'long' };
              let formattedDate = dateObj.toLocaleDateString('en-GB', options);
              document.getElementById("formattedDate").innerText = formattedDate;
            }
          }
        </script>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Location</label>
          <input class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" type="text" name="location"
            required placeholder="Enter location" />
        </div>

        <div class="md:col-span-2">
          <button class="w-full bg-green-500 text-white font-bold py-2 px-4 rounded-md hover:bg-green-600" type="submit"
            name="add_product" value="add product">
            Add Product
          </button>
        </div>
      </form>
    </div>
    <!-- Product Listing -->
    <div class=" mt-8 p-4 sm:p-6">
      <?php
      if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
      }
      include "config1.php";
      // Pagination setup
      $results_per_page = 8; // 2 rows x 4 columns = 8 items per page
      $user_id = $_SESSION['user_id'];

      // Count total results for the user
      $query = "SELECT COUNT(*) FROM products1 WHERE user_id='$user_id'";
      $result = mysqli_query($conn, $query);
      $row = mysqli_fetch_row($result);
      $total_results = $row[0];
      $total_pages = ceil($total_results / $results_per_page);

      // Get current page from URL, default is page 1
      if (isset($_GET['page']) && is_numeric($_GET['page'])) {
        $current_page = (int) $_GET['page'];
      } else {
        $current_page = 1;
      }
      // Ensure the page number is within range
      if ($current_page > $total_pages) {
        $current_page = $total_pages;
      }
      if ($current_page < 1) {
        $current_page = 1;
      }
      // Calculate the starting limit for the query
      $start_limit = ($current_page - 1) * $results_per_page;

      // Fetch only the results for the current page
      $select = mysqli_query($conn, "SELECT * FROM products1 WHERE user_id='$user_id' LIMIT $start_limit, $results_per_page");

      // Display success messages if any
      if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
        foreach ($_SESSION['message'] as $msg) {
          echo '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>' . htmlspecialchars($msg) . '</p>
                </div>';
        }
        unset($_SESSION['message']);
      }
      ?>
      <h1 class="text-3xl font-extrabold text-gray-800 mb-8 text-center">Your Products</h1>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php while ($row = mysqli_fetch_assoc($select)) { ?>
          <div
            class="bg-white p-4 sm:p-6 rounded-lg shadow-lg transform hover:scale-105 hover:shadow-2xl transition duration-300 ease-in-out flex flex-col justify-between h-full">
            <img alt="Product Image" class="rounded-lg object-cover mb-4 w-full h-32"
              src="uploaded_img/<?php echo htmlspecialchars($row['image']); ?>" />
            <div>
              <h3 class="text-lg font-bold">Product Name -
                <?php echo htmlspecialchars($row['Product_Name']); ?>
              </h3>

              <p><span class="font-bold">Price per Quintal -
                </span>₹<?php echo htmlspecialchars($row['Price_per_unit']); ?>/-</p>
              <p><span class="font-bold">Available Quantity -
                </span><?php echo htmlspecialchars($row['Available_Quantity']); ?> Quintal</p>
              <p><span class="font-bold">Category - </span><?php echo htmlspecialchars($row['Category']); ?>
              </p>
              <p><span class="font-bold">Location - </span><?php echo htmlspecialchars($row['Location']); ?>
              </p>
            </div>
            <div class="mt-4 flex justify-between">
              <button onclick="window.location.href='edit_product.php?id=<?php echo htmlspecialchars($row['id']); ?>'"
                class="bg-blue-500 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-600 transition-all">
                Edit
              </button>
              <a href="fardashboard.php?delete=<?php echo htmlspecialchars($row['id']); ?>">
                <button class="bg-red-500 text-white font-bold py-2 px-4 rounded-md hover:bg-red-600 transition-all">
                  Delete
                </button>
              </a>
            </div>
          </div>
        <?php } ?>
      </div>

      <!-- Pagination Navigation -->
      <div class="flex justify-center mt-8 space-x-2">
        <?php if ($current_page > 1) { ?>
          <a href="?page=<?php echo $current_page - 1; ?>"
            class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">Previous</a>
        <?php } ?>
        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
          <a href="?page=<?php echo $i; ?>"
            class="px-4 py-2 rounded-lg <?php echo ($i == $current_page) ? 'bg-blue-500 text-white' : 'bg-gray-300 hover:bg-gray-400'; ?>">
            <?php echo $i; ?>
          </a>
        <?php } ?>
        <?php if ($current_page < $total_pages) { ?>
          <a href="?page=<?php echo $current_page + 1; ?>"
            class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">Next</a>
        <?php } ?>
      </div>
    </div>

    <!-- Market Insights Section -->
    <div class="container mx-auto p-4" id="marketinsights">
      <div class="bg-white p-6 rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold mb-6 text-green-600 flex items-center gap-2 justify-center text-center">
          📊 Market Insights
        </h1>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Demand -->
          <div>
            <h2 class="text-2xl font-semibold mb-4 text-gray-700">🔥 Current Demand Data</h2>
            <ul class="list-none text-gray-600 space-y-3">
              <?php if (!empty($market_insights)): ?>
                <?php foreach ($market_insights as $insight): ?>
                  <li
                    class="p-4 bg-gray-100 rounded-lg hover:bg-green-100 transition-all duration-300 flex items-center gap-3 shadow-sm">
                    <span class="text-2xl">
                      <?= $product_icons[$insight['Prod_Name']] ?? "📦" ?>
                    </span>
                    <div>
                      <span class="text-lg font-semibold text-gray-900">
                        <?= htmlspecialchars($insight['Prod_Name']) ?>
                      </span>
                      <p class="text-gray-500 text-sm">
                        High demand in <span
                          class="text-green-600 font-semibold"><?= htmlspecialchars($insight['Demand_Loc']) ?></span>
                      </p>
                    </div>
                  </li>
                <?php endforeach; ?>
              <?php else: ?>
                <li class="text-gray-500">No market insights available.</li>
              <?php endif; ?>
            </ul>

            <p class="text-gray-600 mt-6 bg-gray-100 p-4 rounded-lg">
              🚜 The agricultural market is experiencing significant shifts this season.
              Certain crops like <span class="font-semibold text-green-700">Tomatoes, Wheat, and Rice</span>
              are in high demand across various states. Buyers are actively seeking bulk orders,
              and prices are fluctuating due to seasonal variations.
            </p>
          </div>

          <!-- Graph -->
          <div class="flex flex-col justify-center items-center">
            <h2 class="text-2xl font-semibold mb-4 text-gray-700">📈 Pricing Trends</h2>
            <div class="w-full bg-gray-200 p-4 rounded-lg shadow-md hover:shadow-xl transition-all duration-300">
              <?php if (!empty($market_insights[0]['image_link'])): ?>
                <img src="<?= htmlspecialchars($market_insights[0]['image_link']) ?>" alt="Graph showing pricing trends"
                  class="rounded-lg shadow-lg w-full h-auto object-cover" />
              <?php else: ?>
                <img src="https://placehold.co/600x400?text=No+Graph+Available" alt="No pricing trends available"
                  class="rounded-lg shadow-lg w-full h-auto object-cover" />
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- News & Tips Section -->
    <div class="container mx-auto px-4">
      <div class="bg-white p-6 rounded-lg shadow-md my-10">
        <h2 class="text-2xl font-bold mb-6 text-blue-700 text-center">📰 News & Tips</h2>
        <ul class="space-y-4">
          <li class="p-4 bg-gray-100 rounded-lg transition hover:bg-blue-100 hover:shadow-md">
            <a href="https://www.nabard.org/" target="_blank" class="flex items-center gap-2">
              <span class="text-xl">🏛️</span>
              <span class="text-lg font-semibold text-blue-800 hover:underline">Government Schemes & Subsidies</span>
            </a>
            <p class="text-gray-600 text-sm mt-1">
              Learn about the latest support programs, loan waivers, and financial aids available for farmers.
              <a href="https://pmkisan.gov.in/" target="_blank" class="text-blue-600 hover:underline">(PM Kisan
                Yojana)</a>
            </p>
          </li>

          <li class="p-4 bg-gray-100 rounded-lg transition hover:bg-blue-100 hover:shadow-md">
            <a href="https://farmer.gov.in/" target="_blank" class="flex items-center gap-2">
              <span class="text-xl">🌱</span>
              <span class="text-lg font-semibold text-blue-800 hover:underline">Best Farming Practices</span>
            </a>
            <p class="text-gray-600 text-sm mt-1">
              Discover modern agricultural techniques to boost your crop yield and reduce water usage.
              <a href="https://krishi.icar.gov.in/" target="_blank" class="text-blue-600 hover:underline">(ICAR - Krishi
                Portal)</a>
            </p>
          </li>

          <li class="p-4 bg-gray-100 rounded-lg transition hover:bg-blue-100 hover:shadow-md">
            <a href="https://enam.gov.in/" target="_blank" class="flex items-center gap-2">
              <span class="text-xl">📈</span>
              <span class="text-lg font-semibold text-blue-800 hover:underline">How to Get Better Prices for Your
                Produce</span>
            </a>
            <p class="text-gray-600 text-sm mt-1">
              Learn negotiation strategies, best market timings, and digital selling platforms for higher profits.
              <a href="https://mkisan.gov.in/" target="_blank" class="text-blue-600 hover:underline">(M-Kisan
                Portal)</a>
            </p>
          </li>
        </ul>
      </div>
    </div>

    <!-- Manage Orders Section -->
    <?php
include "config1.php"; // DB connection

$farmer_user_id = $_SESSION['user_id'] ?? 0;

$query = "
SELECT 
    oi.order_id,
    oi.product_id,
    oi.prod_name,
    oi.quantity,
    oi.total_price,
    oi.delivery_status,
    oi.id AS order_item_id,
    o.status AS payment_status,
    pm.name1 AS buyer_name,
    pm.email1 AS buyer_email,
    pm.phone_no AS buyer_phone,
    pm.address AS buyer_address,
    pm.city,
    pm.state,
    pm.zip_code,
    pm.country
FROM order_items oi
JOIN orders o ON oi.order_id = o.id
JOIN profile_management1 pm ON o.buyer_id = pm.user_id
WHERE oi.farmer_id = '$farmer_user_id' AND oi.delivery_status = 'Pending'
";

$result = mysqli_query($conn, $query);

if (!$result) {
  echo "<p class='text-red-500 font-semibold'>Query Failed: " . mysqli_error($conn) . "</p>";
  exit();
}

$orders_by_buyer = [];

while ($row = mysqli_fetch_assoc($result)) {
  $buyer_key = $row['buyer_email'];

  if (!isset($orders_by_buyer[$buyer_key])) {
    $orders_by_buyer[$buyer_key] = [
      'buyer_name' => $row['buyer_name'],
      'buyer_email' => $row['buyer_email'],
      'buyer_phone' => $row['buyer_phone'],
      'buyer_address' => $row['buyer_address'],
      'city' => $row['city'],
      'state' => $row['state'],
      'zip_code' => $row['zip_code'],
      'country' => $row['country'],
      'payment_status' => $row['payment_status'],
      'items' => [],
    ];
  }

  $orders_by_buyer[$buyer_key]['items'][] = [
    'order_id' => $row['order_id'],
    'prod_name' => $row['prod_name'],
    'quantity' => $row['quantity'],
    'total_price' => $row['total_price'],
    'delivery_status' => $row['delivery_status'],
    'order_item_id' => $row['order_item_id']
  ];
}
?>

<!-- ✅ Manage Orders UI -->
<div class="max-w-7xl mx-auto px-4 my-10 mb-10 mt-10" id="orders">
  <h2 class="text-2xl font-bold mb-6 flex justify-center items-center gap-2">
    📦 Manage Orders
  </h2>

  <div class="rounded-lg shadow border border-gray-200 bg-white overflow-x-auto">
    <table class="w-full text-sm text-left">
      <thead class="bg-green-600 text-white uppercase font-semibold">
        <tr>
          <th class="px-4 py-3">Order IDs</th>
          <th class="px-4 py-3">Products</th>
          <th class="px-4 py-3">Quantity Details</th>
          <th class="px-4 py-3">Price Details (₹)</th>
          <th class="px-4 py-3">Delivery Status</th>
          <th class="px-4 py-3">Payment Status</th>
          <th class="px-4 py-3">Buyer Info</th>
          <th class="px-4 py-3">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <?php foreach ($orders_by_buyer as $buyer) {
          $total_quantity = 0;
          $total_price = 0;
          $all_items = $buyer['items'];
          ?>
          <tr class="hover:bg-gray-50 align-top">
            <td class="px-4 py-3">
              <?php foreach ($all_items as $item) {
                echo "Order #" . $item['order_id'] . "<br>";
              } ?>
            </td>
            <td class="px-4 py-3">
              <?php foreach ($all_items as $item) {
                echo htmlspecialchars($item['prod_name']) . "<br>";
              } ?>
            </td>
            <td class="px-4 py-3">
              <?php foreach ($all_items as $item) {
                echo htmlspecialchars(": " . $item['quantity']) . " Quintal<br>";
                $total_quantity += $item['quantity'];
              } ?>
              <div class="mt-2 font-semibold text-gray-800">
                Total Quantity: <?= $total_quantity ?> Quintal
              </div>
            </td>
            <td class="px-4 py-3">
              <?php foreach ($all_items as $item) {
                echo htmlspecialchars(": ₹" . number_format($item['total_price'], 2)) . "<br>";
                $total_price += $item['total_price'];
              } ?>
              <div class="mt-2 font-semibold text-gray-800">
                Total Price: ₹<?= number_format($total_price, 2); ?>
              </div>
            </td>
            <td class="px-4 py-3">
              <span class="text-yellow-500 font-medium">Pending</span>
            </td>
            <td class="px-4 py-3">
              <?php if (strtolower($buyer['payment_status']) === 'paid') { ?>
                <span class="text-green-600 font-medium">Paid</span>
              <?php } else { ?>
                <span class="text-red-500 font-medium"><?= htmlspecialchars($buyer['payment_status']) ?></span>
              <?php } ?>
            </td>
            <td class="px-4 py-3">
              <div class="text-sm text-gray-800 leading-relaxed">
                <strong><?= htmlspecialchars($buyer['buyer_name']) ?></strong><br>
                <?= htmlspecialchars($buyer['buyer_email']) ?><br>
                <?= htmlspecialchars($buyer['buyer_phone']) ?><br>
                <?= nl2br(htmlspecialchars($buyer['buyer_address'])) ?><br>
                <?= htmlspecialchars($buyer['city']) ?>, <?= htmlspecialchars($buyer['state']) ?><br>
                <?= htmlspecialchars($buyer['zip_code']) ?>, <?= htmlspecialchars($buyer['country']) ?>
              </div>
            </td>
            <td class="px-4 py-3">
              <form method="post" action="mark_shipped_item.php">
                <input type="hidden" name="buyer_email" value="<?= htmlspecialchars($buyer['buyer_email']) ?>">
                <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition">
                  Mark as Shipped
                </button>
              </form>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>

    <!-- Support/Help Section -->
    <div class="container mx-auto px-4" id="support">
      <div class="bg-green-100 p-6 md:p-8 rounded-lg shadow-lg text-center overflow-hidden">
        <h2 class="text-3xl font-bold mb-6 text-green-700 flex items-center justify-center gap-2">
          🤝 Support / Help
        </h2>

        <p class="mb-4 text-gray-700">
          If you have any issues or need assistance, please contact our support team at
          <a class="text-blue-600 hover:underline" href="mailto:support@farmersdashboard.com">
            support@farmersdashboard.com
          </a>.
        </p>

        <h3 class="text-2xl font-semibold mb-4 text-gray-700">
          ❓ Frequently Asked Questions (FAQs)
        </h3>

        <ul class="list-disc list-inside text-gray-700 space-y-2 mx-auto inline-block text-left max-w-md">
          <li>How to list a new product?</li>
          <li>How to update my profile?</li>
          <li>How to track my orders?</li>
        </ul>
      </div>
    </div>
    </div>

  </main>

  <script>
    function toggleMenu() {
      const menu = document.getElementById('mobile-menu');
      menu.classList.toggle('hidden');
    }
  </script>
</body>

</html>