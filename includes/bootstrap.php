<?php

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/shop.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/functions/security.php';
require_once dirname(__DIR__) . '/functions/validators.php';
require_once dirname(__DIR__) . '/classes/Category.php';
require_once dirname(__DIR__) . '/classes/CategoryRepository.php';
require_once dirname(__DIR__) . '/classes/ProductRepository.php';
require_once dirname(__DIR__) . '/classes/UserRepository.php';
require_once dirname(__DIR__) . '/classes/OrderRepository.php';
require_once dirname(__DIR__) . '/classes/PasswordResetRepository.php';
require_once dirname(__DIR__) . '/classes/ReturnRequestRepository.php';
require_once dirname(__DIR__) . '/classes/ContactMessageRepository.php';

function app_repositories(): array
{
    static $repos = null;

    if ($repos === null) {
        $db = getDB();
        $repos = [
            'users' => new UserRepository($db),
            'products' => new ProductRepository($db),
            'categories' => new CategoryRepository($db),
            'orders' => new OrderRepository($db),
            'password_reset' => new PasswordResetRepository($db),
            'return_requests' => new ReturnRequestRepository($db),
            'contact_messages' => new ContactMessageRepository($db),
        ];
    }

    return $repos;
}
