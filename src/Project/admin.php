<?php
// error_reporting(0);

include('admin_config1.php'); // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_deal_id'])) {
        // Delete Deal
        $delete_deal_id = $_POST['delete_deal_id'];
        $conn->query("DELETE FROM deal WHERE id = '$delete_deal_id'");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        // Validate and Insert Deal
        if (!empty($_POST['product_name']) && !empty($_POST['product_description']) && !empty($_POST['product_price']) && !empty($_POST['product_link']) && isset($_FILES['product_image'])) {

            $deal_product_name = $_POST['product_name'];
            $deal_product_description = $_POST['product_description'];
            $deal_product_price = $_POST['product_price'];
            $deal_product_link = $_POST['product_link'];

            // Handle Image Upload
            $deal_upload_dir = "uploaded_img/";
            $deal_image_name = $_FILES['product_image']['name'];
            $deal_image_tmp_name = $_FILES['product_image']['tmp_name'];
            $deal_image_path = $deal_upload_dir . basename($deal_image_name);

            if (move_uploaded_file($deal_image_tmp_name, $deal_image_path)) {
                $conn->query("INSERT INTO deal (Product_Name, Product_desc, Price_per_unit, image,pd_link) 
                              VALUES ('$deal_product_name', '$deal_product_description', '$deal_product_price', '$deal_image_path','$deal_product_link')");
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }
    }
}

// Pagination Setup for Deals
$dealLimit = 1; // Limit to 1 deal per page
$currentDealPage = isset($_GET['dealPage']) ? (int) $_GET['dealPage'] : 1; // Renamed variables
$dealOffset = ($currentDealPage - 1) * $dealLimit;

// Total Deals Count for Pagination
$deal_count_sql = "SELECT COUNT(*) AS total_deals FROM deal"; // Renamed variables
$deal_count_result = $conn->query($deal_count_sql);
$totalDealRows = $deal_count_result->fetch_assoc()['total_deals'];
$totalDealPages = ceil($totalDealRows / $dealLimit);

// Fetch Deals for the Current Page
$fetch_deal_sql = "SELECT * FROM deal ORDER BY id DESC LIMIT $dealLimit OFFSET $dealOffset"; // Renamed variables
$deal_result = $conn->query($fetch_deal_sql);



// Handle Insert (Upload Blog)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["upload"])) {
    $title = $_POST["title"];
    $desc1 = $_POST["desc1"];
    $image = $_POST["image"];
    $blog_link = $_POST["blog_link"];

    $sql = "INSERT INTO blog (title, desc1, image_url, blog_link) VALUES ('$title', '$desc1', '$image', '$blog_link')";
    if ($conn->query($sql) === TRUE) {
        // Redirect to avoid resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle Delete
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])) {
    $id = $_POST["id"]; // ID to delete

    $sql = "DELETE FROM blog WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        // Redirect after deletion
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Pagination Variables
$blogLimit = 1; // Limit is 1 blog per page
$blogPage = isset($_GET['blogPage']) ? $_GET['blogPage'] : 1;
$blogStartFrom = ($blogPage - 1) * $blogLimit;

// Fetch Paginated Blogs
$sql = "SELECT * FROM blog LIMIT $blogStartFrom, $blogLimit";
$result = $conn->query($sql);

// Total Blogs for Pagination
$total_sql = "SELECT COUNT(id) AS total FROM blog";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$totalBlogPages = ceil($total_row['total'] / $blogLimit);
//market_insights//
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Delete all previous records
    $conn->query("DELETE FROM market_insights");

    // Validate Inputs
    if (!empty($_POST['productName']) && !empty($_POST['demandLocation']) && !empty($_POST['quantityRequired']) && !empty($_POST['imageUrl'])) {
        $product_name = $_POST['productName'];
        $demand_location = $_POST['demandLocation'];
        $quantity_required = $_POST['quantityRequired'];
        $image_link = $_POST['imageUrl'];

        // Insert data into database
        $conn->query("INSERT INTO market_insights (Prod_Name, Demand_Loc, Quantity_Req, image_link) 
                      VALUES ('$product_name', '$demand_location', '$quantity_required', '$image_link')");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fair Trade Agri Portal for Farmers</title>
    <link rel="stylesheet" href="../output.css">
    <style>
       

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 80px;
            /* Adjust this value to match the height of your header */
        }
    </style>
</head>

<body class="font-roboto bg-green-100">
<header class="bg-green-800 text-white px-6 py-4 flex items-center justify-between fixed top-0 left-0 w-full shadow-lg z-50">
    <!-- Logo & Title -->
    <div class="flex items-center space-x-3">
        <span class="text-2xl font-bold">Admin Dashboard</span>
        <span class="text-yellow-400 text-2xl">👤</span>
    </div>

    <!-- Desktop Menu -->
    <nav class="hidden md:flex items-center space-x-6">
        <a href="HomePage.php" class="text-yellow-400 font-semibold transition duration-300 hover:text-yellow-500">🏠 Home</a>
        <a href="#adddeal" class="hover:text-yellow-300 transition duration-300">Add Deal</a>
        <a href="#uploadblog" class="hover:text-yellow-300 transition duration-300">Upload Blog</a>
        <a href="#marketinsights" class="hover:text-yellow-300 transition duration-300">Market Insights</a>
        <a href="logout.php">
            <button class="border-2 border-yellow-400 px-4 py-2 rounded-lg bg-yellow-400 text-green-900 font-semibold transition duration-300 hover:bg-yellow-500 hover:text-white shadow-lg">
                Logout
            </button>
        </a>
    </nav>

    <!-- Mobile Menu Button -->
    <button id="menu-btn" class="md:hidden focus:outline-none">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <!-- Mobile Menu (Initially Hidden) -->
    <div id="mobile-menu" class="absolute top-16 left-0 w-full bg-green-800 text-white py-5 text-center shadow-lg hidden">
        <a href="HomePage.php" class="block py-2 text-yellow-400 font-semibold transition duration-300 hover:text-yellow-500">🏠 Home</a>
        <a href="#adddeal" class="block py-2 hover:text-yellow-300 transition duration-300">Add Deal</a>
        <a href="#uploadblog" class="block py-2 hover:text-yellow-300 transition duration-300">Upload Blog</a>
        <a href="#marketinsights" class="block py-2 hover:text-yellow-300 transition duration-300">Market Insights</a>
        <a href="logout.php" class="block py-2">
            <button class="border-2 border-yellow-400 px-4 py-2 rounded-lg bg-yellow-400 text-green-900 font-semibold transition duration-300 hover:bg-yellow-500 hover:text-white shadow-lg">
                Logout
            </button>
        </a>
    </div>
</header>

<!-- JavaScript for Mobile Menu -->
<script>
    document.getElementById('menu-btn').addEventListener('click', function () {
        let menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
        menu.classList.toggle('block');
    });
</script>
<div class="max-w-7xl mx-auto px-4 my-10">
    <section class="p-6 bg-white mt-25 rounded-lg shadow-lg mb-10" id="adddeal">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Add a New Deal</h2>
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <label for="product_name" class="text-gray-700">Product Name</label>
            <input id="product_name" class="w-full border border-gray-300 rounded-lg p-2 mt-1" name="product_name"
                placeholder="Product Name" type="text" required />

            <label for="product_description" class="text-gray-700">Product Description</label>
            <textarea id="product_description" class="w-full border border-gray-300 rounded-lg p-2 mt-1"
                name="product_description" placeholder="Product Description" required></textarea>

            <label for="product_price" class="text-gray-700">Product Price</label>
            <input id="product_price" class="w-full border border-gray-300 rounded-lg p-2 mt-1" name="product_price"
                placeholder="Product Price" type="number" required />

            <label for="product_link" class="text-gray-700">Product Link</label>
            <input id="product_link" class="w-full border border-gray-300 rounded-lg p-2 mt-1" name="product_link"
                placeholder="Product Link" type="url" required />

            <label for="product_image" class="text-gray-700">Product Image</label>
            <input id="product_image" class="w-full border border-gray-300 rounded-lg p-2 mt-1" name="product_image"
                type="file" accept="image/*" required />

            <button class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 w-full md:w-auto"
                type="submit">Add Deal</button>
        </form>
    </section>

    <section class="p-6 bg-white rounded-lg shadow-lg">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Deal of the Day</h2>
        <?php if ($deal_result->num_rows > 0): ?>
            <?php while ($row = $deal_result->fetch_assoc()): ?>
                <div class="relative flex flex-col md:flex-row items-center bg-gray-100 p-6 rounded-lg mb-4">
                    <div class="md:w-1/2 flex justify-center">
                        <img src="<?php echo $row['image']; ?>" alt="Product Image"
                            class="w-full md:w-3/4 h-64 object-cover rounded-lg shadow-md">
                    </div>
                    <div class="md:w-1/2 p-4">
                        <h3 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($row['Product_Name']); ?></h3>
                        <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($row['Product_desc']); ?></p>
                        <div class="flex items-center mt-2">
                            <span
                                class="text-green-700 font-bold text-xl">₹<?php echo htmlspecialchars($row['Price_per_unit']); ?></span>
                        </div>
                        <div class="mt-4 flex space-x-2">
                            <!-- Edit Button -->
                            <a href="edit1_deal.php?id=<?php echo $row['id']; ?>"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                Edit
                            </a>
                            <!-- Delete Button -->
                            <form method="POST">
                                <input type="hidden" name="delete_deal_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-600 text-center">No deals available.</p>
        <?php endif; ?>

        <div class="mt-6 flex justify-between">
            <a href="?dealPage=<?php echo max(1, $currentDealPage - 1); ?>"
                class="px-4 py-2 bg-gray-300 rounded-lg <?php echo ($currentDealPage <= 1) ? 'pointer-events-none opacity-50' : ''; ?>">Previous</a>
            <span>Page <?php echo $currentDealPage; ?> of <?php echo $totalDealPages; ?></span>
            <a href="?dealPage=<?php echo min($totalDealPages, $currentDealPage + 1); ?>"
                class="px-4 py-2 bg-gray-300 rounded-lg <?php echo ($currentDealPage >= $totalDealPages) ? 'pointer-events-none opacity-50' : ''; ?>">Next</a>
        </div>
    </section>
    <section class="py-12">
        <div class="">
            <div class="bg-white rounded-lg shadow-md p-6" id="uploadblog">
                <h2 class="text-3xl font-bold mb-6  text-center">Upload Blog</h2>
                <form method="POST" action="">
                    <!-- Title Input -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="title">Title</label>
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 border-gray-300 leading-tight focus:outline focus:outline-black"
                            id="title" placeholder="Enter title (max 50 characters)" type="text" name="title"
                            maxlength="50" required />
                    </div>
                    <!-- Description Input -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="desc1">Description</label>
                        <textarea
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 border-gray-300 leading-tight focus:outline focus:outline-black"
                            id="desc1" placeholder="Enter description (100 to 200 characters)" rows="5" name="desc1"
                            minlength="100" maxlength="200" required></textarea>
                    </div>
                    <!-- Image URL Input -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="image">Image URL</label>
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 border-gray-300 leading-tight focus:outline focus:outline-black"
                            id="image" placeholder="Enter image URL" type="text" name="image" required />
                    </div>
                    <!-- Blog Link Input -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="blog-link">Blog Link</label>
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 border-gray-300 leading-tight focus:outline focus:outline-black"
                            id="blog-link" placeholder="Enter blog link" type="text" name="blog_link" required />
                    </div>
                    <!-- Submit Button -->
                    <div class="flex items-center justify-between">
                        <button
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            type="submit" name="upload">
                            Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <section class=" px-4 my-10">
    <div class="bg-white rounded-lg shadow-md p-6 w-full">
        <h2 class="text-2xl md:text-3xl font-bold mb-6 text-center">
            Blog List
        </h2>

        <div id="blogList">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="relative mb-6 p-6 border rounded-lg shadow-lg bg-gray-50">';

                    // Blog Title and Description
                    echo '<h3 class="text-xl md:text-2xl font-semibold text-gray-800 mb-2">' . htmlspecialchars($row['title']) . '</h3>';
                    echo '<p class="text-gray-600 mb-4 text-sm md:text-base">' . htmlspecialchars($row['desc1']) . '</p>';

                    // Blog Image
                    echo '<img src="' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['title']) . '" class="w-full h-40 md:h-56 object-cover rounded-md shadow mb-4">';

                    // Buttons (Read More, Edit, Delete)
                    echo '<div class="flex flex-wrap md:flex-nowrap justify-end space-x-2 md:space-x-3">';
                    echo '<a href="' . htmlspecialchars($row['blog_link']) . '" target="_blank" class="bg-blue-500 text-white px-3 md:px-4 py-2 rounded-md text-sm md:text-base font-semibold hover:bg-blue-600 transition">Read More</a>';
                    echo '<a href="edit_blog.php?id=' . urlencode($row['id']) . '" class="bg-yellow-500 text-white px-3 md:px-4 py-2 rounded-md text-sm md:text-base font-semibold hover:bg-yellow-600 transition">Edit</a>';
                    echo '<form method="POST" action="" onsubmit="return confirmDelete();" class="inline">';
                    echo '<input type="hidden" name="id" value="' . htmlspecialchars($row['id']) . '">';
                    echo '<button type="submit" name="delete" class="bg-red-500 text-white px-3 md:px-4 py-2 rounded-md text-sm md:text-base font-semibold hover:bg-red-600 transition">Delete</button>';
                    echo '</form>';
                    echo '</div>'; // Buttons
                    echo '</div>'; // Blog Card
                }
            } else {
                echo '<p class="text-center text-gray-600">No blogs uploaded yet.</p>';
            }
            ?>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-8">
            <?php
            for ($i = 1; $i <= $totalBlogPages; $i++) {
                echo '<a href="?blogPage=' . $i . '" class="px-3 py-2 mx-1 border rounded-md text-sm md:text-base ';
                echo $i == $blogPage ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800 hover:bg-blue-500 hover:text-white transition';
                echo '">' . $i . '</a>';
            }
            ?>
        </div>
    </div>
</section>
    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this blog?");
        }
    </script>
<?php
include "config1.php"; // ✅ DB connection

// Form submission logic
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $category = $_POST['category'];
    $item_name = $_POST['item_name'];
    $unit = $_POST['unit'];
    $price = $_POST['price'];

    // Insert query
    $query = "INSERT INTO market_rates (category, item_name, unit, current_price) 
              VALUES ('$category', '$item_name', '$unit', '$price')";
    mysqli_query($conn, $query);
}
?>

<!-- 🌾 Add Market Rates Form Wrapper -->
<div class=" mt-24 p-6 bg-white shadow-lg rounded-lg">
  <h2 class="text-2xl font-bold text-green-700 mb-6 text-center">➕ Add Market Rates</h2>

  <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <input type="text" name="category" placeholder="Category (e.g., Grains)" required class="border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-green-500">
    
    <input type="text" name="item_name" placeholder="Item Name (e.g., Wheat)" required class="border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-green-500">
    
    <input type="text" name="unit" placeholder="Unit (e.g., per Quintal)" required class="border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-green-500">
    
    <input type="number" step="0.01" name="price" placeholder="Price in ₹" required class="border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-green-500">
  </form>

  <!-- 🔘 Button Outside Grid -->
  <div class="mt-4">
    <button type="submit" form="add-rate-form" class="bg-green-600 text-white text-sm font-medium px-4 py-2 rounded hover:bg-green-700 transition">
      Add Rate
    </button>
  </div>
</div>
<div class="bg-white shadow-lg rounded-lg p-6 my-8 mb-5" id="marketinsights">
    <h2 class="text-xl font-bold text-gray-800 mb-4 text-center">Current Demand Data</h2>

    <!-- Input Form -->
    <form method="post">
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Product Name:</label>
            <input type="text" name="productName" class="border border-gray-300 rounded-md w-full p-3"
                required placeholder="e.g., Wheat, Tomatoes, Mangoes" />
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Demand Location:</label>
            <input type="text" name="demandLocation" class="border border-gray-300 rounded-md w-full p-3"
                required placeholder="e.g., Delhi, Mumbai, Ranchi" />
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Quantity Required:</label>
            <input type="text" name="quantityRequired" class="border border-gray-300 rounded-md w-full p-3"
                required placeholder="e.g., 10 Quintals" />
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Image URL:</label>
            <input type="text" name="imageUrl" class="border border-gray-300 rounded-md w-full p-3"
                required placeholder="e.g., https://example.com/image.jpg" />
        </div>

        <button type="submit"
            class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-md">
            Submit Data
        </button>
    </form>
</div>

    </div>
</body>

</html>