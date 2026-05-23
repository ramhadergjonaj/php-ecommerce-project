<?php

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/functions/helpers.php';
require_once dirname(__DIR__) . '/functions/orders.php';
require_once dirname(__DIR__) . '/functions/messaging.php';

requireLogin();

if (isAdmin()) {
    redirect_to('pages/dashboard.php');
}

$userId = (int) $_SESSION['user_id'];
$orderId = (int) ($_POST['order_id'] ?? 0);
$action = $_POST['action'] ?? '';

$orderRepo = app_repositories()['orders'];
$returnRepo = app_repositories()['return_requests'];
$order = $orderRepo->findByIdForUser($orderId, $userId);

if (!$order) {
    $_SESSION['flash_error'] = 'Porosia nuk u gjet.';
    redirect_to('pages/my-orders.php');
}

try {
    if ($action === 'direct_return') {
        if (!canDirectReturnOrder($order)) {
            $_SESSION['flash_error'] = 'Kthimi i drejtpërdrejtë nuk lejohet (koha 7h ose statusi).';
        } else {
            $orderRepo->markReturned($orderId);
            sendEmailToCustomer(
                $_SESSION['user_email'],
                $_SESSION['user_name'],
                'Porosia #' . $orderId . ' u kthye',
                "Porosia juaj #$orderId u regjistrua si e kthyer brenda afatit 7-orësh."
            );
            $_SESSION['flash_success'] = 'Porosia #' . $orderId . ' u kthye.';
        }
    } elseif ($action === 'request_return') {
        $reason = cleanInput($_POST['reason'] ?? '');

        if ($reason === '') {
            $_SESSION['flash_error'] = 'Shkruani arsyen e kthimit.';
        } elseif (!canRequestAdminReturn($order, $returnRepo->findPendingByOrder($orderId))) {
            $_SESSION['flash_error'] = 'Kërkesa për kthim nuk lejohet për këtë porosi.';
        } else {
            $returnRepo->create($orderId, $userId, $reason);
            sendAppEmail(
                adminEmail(),
                emailSubject('Kërkesë kthimi #' . $orderId),
                "Klienti " . $_SESSION['user_name'] . " kërkon kthimin e porosisë #$orderId.\n\nArsyeja:\n$reason",
                $_SESSION['user_email']
            );
            $_SESSION['flash_success'] = 'Kërkesa u dërgua te admini. Do të njoftoheni me email.';
        }
    }
} catch (Throwable $e) {
    error_log($e->getMessage());
    $_SESSION['flash_error'] = 'Gabim. Ekzekutoni database/add_returns_and_google.sql';
}

redirect_to('pages/my-orders.php');
