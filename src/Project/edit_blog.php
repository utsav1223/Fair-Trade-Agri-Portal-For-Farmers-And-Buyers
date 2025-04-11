









<?php
require 'admin_config1.php'; // Database connection

// Validate ID from URL
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid request. No valid ID provided.");
}

$blog_id = intval($_GET['id']); // Ensure ID is an integer

// Fetch existing blog data
$query = "SELECT * FROM blog WHERE id = $blog_id";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn)); // Debugging output
}

if (mysqli_num_rows($result) !== 1) {
    die("Blog not found. ID: " . $blog_id); // Debugging output
}

$blog = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc1 = mysqli_real_escape_string($conn, $_POST['desc1']);
    $blog_link = mysqli_real_escape_string($conn, $_POST['blog_link']);
    $image_url = mysqli_real_escape_string($conn, $_POST['image_url']); // Image URL input

    // Update query
    $update_query = "UPDATE blog SET 
                        title='$title', 
                        desc1='$desc1', 
                        blog_link='$blog_link', 
                        image_url='$image_url' 
                    WHERE id=$blog_id";

    if (mysqli_query($conn, $update_query)) {
        header("Location: admin.php"); // Redirect back to blog list
        exit();
    } else {
        echo "Error updating blog: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog</title>
    <link rel="stylesheet" href="../output.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-6">
    <div class="max-w-4xl w-full bg-white p-12 rounded-lg shadow-lg">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Edit Blog</h2>
        <form method="POST" class="space-y-6">
            
            <!-- Title -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($blog['title']) ?>"
                    class="w-full p-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"  maxlength="100" required>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Description</label>
                <textarea name="desc1" class="w-full p-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" minlength="200" required><?= htmlspecialchars($blog['desc1']) ?></textarea>
            </div>

            <!-- Blog Link -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Blog Link</label>
                <input type="url" name="blog_link" value="<?= htmlspecialchars($blog['blog_link']) ?>"
                    class="w-full p-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <!-- Blog Image URL -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Blog Image URL</label>
                <input type="text" name="image_url" value="<?= htmlspecialchars($blog['image_url']) ?>"
                    class="w-full p-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <div class="mt-3">
                    <span class="text-gray-600 text-sm">Current Image:</span>
                    <img src="<?= $blog['image_url'] ?>" alt="Current Image" class="h-64 w-full object-cover mt-2 rounded-lg shadow-md">
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-between items-center mt-6">
                <a href="admin.php" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition">Cancel</a>
                <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition">Update Blog</button>
            </div>
        </form>
    </div>
</body>
</html>