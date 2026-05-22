<?php

require_once dirname(__DIR__) . '/functions/helpers.php';

function initCart(): void
{
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

function getCart(): array
{
    initCart();

    return $_SESSION['cart'];
}

function getCartCount(): int
{
    $count = 0;

    foreach (getCart() as $qty) {
        $count += (int) $qty;
    }

    return $count;
}

function addToCart(int $productId, int $quantity = 1): void
{
    initCart();
    $quantity = max(1, $quantity);

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
}

function updateCartItem(int $productId, int $quantity): void
{
    initCart();

    if ($quantity <= 0) {
        unset($_SESSION['cart'][$productId]);
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
}

function removeFromCart(int $productId): void
{
    initCart();
    unset($_SESSION['cart'][$productId]);
}

function clearCart(): void
{
    $_SESSION['cart'] = [];
}

/**
 * @return array<int, array{product: Product, quantity: int, subtotal: float}>
 */
function getCartLineItems(): array
{
    $lines = [];

    foreach (getCart() as $productId => $quantity) {
        $product = getProductById((int) $productId);

        if ($product) {
            $lines[] = [
                'product' => $product,
                'quantity' => (int) $quantity,
                'subtotal' => $product->getPrice() * (int) $quantity,
            ];
        }
    }

    return $lines;
}

function getCartTotal(): float
{
    $total = 0.0;

    foreach (getCartLineItems() as $line) {
        $total += $line['subtotal'];
    }

    return $total;
}
