<?php
require_once 'db.php'; // your PDO connection

// Get sale categories with products on sale
function getSaleCategories(PDO $pdo) {
    $stmt = $pdo->query("
        SELECT c.category_id, c.name, c.image,
               (
                 SELECT MAX(p.price - IFNULL(p.sale_price, p.price))
                 FROM products p
                 WHERE p.status = 'active'
                   AND p.sale_price IS NOT NULL
                   AND (p.category_id = c.category_id OR p.category_id IN (
                       SELECT category_id 
                       FROM categories 
                       WHERE parent_id = c.category_id
                   ))
               ) AS discount_amount
        FROM categories c
        WHERE c.parent_id IS NULL
        ORDER BY c.name ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get featured sale products
function getFeaturedSaleProducts(PDO $pdo, $limit = 4) {
    $stmt = $pdo->prepare("
        SELECT *
        FROM products
        WHERE status = 'active' AND featured = 1 AND sale_price IS NOT NULL
        ORDER BY RAND()
        LIMIT ?
    ");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get flash sale products (recently added on sale)
function getFlashSaleProducts(PDO $pdo, $limit = 2) {
    $stmt = $pdo->prepare("
        SELECT *
        FROM products
        WHERE status = 'active' AND sale_price IS NOT NULL
        ORDER BY created_at DESC
        LIMIT ?
    ");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get clearance products (low stock or on sale but not featured)
function getClearanceProducts(PDO $pdo, $limit = 4) {
    $stmt = $pdo->prepare("
        SELECT *
        FROM products
        WHERE status = 'active' AND sale_price IS NOT NULL AND (featured = 0 OR stock_quantity <= 5)
        ORDER BY stock_quantity ASC, price ASC
        LIMIT ?
    ");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



