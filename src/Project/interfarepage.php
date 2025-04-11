
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Category</title>
    <!-- for integrating icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="../output.css">
</head>

<body class="bg-white">

    <!-- Header Section -->
    <!-- Header Section -->
<header class="fixed top-0 left-0 w-full bg-green-900 text-white py-4 shadow-lg z-50">
    <div class="max-w-6xl mx-auto px-6 flex justify-center items-center">
        <h1 class="text-2xl md:text-3xl font-bold text-center">Select Category</h1>
    </div>
</header>

<!-- Add margin-top to avoid overlap with fixed header -->
<div class="mt-16"></div>


    <!-- Main Selection Section -->
    <div class="flex flex-col md:flex-row justify-center items-center min-h-screen text-center px-4 md:px-0">
        <!-- Farmer Section -->
        <div class="w-full md:w-1/2 p-8 md:p-16 flex flex-col items-center">
            <div class="flex justify-center mb-4">
                <div class="bg-black text-white px-4 py-1 rounded-full text-sm md:text-lg font-bold">BUSINESS</div>
            </div>
            <h2 class="text-2xl md:text-4xl font-semibold mb-4">Are you <span class="italic text-green-800">Farmer?</span></h2>
            <p class="text-gray-700 mb-6 text-base md:text-lg max-w-md">As a farmer, you're the heart and soul of our
                marketplace. Share your farm's fresh produce with eager customers who appreciate quality and
                sustainability. Whether you have a small garden or a large farm, this platform allows you to reach a
                wider audience and receive fair compensation for your hard work.</p>
            <a href="form.php">
                <button
                    class="bg-green-900 text-white px-6 py-2 md:px-8 md:py-3 rounded-full mb-4 text-lg md:text-xl transition ease-in-out duration-300 hover:bg-green-800">Get
                    Started</button>
            </a>
            <p class="text-gray-700 text-sm md:text-base">Already have an account? <a href="login.php"
                    class="text-green-600">Log in</a></p>
        </div>

        <!-- Buyer Section -->
        <div
            class="w-full md:w-1/2 p-8 md:p-16 border-t md:border-t-0 md:border-l border-gray-300 flex flex-col items-center">
            <div class="flex justify-center mb-4">
                <div class="h-8 md:h-12"></div> <!-- Spacer to align the headers -->
            </div>
            <h2 class="text-2xl md:text-4xl font-semibold mb-4">Are you <span class="italic text-blue-800">Buyer?</span></h2>
            <p class="text-gray-700 mb-6 text-base md:text-lg max-w-md">As a buyer, you can explore a diverse array of
                fresh, locally-sourced produce straight from the farm to your table. Enjoy the best quality fruits,
                vegetables, and more, knowing you're supporting local farmers and sustainable practices. Find the
                produce you love, and savor the taste of farm-fresh goodness.</p>
            <a href="form.php">
                <button
                    class="bg-white text-black border border-gray-300 px-6 py-2 md:px-8 md:py-3 rounded-full mb-4 text-lg md:text-xl transition ease-in-out duration-300 hover:bg-gray-100">Get
                    Started</button>
            </a>
            <p class="text-gray-700 text-sm md:text-base">Already have an account? <a href="login.php"
                    class="text-blue-800">Log in</a></p>
        </div>
    </div>

    <!-- Footer Section -->
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
          <a href="#" class="hover:text-lime-300"><i class="fab fa-linkedin"></i></a>
          <a href="#" class="hover:text-lime-300"><i class="fab fa-instagram"></i></a>
          <a href="#" class="hover:text-lime-300"><i class="fab fa-github"></i></a>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="text-center md:text-right">
        <h2 class="text-xl font-bold mb-3">Quick Links</h2>
        <ul class="space-y-1 text-base text-green-100">
          <li><a href="#" class="hover:text-white">Home</a></li>
          <li><a href="#" class="hover:text-white">About</a></li>
          <li><a href="#" class="hover:text-white">Our Works</a></li>
          <li><a href="#" class="hover:text-white">Services</a></li>
          <li><a href="#" class="hover:text-white">Blog</a></li>
          <li><a href="#" class="hover:text-white">Contact Us</a></li>
        </ul>
      </div>
    </div>

    <div class="mt-8 border-t border-green-600 pt-4 text-center text-sm text-green-200">
      © 2025 Fair Trade Agri Portal. All Rights Reserved.
    </div>
  </div>
</footer>
</body>

</html>
