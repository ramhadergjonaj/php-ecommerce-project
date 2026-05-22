<?php

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/functions/helpers.php';
require_once dirname(__DIR__) . '/includes/cart.php';

requireLogin();

if (!isAdmin()) {
    $productId = (int) ($_POST['product_id'] ?? $_GET['product_id'] ?? 0);
    $quantity = max(1, (int) ($_POST['quantity'] ?? 1));

    if ($productId > 0 && getProductById($productId)) {
        addToCart($productId, $quantity);
        $_SESSION['flash_success'] = 'Produkti u shtua në shportë.';
    } else {
        $_SESSION['flash_error'] = 'Produkti nuk u gjet.';
    }

    $redirect = $_POST['redirect'] ?? 'pages/cart.php';

    if (!empty($_POST['redirect']) && strpos($_POST['redirect'], 'product_details') !== false) {
        $redirect = 'pages/product_details.php?id=' . $productId;
    }

    redirect_to($redirect);
}

redirect_to('pages/products.php');
