<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../functions/helpers.php';

requireAdmin();

require_once __DIR__ . '/../includes/header.php';

$users = getUsers();
$products = getProducts();

$currentUserObj = getUserByEmail($_SESSION['user_email']);
?>