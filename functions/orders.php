<?php

/** Orë për kthim të drejtpërdrejtë nga përdoruesi */
define('ORDER_RETURN_HOURS', 7);

function orderReturnDeadlineTimestamp(string $createdAt): int
{
    return strtotime($createdAt) + (ORDER_RETURN_HOURS * 3600);
}

function canDirectReturnOrder(array $order): bool
{
    if (!in_array($order['status'], ['pending', 'completed'], true)) {
        return false;
    }

    return time() < orderReturnDeadlineTimestamp($order['created_at']);
}

function canRequestAdminReturn(array $order, ?array $pendingRequest): bool
{
    if (!in_array($order['status'], ['pending', 'completed'], true)) {
        return false;
    }

    if ($pendingRequest !== null) {
        return false;
    }

    return time() >= orderReturnDeadlineTimestamp($order['created_at']);
}

function orderReturnTimeLeftLabel(string $createdAt): string
{
    $left = orderReturnDeadlineTimestamp($createdAt) - time();

    if ($left <= 0) {
        return 'Koha për kthim të drejtpërdrejtë ka skaduar.';
    }

    $h = (int) floor($left / 3600);
    $m = (int) floor(($left % 3600) / 60);

    return "Mbeten ~{$h}h {$m}min për kthim të drejtpërdrejtë";
}
