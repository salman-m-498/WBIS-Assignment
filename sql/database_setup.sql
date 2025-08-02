-- Create the user table
CREATE TABLE `user` (
    `user_id` VARCHAR(11) NOT NULL PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'member') NOT NULL DEFAULT 'member',
    `profile_pic` VARCHAR(255) NULL,
    `remember_token` VARCHAR(64) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_email` (`email`),
    INDEX `idx_username` (`username`),
    INDEX `idx_role` (`role`)
);

-- Create user_profiles table for additional user information
CREATE TABLE `user_profiles` (
    `profile_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` VARCHAR(11) NOT NULL,
    `first_name` VARCHAR(50) NOT NULL,
    `last_name` VARCHAR(50) NOT NULL,
    `phone` VARCHAR(20) NULL,
    `date_of_birth` DATE NULL,
    `address` TEXT NULL,
    `newsletter_subscription` BOOLEAN DEFAULT FALSE,
    `marketing_emails` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE CASCADE,
    INDEX `idx_user_id` (`user_id`)
);

-- Insert a default admin user (password: admin123)
-- You can change the password by updating the hash below
INSERT INTO `user` (`user_id`, `username`, `email`, `password`, `role`) VALUES
('USR00000001', 'admin', 'admin@toylandstore.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert admin profile
INSERT INTO `user_profiles` (`user_id`, `first_name`, `last_name`, `phone`, `address`) VALUES
('USR00000001', 'Admin', 'User', '555-0000', 'ToyLand Store HQ, Admin City, AC 12345, USA');

-- Optional: Create additional tables for future use

-- Categories table
CREATE TABLE `categories` (
    `category_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `image` VARCHAR(255) NULL,
    `parent_id` INT NULL,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`parent_id`) REFERENCES `categories`(`category_id`) ON DELETE SET NULL,
    INDEX `idx_name` (`name`),
    INDEX `idx_status` (`status`)
);

-- Products table
CREATE TABLE `products` (
    `product_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL,
    `description` TEXT NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    `sale_price` DECIMAL(10, 2) NULL,
    `sku` VARCHAR(50) UNIQUE NOT NULL,
    `stock_quantity` INT DEFAULT 0,
    `category_id` INT NULL,
    `image` VARCHAR(255) NULL,
    `status` ENUM('active', 'inactive', 'out_of_stock') DEFAULT 'active',
    `featured` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`category_id`) ON DELETE SET NULL,
    INDEX `idx_name` (`name`),
    INDEX `idx_sku` (`sku`),
    INDEX `idx_status` (`status`),
    INDEX `idx_featured` (`featured`)
);

-- Orders table
CREATE TABLE `orders` (
    `order_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` VARCHAR(11) NOT NULL,
    `order_number` VARCHAR(20) UNIQUE NOT NULL,
    `total_amount` DECIMAL(10, 2) NOT NULL,
    `status` ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    `shipping_address` TEXT NOT NULL,
    `billing_address` TEXT NOT NULL,
    `payment_method` VARCHAR(50) NULL,
    `payment_status` ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE CASCADE,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_order_number` (`order_number`),
    INDEX `idx_status` (`status`)
);

-- Wishlist table
CREATE TABLE `wishlist` (
    `wishlist_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` VARCHAR(11) NOT NULL,
    `product_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_user_product` (`user_id`, `product_id`),
    INDEX `idx_user_id` (`user_id`)
);

-- Reviews table
CREATE TABLE `reviews` (
    `review_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` VARCHAR(11) NOT NULL,
    `product_id` INT NOT NULL,
    `rating` INT NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
    `title` VARCHAR(200) NULL,
    `comment` TEXT NULL,
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`) ON DELETE CASCADE,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_product_id` (`product_id`),
    INDEX `idx_rating` (`rating`),
    INDEX `idx_status` (`status`)
);

-- Show tables to verify creation
SHOW TABLES;
