<header class="bg-blue-800 text-white p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
        <!-- Logo/Site Title -->
        <a href="index.php" class="text-3xl font-bold text-yellow-400 hover:text-yellow-300 transition duration-300">ToyLand</a>

        <!-- Navigation Links -->
        <nav>
            <ul class="flex space-x-6">
                <li><a href="index.php" class="hover:text-yellow-400 transition duration-300">Home</a></li>
                <li><a href="public/products.php" class="hover:text-yellow-400 transition duration-300">Products</a></li>
                <li><a href="#" class="hover:text-yellow-400 transition duration-300">About Us</a></li>
                <li><a href="#" class="hover:text-yellow-400 transition duration-300">Contact</a></li>
            </ul>
        </nav>

        <!-- User Actions (Login, Register, Cart) -->
        <div class="flex items-center space-x-4">
            <a href="public/register.php#" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-full shadow-md transition duration-300">Login</a>
            <a href="public/register.php" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-full shadow-md transition duration-300">Register</a>
            <a href="api/cart.php" class="bg-yellow-400 hover:bg-yellow-500 text-blue-900 font-bold py-2 px-4 rounded-full shadow-md transition duration-300 flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.5 14 6.354 14H15a1 1 0 000-2H6.354c-.254 0-.51.053-.746.159l.809-3.238a1 1 0 00-1.022-1.242l-1.474.368L7.136 7.136A1 1 0 008 7h5a1 1 0 00.95-.623l3-7H3zM6 16a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                </svg>
                <span>Cart</span>
            </a>
        </div>
    </div>
</header>
