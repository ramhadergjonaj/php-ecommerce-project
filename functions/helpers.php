<?php

require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Admin.php';

function getUsers() {
    return [
        new Admin(1, 'Admin User', 'admin@shop.com', 'admin123'),
        new User(2, 'Normal User', 'user@shop.com', 'customer', 'user123'),
        new User(3, 'Test User', 'test@shop.com', 'customer', 'test123')
    ];
}

function getProducts() {
    return [
        new Product(
            1,
            "MacBook Pro M3",
            1999.99,
            "Electronics",
            "https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=800&auto=format&fit=crop",
            "Apple MacBook Pro me çip M3, performancë e lartë për profesionistë."
        ),
        new Product(
            2,
            "Samsung Galaxy S24",
            999.00,
            "Electronics",
            "https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?q=80&w=800&auto=format&fit=crop",
            "Smartphone i fundit nga Samsung me AI të integruar."
        ),
        new Product(
            3,
            "Kufje Sony WH-1000XM5",
            349.50,
            "Accessories",
            "https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?q=80&w=800&auto=format&fit=crop",
            "Kufje me zhurmë-ndalues nga Sony."
        ),
        new Product(
            4,
            "Karrige Ergonomike Herman Miller",
            1200.00,
            "Furniture",
            "https://images.unsplash.com/photo-1505843490538-5133c6c7d0e1?q=80&w=800&auto=format&fit=crop",
            "Karrige zyre me ergonomi të përsosur."
        ),
        new Product(
            5,
            "Tastierë Mekanike Keychron",
            95.00,
            "Accessories",
            "https://images.unsplash.com/photo-1595225476474-87563907a212?q=80&w=800&auto=format&fit=crop",
            "Tastierë mekanike wireless për programues dhe gamers."
        ),
        new Product(
            6,
            "Monitor LG UltraWide",
            450.00,
            "Electronics",
            "https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?q=80&w=800&auto=format&fit=crop",
            "Monitor 34-inch i gjerë për produktivitet maksimal."
        )
    ];
}

function getUserByEmail($email) {
    $users = getUsers();

    foreach ($users as $user) {
        if ($user->getEmail() === $email) {
            return $user;
        }
    }

    return null;
}

function getProductById($id) {
    $products = getProducts();

    foreach ($products as $product) {
        if ($product->getId() == $id) {
            return $product;
        }
    }

    return null;
}

function sortProductsByPrice($products, $order = 'asc') {
    usort($products, function($a, $b) use ($order) {
        if ($order === 'asc') {
            return $a->getPrice() <=> $b->getPrice();
        }

        return $b->getPrice() <=> $a->getPrice();
    });

    return $products;
}