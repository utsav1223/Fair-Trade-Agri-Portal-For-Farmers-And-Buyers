




<?php
// Include your database connection file
include 'admin_config1.php';

// Setup pagination
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1; // Current page number
$items_per_page = 1; // Display one card per page
$offset_value = ($current_page - 1) * $items_per_page; // Calculate offset for query

// Count total items for pagination
$count_query = "SELECT COUNT(*) AS total_items FROM deal";
$count_result = $conn->query($count_query);
$total_row = $count_result->fetch_assoc();
$total_pages_count = ceil($total_row['total_items'] / $items_per_page);

// Fetch the item(s) for the current page
$item_query = "SELECT * FROM deal LIMIT $offset_value, $items_per_page";
$item_result = $conn->query($item_query);
// Check if the query execution is successful
if (!$item_result) {
  die("Query failed: " . $conn->error);
}

// $fetch_deal_sql = "SELECT * FROM deal ORDER BY id DESC LIMIT $dealLimit OFFSET $dealOffset"; // Renamed variables
// $deal_result = $conn->query($fetch_deal_sql);



$blogPostLimit = 3; // Number of blogs per page
$currentBlogPage = isset($_GET['blogCurrentPage']) ? (int) $_GET['blogCurrentPage'] : 1; // Current page number
$blogOffset = ($currentBlogPage - 1) * $blogPostLimit; // Calculate offset for the SQL query

// Fetch paginated blogs
$blogQuery = "SELECT * FROM blog LIMIT $blogOffset, $blogPostLimit";
$blogResult = $conn->query($blogQuery);

// Total blogs for pagination
$totalBlogQuery = "SELECT COUNT(id) AS total_blogs FROM blog";
$totalBlogResult = $conn->query($totalBlogQuery);
$totalBlogRow = $totalBlogResult->fetch_assoc();
$totalBlogPages = ceil($totalBlogRow['total_blogs'] / $blogPostLimit);



?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Title of our website -->
  <title>Fair Trade Agri Portal</title>
  <!-- Connecting Tailwind CSS -->
  <link rel="stylesheet" href="../output.css">
  <!-- for integrating icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

  <style>
    @keyframes fade-in {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .animate-fade-in {
      animation: fade-in 1.2s ease-in-out;
    }

    html {
      scroll-behavior: smooth;
      scroll-padding-top: 80px;
      /* Adjust this value to match the height of your header */
    }

    * {
      box-sizing: border-box;
    }

    html,
    body {
      overflow-x: hidden !important;
      max-width: 100vw;
    }
  </style>
</head>

<body class="bg-gray-200" onload="AOS.init();">
  <div class="font-roboto">

    <header id="navbar"
      class="fixed top-0 left-0 w-full z-50 bg-transparent backdrop-blur-md bg-opacity-20 transition-all duration-500"
      data-aos="fade-down">
      <div class="flex items-center justify-between p-4">
        <!-- Logo & Title -->
        <div class="flex items-center">
          <img alt="Fair Trade Logo" class="h-12 w-12 md:h-18 md:w-18 ml-3" src="../Components/fair21.png"
            data-aos="zoom-in" data-aos-delay="200" />
          <h3 class="pl-2 md:pl-5 text-white text-xl md:text-2xl font-semibold" data-aos="fade-right">
            Fair Trade <span class="text-orange-400">Agri Portal</span>
          </h3>
        </div>

        <!-- Desktop Navigation -->
        <nav class="hidden lg:flex gap-6 text-white">
          <ul class="flex gap-6">
            <li><a class="hover:text-orange-300 transition duration-300" href="#Home">Home</a></li>
            <li><a class="hover:text-orange-300 transition duration-300" href="#about-us">About Us</a></li>
            <li><a class="hover:text-orange-300 transition duration-300" href="#marketplace">Explore Market Place</a>
            </li>
            <li><a class="hover:text-orange-300 transition duration-300" href="#products">Products</a></li>
            <li><a class="hover:text-orange-300 transition duration-300" href="#blog">Blog</a></li>
            <li><a class="hover:text-orange-300 transition duration-300" href="#learn">learn</a></li>
            <li><a class="hover:text-orange-300 transition duration-300" href="#contact">Contact</a></li>
          </ul>
        </nav>

        <!-- Desktop Buttons -->
        <div class="hidden lg:flex gap-4 items-center">
          <!-- Cart Icon -->
          <?php
          session_start();
          $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

          if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'buyer') { ?>
            <a href="cart.php" class="relative text-white text-2xl">
              🛒
              <?php if ($cart_count > 0) { ?>
                <span
                  class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                  <?php echo $cart_count; ?>
                </span>
              <?php } ?>
            </a>
          <?php } ?>

          <!-- Dashboard Button -->
          <?php if (!empty($_SESSION['user_type'])):
            switch ($_SESSION['user_type']) {
              case 'farmer':
                $dashboard_url = "fardashboard.php";
                break;
              case 'buyer':
                $dashboard_url = "buyer_dashboard.php";
                break;
              case 'admin':
                $dashboard_url = "admin.php";
                break;
              default:
                $dashboard_url = "index.php"; // Default page if user_type is unknown
            }
            ?>
            <a href="<?= htmlspecialchars($dashboard_url, ENT_QUOTES, 'UTF-8'); ?>">
              <button
                class="bg-yellow-500 p-3 text-black rounded-xl transition duration-300 hover:scale-105 hover:bg-yellow-400 shadow-lg">
                Dashboard
              </button>
            </a>
          <?php endif; ?>


          <a href="interfarepage.php">
            <button
              class="bg-yellow-500 p-3 text-black rounded-xl transition duration-300 hover:scale-105 hover:bg-yellow-400 shadow-lg">
              Login
            </button>
          </a>
        </div>

        <!-- Mobile Menu Button -->
        <button class="lg:hidden text-white" id="menu-toggle">
          <i class="fas fa-bars text-2xl"></i>
        </button>
      </div>

      <!-- 🔥 Cart Message Notification -->
      <?php if (isset($_SESSION['cart_message'])) { ?>
        <div id="cart-message"
          class="fixed top-25 left-1/2 transform -translate-x-1/2 bg-white text-green-700 text-lg font-semibold shadow-lg px-6 py-3 rounded-full flex items-center gap-2 transition-all duration-500">
          ✅ <?php echo $_SESSION['cart_message']; ?>
        </div>
        <script>
          setTimeout(() => {
            let cartMessage = document.getElementById("cart-message");
            if (cartMessage) {
              cartMessage.style.opacity = "0";
              cartMessage.style.transform = "translateY(-20px)";
              setTimeout(() => { cartMessage.remove(); }, 500);
            }
          }, 3000);
        </script>
        <?php unset($_SESSION['cart_message']); ?>
      <?php } ?>


      <!-- Mobile Menu (Initially Hidden) -->
      <nav class="lg:hidden hidden flex-col gap-6 text-white p-3" id="mobile-menu">
        <ul class="flex flex-col gap-6">
          <li><a class="hover:text-orange-300 transition duration-300" href="#Home">Home</a></li>
          <li><a class="hover:text-orange-300 transition duration-300" href="#about-us">About Us</a></li>
          <li><a class="hover:text-orange-300 transition duration-300" href="#marketplace">Explore Market Place</a></li>
          <li><a class="hover:text-orange-300 transition duration-300" href="#products">Products</a></li>
          <li><a class="hover:text-orange-300 transition duration-300" href="#blog">Blog</a></li>
          <li><a class="hover:text-orange-300 transition duration-300" href="#learn">Learn</a></li>
          <li><a class="hover:text-orange-300 transition duration-300" href="#contact">Contact</a></li>

          <!-- Dashboard & Cart for Mobile -->
          <li class="flex flex-col gap-3 items-center">
            <!-- Cart Icon -->
            <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'buyer') { ?>
              <a href="cart.php" class="relative text-white text-2xl">
                🛒
                <?php if ($cart_count > 0) { ?>
                  <span
                    class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                    <?php echo $cart_count; ?>
                  </span>
                <?php } ?>
              </a>
            <?php } ?>

            <?php if (!empty($_SESSION['user_type'])): ?>
              <a href="<?= htmlspecialchars($dashboard_url, ENT_QUOTES, 'UTF-8'); ?>">
                <button
                  class="bg-yellow-500 p-3 text-black rounded-xl transition duration-300 hover:scale-105 hover:bg-yellow-400 shadow-lg w-full">
                  Dashboard
                </button>
              </a>
            <?php endif; ?>

            <a href="interfarepage.php">
              <button
                class="bg-yellow-500 p-3 text-black rounded-xl transition duration-300 hover:scale-105 hover:bg-yellow-400 shadow-lg w-full">
                Login
              </button>
            </a>
          </li>
        </ul>
      </nav>
    </header>

    <!-- 🔥 Fix: Mobile Menu Toggle Script -->
    <script>
      document.getElementById('menu-toggle').addEventListener('click', function () {
        let menu = document.getElementById('mobile-menu');
        if (menu.classList.contains('hidden')) {
          menu.classList.remove('hidden');
          menu.classList.add('block'); // Show menu
        } else {
          menu.classList.remove('block');
          menu.classList.add('hidden'); // Hide menu
        }
      });
    </script>
  </div>


  <main>
    <section>
      <!-- Home Section -->
      <section class="relative overflow-hidden w-full h-screen" id="Home">
        <div class="slider relative w-full h-screen overflow-hidden">
          <!-- Slider Container -->
          <div class="slides flex transition-transform duration-1000 ease-in-out">
            <div class="slide w-full h-screen flex-shrink-0" data-aos="fade-up">
              <img src="../Components/farmer3.jpg" alt="Farmer Image 1"
                class="w-full h-screen object-cover brightness-75">
            </div>
            <div class="slide w-full h-screen flex-shrink-0" data-aos="fade-up" data-aos-delay="300">
              <img src="../Components/farmerai27.jpg" alt="Farmer Image 2"
                class="w-full h-screen object-cover brightness-75">
            </div>
            <div class="slide w-full h-screen flex-shrink-0" data-aos="fade-up" data-aos-delay="500">
              <img src="../Components/farmer4.jpg" alt="Farmer Image 3"
                class="w-full h-screen object-cover brightness-75">
            </div>
          </div>

          <!-- Gradient Overlay -->
          <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/30"></div>

          <!-- Text Overlay -->
          <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
            <h1 class="text-5xl md:text-7xl font-extrabold text-white drop-shadow-lg tracking-wide animate-fade-in"
              data-aos="zoom-in">
              🌿 Empowering Farmers With <span class="text-green-400">Better Market Rates</span> 💰
            </h1>
            <p class="mt-4 text-xl md:text-2xl text-gray-200 drop-shadow-md animate-fade-in" data-aos="fade-up"
              data-aos-delay="400">
              Connecting <span class="font-bold text-yellow-400">Farmers</span> and <span
                class="font-bold text-green-300">Buyers</span> For Fair Trade 🚜✨
            </p>
            <a href="interfarepage.php">
              <button class="bg-gradient-to-r from-green-600 to-green-500 p-4 mt-5 text-white rounded-full w-56 text-lg font-semibold shadow-md 
               hover:from-green-500 hover:to-green-400 hover:scale-105 transition-all duration-300 ease-in-out"
                data-aos="flip-up" data-aos-delay="600">
                🚀 Get Started Now!
              </button>
            </a>

          </div>

          <!-- Navigation Buttons -->
          <button
            class="prev absolute left-5 top-1/2 transform -translate-y-1/2 bg-gray-800 bg-opacity-50 p-3 rounded-full text-white hover:bg-gray-600">
            &#10094;
          </button>
          <button
            class="next absolute right-5 top-1/2 transform -translate-y-1/2 bg-gray-800 bg-opacity-50 p-3 rounded-full text-white hover:bg-gray-600">
            &#10095;
          </button>

          <!-- Dots Navigation -->
          <div class="dots absolute bottom-5 left-1/2 transform -translate-x-1/2 flex space-x-3">
            <button class="dot w-4 h-4 rounded-full bg-white bg-opacity-50 hover:bg-opacity-100"
              data-slide="0"></button>
            <button class="dot w-4 h-4 rounded-full bg-white bg-opacity-50 hover:bg-opacity-100"
              data-slide="1"></button>
            <button class="dot w-4 h-4 rounded-full bg-white bg-opacity-50 hover:bg-opacity-100"
              data-slide="2"></button>
          </div>
        </div>
      </section>

      <!-- About Section -->
      <section id="about-us" data-aos="fade-up">
        <div class="bg-green-900 py-8 text-white">
          <div class="max-w-5xl mx-auto px-6 text-center">
            <h3 class="text-2xl font-bold mb-4">About Fair Trade Agri Portal</h3>
            <p class="text-base leading-relaxed px-4 sm:px-16">
              Fair Trade Agri-Portal is an online marketplace connecting farmers directly with buyers,
              ensuring fair trade practices and better prices for agricultural produce.
            </p>
          </div>

          <div class="max-w-4xl mx-auto mt-6 flex flex-wrap justify-center gap-12 px-4 sm:px-0">
            <div class="flex flex-col items-center text-center" data-aos="zoom-in" data-aos-delay="200">
              <div class="bg-white p-4 rounded-full shadow-lg transform transition hover:scale-105">
                <img src="../Components/better_p.png" alt="Better Price" class="h-16 w-16">
              </div>
              <p class="mt-3 text-sm sm:text-base font-semibold">Better Prices</p>
            </div>

            <div class="flex flex-col items-center text-center" data-aos="zoom-in" data-aos-delay="400">
              <div class="bg-white p-4 rounded-full shadow-lg transform transition hover:scale-105">
                <img src="../Components/direct_s.png" alt="Direct Selling" class="h-16 w-16">
              </div>
              <p class="mt-3 text-sm sm:text-base font-semibold">Direct Selling</p>
            </div>

            <div class="flex flex-col items-center text-center" data-aos="zoom-in" data-aos-delay="600">
              <div class="bg-white p-4 rounded-full shadow-lg transform transition hover:scale-105">
                <img src="../Components/secure_t.png" alt="Secure Transactions" class="h-16 w-16">
              </div>
              <p class="mt-3 text-sm sm:text-base font-semibold">Secure Transactions</p>
            </div>

            <div class="flex flex-col items-center text-center" data-aos="zoom-in" data-aos-delay="800">
              <div class="bg-white p-4 rounded-full shadow-lg transform transition hover:scale-105">
                <img src="../Components/market_i.png" alt="Market Insights" class="h-16 w-16">
              </div>
              <p class="mt-3 text-sm sm:text-base font-semibold">Market Insights</p>
            </div>
          </div>
        </div>
      </section>


      <!-- Deal of the Day Display -->
      <!-- Deal of the Day Section -->
      <!-- Existing Deals of the Day Section -->
      <section
        class="max-w-7xl mx-auto p-4 md:p-8 bg-white shadow-md mt-6 md:mt-10 mb-6 md:mb-10 border border-gray-300 rounded-lg relative overflow-hidden"
        data-aos="fade-up" id="marketplace">

        <!-- Background Image for Crops -->
        <div class="absolute inset-0 w-full h-full bg-cover bg-center opacity-20 rounded-lg"
          style="background-image: url('https://source.unsplash.com/1600x900/?farm,crops');"></div>

        <!-- Heading Section -->
        <div
          class="relative flex flex-col md:flex-row justify-between items-center mb-4 md:mb-6 border-b border-gray-300 pb-2 md:pb-4">
          <h1 class="text-2xl md:text-4xl font-bold text-gray-900 text-center md:text-left" data-aos="fade-right">
            🌾 Deals of the Day
          </h1>
          <div
            class="text-gray-700 text-sm md:text-lg font-medium bg-white px-4 py-2 md:px-6 md:py-3 shadow-md border border-gray-200 rounded-lg"
            data-aos="fade-left">
            ⏳ Time Left: <span id="running-timer" class="ml-2 text-green-600 font-bold">Loading...</span>
          </div>
        </div>

        <!-- Deals Section -->
        <?php if ($item_result->num_rows > 0): ?>
          <?php while ($item_row = $item_result->fetch_assoc()): ?>
            <div
              class="relative flex flex-col md:flex-row items-center bg-white p-4 md:p-6 mb-4 md:mb-6 shadow-lg border border-gray-300 rounded-lg transition-all hover:shadow-2xl">

              <!-- Product Image -->
              <div class="w-full md:w-1/2 flex justify-center">
                <img src="<?php echo htmlspecialchars($item_row['image']); ?>" alt="Product Image"
                  class="w-full max-h-64 md:h-96 object-cover rounded-lg shadow-md border border-gray-200"
                  data-aos="fade-left">
              </div>

              <!-- Product Details -->
              <div class="w-full md:w-1/2 p-4 text-center md:text-left" data-aos="fade-right">
                <h3 class="text-xl md:text-3xl font-bold text-gray-900">
                  <?php echo htmlspecialchars($item_row['Product_Name']); ?>
                </h3>
                <p class="text-gray-700 mt-2 md:mt-4 leading-relaxed text-sm md:text-base">
                  <?php echo htmlspecialchars($item_row['Product_desc']); ?>
                </p>
                <p class="mt-3 md:mt-6 text-green-700 font-bold text-lg md:text-xl">
                  Price: ₹<?php echo htmlspecialchars($item_row['Price_per_unit']); ?>
                </p>



                <!-- Responsive Buy Now Button -->
                <a href="<?php echo $item_row['pd_link']; ?>">
                  <button
                    class="bg-yellow-400 text-black w-full md:w-auto px-4 py-2 md:px-6 md:py-3 font-semibold shadow-lg rounded-lg transition-all transform hover:bg-yellow-500 hover:shadow-xl mt-4 md:mt-6 text-base md:text-lg"
                    data-aos="flip-left">
                    🛒 BUY NOW
                  </button>
                </a>

              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p class="text-gray-700 text-center text-lg font-semibold p-6 bg-white border border-gray-300 shadow-md rounded-lg min-h-[150px]"
            data-aos="fade-up">🚫 No deals available at the moment.</p>
        <?php endif; ?>

        <!-- Pagination Controls -->
        <div class="mt-6 flex flex-col md:flex-row justify-between items-center gap-3 relative z-10">
          <a href="?page=<?php echo max(1, $current_page - 1); ?>" class="w-full md:w-auto text-center px-4 py-2 md:px-6 md:py-3 bg-gray-300 text-gray-900 font-semibold hover:bg-gray-400 transition-all border border-gray-500 rounded-lg
      <?php echo ($current_page <= 1) ? 'pointer-events-none opacity-50' : ''; ?>">
            ⬅ Previous
          </a>
          <span class="text-md md:text-lg font-semibold text-gray-900">Page <?php echo $current_page; ?> of
            <?php echo $total_pages_count; ?></span>
          <a href="?page=<?php echo min($total_pages_count, $current_page + 1); ?>" class="w-full md:w-auto text-center px-4 py-2 md:px-6 md:py-3 bg-gray-300 text-gray-900 font-semibold hover:bg-gray-400 transition-all border border-gray-500 rounded-lg
      <?php echo ($current_page >= $total_pages_count) ? 'pointer-events-none opacity-50' : ''; ?>">
            Next ➡
          </a>
        </div>
      </section>
      <div class="max-w-7xl mx-auto mt-15 mb-8 px-4 " data-aos="fade-up" id="products">
        <?php
        include "config1.php"; // Include your database connection file
        
        // Pagination setup
        $items_per_page = 12; // 3 rows x 4 columns = 12 items per page
        $search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : "";
        $min_price = isset($_GET['min_price']) && $_GET['min_price'] !== "" ? (float) $_GET['min_price'] : 0;
        $max_price = isset($_GET['max_price']) && $_GET['max_price'] !== "" ? (float) $_GET['max_price'] : 1000000;

        // Search Query
        $query = "SELECT COUNT(*) FROM products1 WHERE 1";
        if ($search_name !== "") {
          $query .= " AND Product_Name LIKE '%$search_name%'";
        }
        $query .= " AND Price_per_unit BETWEEN $min_price AND $max_price";

        $total_items_result = mysqli_query($conn, $query);
        $total_items_row = mysqli_fetch_row($total_items_result);
        $total_items = $total_items_row[0];
        $total_pages = ceil($total_items / $items_per_page);

        // Get the current page from URL, default to page 1
        $current_pg = isset($_GET['pg']) && is_numeric($_GET['pg']) ? (int) $_GET['pg'] : 1;
        $current_pg = max(1, min($current_pg, $total_pages));
        $start_limit = ($current_pg - 1) * $items_per_page;

        // Fetch only the results for the current page
        $query = "SELECT * FROM products1 WHERE 1";
        if ($search_name !== "") {
          $query .= " AND Product_Name LIKE '%$search_name%'";
        }
        $query .= " AND Price_per_unit BETWEEN $min_price AND $max_price";
        $query .= " LIMIT $start_limit, $items_per_page";

        $product_query = mysqli_query($conn, $query);
        ?>




        <!-- Product Grid -->
        <h2 class="text-2xl font-bold mb-4 text-center" data-aos="fade-right">Products</h2>




        <!-- Search Section -->
        <div class="mb-6 px-4">
          <form method="GET" class="flex flex-wrap gap-4 justify-center items-center">
            <input type="text" name="search_name" placeholder="Product Name"
              class="border p-2 rounded-lg w-full sm:w-1/4">
            <!-- Adding List menu user can select and based on that it will update the search results -->






            <input type="number" name="min_price" placeholder="Min Price" class="border p-2 rounded-lg w-full sm:w-1/4">
            <input type="number" name="max_price" placeholder="Max Price" class="border p-2 rounded-lg w-full sm:w-1/4">
            <div class="flex gap-2">
              <button type="submit"
                class="bg-yellow-500 text-black px-4 py-2 rounded-lg hover:bg-yellow-600 shadow-md transition duration-200 transform hover:scale-105 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                  stroke="currentColor" class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-4.35-4.35M17 10.5A6.5 6.5 0 1 1 10.5 4a6.5 6.5 0 0 1 6.5 6.5z" />
                </svg>
                Search
              </button>

              <?php if ($search_name !== "" || $min_price !== 0 || $max_price !== 1000000) { ?>
                <a href="index.php"
                  class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 shadow-md transition duration-200 transform hover:scale-105">
                  Clear
                </a>
              <?php } ?>
            </div>
          </form>
        </div>


        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <?php while ($product_row = mysqli_fetch_assoc($product_query)) { ?>
            <div
              class="bg-white p-4 rounded-lg shadow-lg transform transition-transform duration-300 hover:scale-105 flex flex-col justify-between h-full"
              data-aos="flip-up">
              <img alt="Product Image" class="w-full h-32 rounded-lg object-cover mb-4"
                src="uploaded_img/<?php echo htmlspecialchars($product_row['image']); ?>" data-aos="fade-in" />
              <div>
                <h3 class="text-lg font-bold" data-aos="fade-right">Product Name -
                  <?php echo htmlspecialchars($product_row['Product_Name']); ?>
                </h3>
                <p data-aos="fade-right"><span class="font-bold">Price per Quintal -
                  </span>₹<?php echo htmlspecialchars($product_row['Price_per_unit']); ?>/-</p>
                <p data-aos="fade-left"><span class="font-bold">Available Quantity -
                  </span><?php echo htmlspecialchars($product_row['Available_Quantity']); ?> Quintal</p>
                <p data-aos="fade-right"><span class="font-bold">Category -
                  </span><?php echo htmlspecialchars($product_row['Category']); ?></p>
                <p data-aos="fade-left"><span class="font-bold">Location -
                  </span><?php echo htmlspecialchars($product_row['Location']); ?></p>
              </div>
              <div class="mt-4">
                <a href="product_details.php?id=<?php echo $product_row['id']; ?>" class="w-full">
                  <button
                    class="bg-yellow-500 text-black py-2 px-4 rounded-xl hover:bg-yellow-600 w-full text-center hover:text-white"
                    data-aos="zoom-in" data-aos-delay="200">
                    View Details
                  </button>
                </a>
              </div>
            </div>
          <?php } ?>
        </div>

        <!-- Pagination Navigation -->
        <div class="flex justify-center mt-8 space-x-2" data-aos="fade-up">
          <?php if ($current_pg > 1) { ?>
            <a href="?pg=<?php echo $current_pg - 1; ?>&search_name=<?php echo urlencode($search_name); ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>"
              class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">Previous</a>
          <?php } ?>
          <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
            <a href="?pg=<?php echo $i; ?>&search_name=<?php echo urlencode($search_name); ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>"
              class="px-4 py-2 rounded-lg <?php echo ($i == $current_pg) ? 'bg-blue-500 text-white' : 'bg-gray-300 hover:bg-gray-400'; ?>"><?php echo $i; ?></a>
          <?php } ?>
          <?php if ($current_pg < $total_pages) { ?>
            <a href="?pg=<?php echo $current_pg + 1; ?>&search_name=<?php echo urlencode($search_name); ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>"
              class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">Next</a>
          <?php } ?>
        </div>
      </div>


      <section class="py-16" id="blog">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
          <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-10 lg:p-12 max-w-7xl mx-auto" data-aos="fade-up"
            data-aos-duration="1000">

            <!-- Heading -->
            <h2 class="text-4xl sm:text-5xl font-extrabold text-green-800 mb-10 flex items-center gap-2"
              data-aos="fade-right">
              🌿 <span>Our Latest Blog Posts</span>
            </h2>

            <!-- Blog Grid -->
            <div id="blogList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
              <?php
              if ($blogResult->num_rows > 0) {
                while ($blogRow = $blogResult->fetch_assoc()) {
                  echo '<div class="relative bg-white border border-green-200 rounded-xl shadow-md hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between h-full" data-aos="zoom-in">';

                  // Text Content
                  echo '<div class="p-5">';
                  echo '<h3 class="text-xl font-semibold text-green-800 mb-2">' . htmlspecialchars($blogRow['title']) . '</h3>';
                  echo '<p class="text-green-700 text-sm">' . htmlspecialchars($blogRow['desc1']) . '</p>';
                  echo '</div>';

                  // Image with fixed height
                  echo '<div class="h-48 sm:h-56 overflow-hidden rounded-b-xl">';
                  echo '<img src="' . htmlspecialchars($blogRow['image_url']) . '" alt="' . htmlspecialchars($blogRow['title']) . '" 
                   class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">';
                  echo '</div>';

                  // Button
                  echo '<form method="GET" action="' . htmlspecialchars($blogRow['blog_link']) . '" target="_blank" class="p-4">';
                  echo '<button class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition duration-300 font-medium">Read More</button>';
                  echo '</form>';

                  echo '</div>';
                }
              } else {
                echo '<p class="text-gray-600 text-center col-span-full text-lg" data-aos="fade-in">No blogs uploaded yet.</p>';
              }
              ?>
            </div>

            <!-- Pagination -->
            <div class="flex justify-center mt-10" data-aos="fade-up">
              <?php
              for ($i = 1; $i <= $totalBlogPages; $i++) {
                echo '<a href="?blogCurrentPage=' . $i . '" class="px-4 py-2 mx-1 rounded-lg text-lg font-semibold border ';
                echo $i == $currentBlogPage
                  ? 'bg-green-600 text-white border-green-600'
                  : 'bg-white  border-green-300 hover:bg-green-100 hover:text-green-700';
                echo ' transition duration-300 ease-in-out" data-aos="zoom-in" data-aos-delay="' . ($i * 100) . '">' . $i . '</a>';
              }
              ?>
            </div>

          </div>
        </div>
      </section>



      <section class="max-w-7xl mx-auto  px-8 py-16 rounded-3xl shadow-2xl mt-20 mb-20" id="learn">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-14 items-center">

          <!-- Left: Text Content -->
          <div>
            <h2 class="text-5xl font-extrabold text-green-800 mb-6">🌱 Learn With Us</h2>
            <p class="text-green-700 text-xl mb-8 leading-relaxed">
              Discover how our platform helps farmers get better prices, learn sustainable practices, and connect with
              trusted buyers.
              Our latest video explains how we’re transforming agriculture for the better.
            </p>
            <ul class="text-green-700 text-lg list-disc list-inside space-y-3">
              <li>Fair trade practices</li>
              <li>Real success stories</li>
              <li>Tips to grow & sell smart</li>
            </ul>
          </div>

          <!-- Right: Responsive Bigger Video -->
          <div class="w-full">
            <div class="w-full h-80 sm:h-[28rem] md:h-[30rem] lg:h-[34rem] rounded-xl overflow-hidden shadow-lg">
              <iframe class="w-full h-full rounded-lg" src="https://www.youtube.com/embed/ZJG1FjH1cEE?autoplay=1&mute=1"
                title="YouTube video" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
              </iframe>
            </div>
          </div>

        </div>
      </section>






      <?php
// session_start(); // ✅ Add at very top of the file (before any output)

$successMessage = "";
$errorMessage = "";

// If form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = htmlspecialchars(trim($_POST['name']));
  $email = htmlspecialchars(trim($_POST['email']));
  $message = htmlspecialchars(trim($_POST['message']));

  $to = "utsavjha93030@gmail.com";
  $subject = "New Contact Form Message from $name";
  $body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
  $headers = "From: $email\r\nReply-To: $email\r\n";

  if (mail($to, $subject, $body, $headers)) {
    $_SESSION['flash_message'] = "Message sent successfully!";
    $_SESSION['flash_type'] = "success";
  } else {
    $_SESSION['flash_message'] = "Something went wrong. Please try again.";
    $_SESSION['flash_type'] = "error";
  }

  // Reload the page without using header()
  echo "<script>window.location.href = window.location.href;</script>";
  exit();
}

// Set flash message from session and then clear it
if (isset($_SESSION['flash_message'])) {
  if ($_SESSION['flash_type'] == "success") {
    $successMessage = $_SESSION['flash_message'];
  } else {
    $errorMessage = $_SESSION['flash_message'];
  }
  unset($_SESSION['flash_message']);
  unset($_SESSION['flash_type']);
}
?>

<!-- HTML SECTION STARTS HERE -->
<section class="py-16 px-4 sm:px-6 lg:px-8 bg-green-50" id="contact">
  <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
    <!-- Left: Text + Illustration -->
    <div class="text-center lg:text-left space-y-6">
      <h2 class="text-4xl font-extrabold text-green-800">
        We’d love to hear from you!
      </h2>
      <p class="text-green-700 text-lg">
        Whether you have a question about features, need assistance, or just want to share feedback —
        our team is ready to listen. Fill out the form and we’ll get back to you as soon as possible.
      </p>
      <i class="fa-solid fa-phone"> Contact Illustration </i>
    </div>

    <!-- Right: Contact Form -->
    <form action="https://getform.io/f/aroorgnb" method="POST"
  class="bg-white shadow-xl rounded-2xl p-8 space-y-6">

  <div>
    <label for="name" class="block text-sm font-medium text-green-900">Full Name</label>
    <input type="text" name="name" id="name" required
      class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2" />
  </div>

  <div>
    <label for="email" class="block text-sm font-medium text-green-900">Email Address</label>
    <input type="email" name="email" id="email" required
      class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2" />
  </div>

  <div>
    <label for="message" class="block text-sm font-medium text-green-900">Your Message</label>
    <textarea id="message" name="message" rows="4" required
      class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2"></textarea>
  </div>

  <div class="text-center">
    <button type="submit"
      class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-md transition duration-300">
      Send Message
    </button>
  </div>
</form>

  </div>
</section>
      <section class="bg-green-50 py-12">
        <div class="max-w-7xl mx-auto px-6">
          <h2 class="text-3xl md:text-4xl font-bold text-center text-green-800 mb-10">What Our Farmers Say 🌾</h2>

          <!-- Infinite Loop Wrapper -->
          <div class="overflow-hidden">
            <div class="flex space-x-6 animate-slide-infinite w-max">

              <!-- Testimonial 1 -->
              <div class="bg-white shadow-lg rounded-xl p-6 w-72 min-w-[18rem] text-center">
                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Ramesh Kumar"
                  class="w-16 h-16 rounded-full mx-auto mb-4 border-2 border-green-500 shadow">
                <p class="text-green-700 mb-4 italic">“This portal changed the way I sell my crops. Fair pricing and
                  real buyers!”</p>
                <h4 class="font-bold text-green-900">Ramesh Kumar</h4>
                <p class="text-sm text-green-600">Farmer, Bihar</p>
              </div>

              <!-- Testimonial 2 -->
              <div class="bg-white shadow-lg rounded-xl p-6 w-72 min-w-[18rem] text-center">
                <img src="https://randomuser.me/api/portraits/women/45.jpg" alt="Sunita Devi"
                  class="w-16 h-16 rounded-full mx-auto mb-4 border-2 border-green-500 shadow">
                <p class="text-green-700 mb-4 italic">“Love the market insights. Helps me plan what to grow!”</p>
                <h4 class="font-bold text-green-900">Sunita Devi</h4>
                <p class="text-sm text-green-600">Farmer, UP</p>
              </div>

              <!-- Testimonial 3 -->
              <div class="bg-white shadow-lg rounded-xl p-6 w-72 min-w-[18rem] text-center">
                <img src="https://randomuser.me/api/portraits/men/74.jpg" alt="Manoj Yadav"
                  class="w-16 h-16 rounded-full mx-auto mb-4 border-2 border-green-500 shadow">
                <p class="text-green-700 mb-4 italic">“Great support and easy to list my produce. Highly recommended!”
                </p>
                <h4 class="font-bold text-green-900">Manoj Yadav</h4>
                <p class="text-sm text-green-600">Farmer, MP</p>
              </div>

              <!-- Duplicate Cards to Continue Loop Smoothly -->
              <div class="bg-white shadow-lg rounded-xl p-6 w-72 min-w-[18rem] text-center">
                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Ramesh Kumar"
                  class="w-16 h-16 rounded-full mx-auto mb-4 border-2 border-green-500 shadow">
                <p class="text-green-700 mb-4 italic">“This portal changed the way I sell my crops. Fair pricing and
                  real buyers!”</p>
                <h4 class="font-bold text-green-900">Ramesh Kumar</h4>
                <p class="text-sm text-green-600">Farmer, Bihar</p>
              </div>

              <div class="bg-white shadow-lg rounded-xl p-6 w-72 min-w-[18rem] text-center">
                <img src="https://randomuser.me/api/portraits/women/45.jpg" alt="Sunita Devi"
                  class="w-16 h-16 rounded-full mx-auto mb-4 border-2 border-green-500 shadow">
                <p class="text-green-700 mb-4 italic">“Love the market insights. Helps me plan what to grow!”</p>
                <h4 class="font-bold text-green-900">Sunita Devi</h4>
                <p class="text-sm text-green-600">Farmer, UP</p>
              </div>

            </div>
          </div>
        </div>
      </section>

      <style>
        @keyframes slide-infinite {
          0% {
            transform: translateX(0);
          }

          100% {
            transform: translateX(-50%);
          }
        }

        .animate-slide-infinite {
          animation: slide-infinite 40s linear infinite;
        }
      </style>







      <!--
        2] Deal Of The Day
        3]Blog
        4]Testimonials
        5]Contact Section Like That -->
      <footer class="bg-green-900 text-white py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <!-- About Us -->
            <div>
              <h2 class="text-xl font-bold mb-3">About Us</h2>
              <p class="text-base text-green-100 mb-3 leading-relaxed">
                We are 2nd-year CSE students building the Fair Trade Agri Portal to support farmers through tech.
              </p>
              <div class="space-y-1 text-green-100 text-sm">
                <div class="flex items-center">
                  <i class="fas fa-phone-alt mr-2 text-lime-300"></i>
                  <span>+91 9303010351</span>
                </div>
                <div class="flex items-center">
                  <i class="fas fa-envelope mr-2 text-lime-300"></i>
                  <span>utsavjha93030@gmail.com</span>
                </div>
              </div>
            </div>

            <!-- Meet the Team -->
            <div class="text-center md:text-left">
              <h2 class="text-xl font-bold mb-3">Meet the Team</h2>
              <ul class="space-y-1 text-base text-green-100">
                <li>Utsav Kumar Jha <span class="text-lime-300">- Team Lead</span></li>
                <li>Piyush Mani Tiwari</li>
                <li>Pranaw Kumar</li>
                <li>Nitin Singh Sikarwar</li>
              </ul>
              <div class="flex justify-center md:justify-start mt-4 space-x-4 text-lg">
                <a href="https://www.linkedin.com/in/utsav-kumar-jha-78a4a72aa/" class="hover:text-lime-300"><i
                    class="fab fa-linkedin"></i></a>
                <a href="https://www.instagram.com/utsav91091/" class="hover:text-lime-300"><i
                    class="fab fa-instagram"></i></a>
                <a href="https://github.com/utsav1223" class="hover:text-lime-300"><i class="fab fa-github"></i></a>
              </div>
            </div>

            <!-- Quick Links -->
            <div class="text-center md:text-right">
              <h2 class="text-xl font-bold mb-3">Quick Links</h2>
              <ul class="space-y-1 text-base text-green-100">
                <li><a href="#Home" class="hover:text-white">Home</a></li>
                <li><a href="#about-us" class="hover:text-white">About</a></li>
                <li><a href="#marketplace" class="hover:text-white">Explore Market Place</a></li>
                <li><a href="#products" class="hover:text-white">Products</a></li>
                <li><a href="#blog" class="hover:text-white">Blog</a></li>
                <li><a href="#learn" class="hover:text-white">learn</a></li>
                <li><a href="#contact" class="hover:text-white">Contact Us</a></li>
              </ul>
            </div>
          </div>

          <div class="mt-8 border-t border-green-600 pt-4 text-center text-sm text-green-200">
            © 2025 Fair Trade Agri Portal. All Rights Reserved.
          </div>
        </div>
      </footer>
  </main>
  <script>
    const slides = document.querySelector(".slides");
    const slideItems = document.querySelectorAll(".slide");
    const prevBtn = document.querySelector(".prev");
    const nextBtn = document.querySelector(".next");
    const dots = document.querySelectorAll(".dot");

    let currentIndex = 0;
    const totalSlides = slideItems.length;

    function updateSlider(index) {
      slides.style.transform = `translateX(-${index * 100}%)`;
      dots.forEach(dot => dot.classList.remove("bg-opacity-100"));
      dots[index].classList.add("bg-opacity-100");
    }

    nextBtn.addEventListener("click", () => {
      currentIndex = (currentIndex + 1) % totalSlides;
      updateSlider(currentIndex);
    });

    prevBtn.addEventListener("click", () => {
      currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
      updateSlider(currentIndex);
    });

    dots.forEach(dot => {
      dot.addEventListener("click", (e) => {
        currentIndex = parseInt(e.target.getAttribute("data-slide"));
        updateSlider(currentIndex);
      });
    });

    setInterval(() => {
      currentIndex = (currentIndex + 1) % totalSlides;
      updateSlider(currentIndex);
    }, 5000);





    window.addEventListener("scroll", function () {
      let navbar = document.getElementById("navbar");
      if (window.scrollY > 50) {
        navbar.classList.add("bg-green-900", "bg-opacity-90", "shadow-lg");
        navbar.classList.remove("bg-transparent", "backdrop-blur-md", "bg-opacity-20");
      } else {
        navbar.classList.remove("bg-green-900", "bg-opacity-90", "shadow-lg");
        navbar.classList.add("bg-transparent", "backdrop-blur-md", "bg-opacity-20");
      }
    });



    AOS.init({
      duration: 1000, // Animation speed
      easing: "ease-in-out", // Smooth effect
      once: true // Animation runs once when loaded
    });
  </script>




  <!-- JavaScript for Countdown Timer -->
  <script>
    function startCountdown() {
      let hours = 23;
      let minutes = 59;
      let seconds = 59;

      function updateTimer() {
        let timerElement = document.getElementById("running-timer");
        if (!timerElement) return;

        let timeString = `${hours}h ${minutes}m ${seconds}s`;
        timerElement.textContent = timeString;

        if (hours === 0 && minutes === 0 && seconds === 0) {
          timerElement.textContent = "⏳ Deal Expired!";
          return;
        }

        seconds--;
        if (seconds < 0) {
          seconds = 59;
          minutes--;
        }
        if (minutes < 0) {
          minutes = 59;
          hours--;
        }

        setTimeout(updateTimer, 1000);
      }

      updateTimer();
    }

    document.addEventListener("DOMContentLoaded", startCountdown);
  </script>


</body>

</html>