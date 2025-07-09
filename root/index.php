<?php
// Include the header file from the 'includes' folder
include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToyLand - Your Ultimate Toy Store</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom styles for Inter font */
        body {
            font-family: 'Inter', sans-serif;
        }
        /* Ensure images are responsive */
        .product-image {
            width: 100%;
            height: 200px; /* Fixed height for product images */
            object-fit: cover; /* Cover the area, cropping if necessary */
            border-radius: 8px;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

    <main class="container mx-auto px-4 py-8">
        <!-- Opening Banner Section -->
        <section class="bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl shadow-lg p-8 mb-12 text-center">
            <h1 class="text-5xl font-extrabold mb-4 animate-fade-in-down">Welcome to ToyLand!</h1>
            <p class="text-xl mb-6 leading-relaxed animate-fade-in-up">
                Discover a magical world of toys for all ages. From action figures to educational games,
                we have something special for every child (and child at heart!).
            </p>
            <button class="bg-yellow-400 hover:bg-yellow-500 text-blue-900 font-bold py-3 px-8 rounded-full shadow-lg transform hover:scale-105 transition duration-300 ease-in-out">
                Shop Now!
            </button>
        </section>

        <!-- Products Section -->
        <section class="mb-12">
            <h2 class="text-4xl font-bold text-center text-blue-800 mb-8">Our Bestsellers</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                <!-- Product Card 1 -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition duration-300 ease-in-out">
                    <img src="https://placehold.co/400x200/FFD700/000000?text=Action+Figure" alt="Action Figure" class="product-image">
                    <div class="p-5">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Mighty Hero Action Figure</h3>
                        <p class="text-gray-600 text-sm mb-4">Unleash epic adventures with this poseable hero!</p>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-blue-700">$19.99</span>
                            <button class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-full shadow-md">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product Card 2 -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition duration-300 ease-in-out">
                    <img src="https://placehold.co/400x200/ADD8E6/000000?text=Building+Blocks" alt="Building Blocks" class="product-image">
                    <div class="p-5">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Creative Building Blocks Set</h3>
                        <p class="text-gray-600 text-sm mb-4">Build anything you can imagine with colorful blocks.</p>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-blue-700">$29.50</span>
                            <button class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-full shadow-md">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product Card 3 -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition duration-300 ease-in-out">
                    <img src="https://placehold.co/400x200/90EE90/000000?text=Plush+Toy" alt="Plush Toy" class="product-image">
                    <div class="p-5">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Cuddly Bear Plush Toy</h3>
                        <p class="text-gray-600 text-sm mb-4">Soft and huggable, your new best friend.</p>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-blue-700">$15.00</span>
                            <button class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-full shadow-md">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product Card 4 -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition duration-300 ease-in-out">
                    <img src="https://placehold.co/400x200/FFB6C1/000000?text=Puzzle" alt="Puzzle" class="product-image">
                    <div class="p-5">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Challenging Jigsaw Puzzle</h3>
                        <p class="text-gray-600 text-sm mb-4">Hours of fun with this intricate 1000-piece puzzle.</p>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-blue-700">$12.75</span>
                            <button class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-full shadow-md">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

</body>
</html>

<?php
// Include the footer file from the 'includes' folder
include 'includes/footer.php';
?>
