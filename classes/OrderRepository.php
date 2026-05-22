<?php

class OrderRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * @param array<int, array{product_id: int, quantity: int, unit_price: float}> $items
     */
    public function createOrder(int $userId, array $items): int
    {
        $total = 0.0;

        foreach ($items as $item) {
            $total += $item['unit_price'] * $item['quantity'];
        }

        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO orders (user_id, status, total_amount) VALUES (?, ?, ?)'
            );
            $stmt->execute([$userId, 'pending', $total]);
            $orderId = (int) $this->db->lastInsertId();

            $itemStmt = $this->db->prepare(
                'INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)'
            );

            foreach ($items as $item) {
                $itemStmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['unit_price'],
                ]);
            }

            $this->db->commit();

            return $orderId;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, COUNT(oi.id) AS item_count
             FROM orders o
             LEFT JOIN order_items oi ON oi.order_id = o.id
             WHERE o.user_id = ?
             GROUP BY o.id
             ORDER BY o.created_at DESC'
        );
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    public function findByIdForUser(int $orderId, int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ?');
        $stmt->execute([$orderId, $userId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function findAllWithUser(): array
    {
        $stmt = $this->db->query(
            'SELECT o.*, u.name AS user_name, u.email AS user_email,
                    COUNT(oi.id) AS item_count
             FROM orders o
             INNER JOIN users u ON u.id = o.user_id
             LEFT JOIN order_items oi ON oi.order_id = o.id
             GROUP BY o.id
             ORDER BY o.created_at DESC'
        );

        return $stmt->fetchAll();
    }

    public function getOrderItems(int $orderId): array
    {
        $stmt = $this->db->prepare(
            'SELECT oi.*, p.name AS product_name
             FROM order_items oi
             INNER JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = ?'
        );
        $stmt->execute([$orderId]);

        return $stmt->fetchAll();
    }

    public function updateStatus(int $orderId, string $status): bool
    {
        $allowed = ['pending', 'completed', 'cancelled', 'returned'];

        if (!in_array($status, $allowed, true)) {
            return false;
        }

        $stmt = $this->db->prepare('UPDATE orders SET status = ? WHERE id = ?');

        return $stmt->execute([$status, $orderId]);
    }

    public function markReturned(int $orderId): bool
    {
        return $this->updateStatus($orderId, 'returned');
    }
}
