<?php

class ReturnRequestRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create(int $orderId, int $userId, string $reason): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO return_requests (order_id, user_id, reason, status) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$orderId, $userId, $reason, 'pending']);

        return (int) $this->db->lastInsertId();
    }

    public function findPendingByOrder(int $orderId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM return_requests WHERE order_id = ? AND status = 'pending' ORDER BY id DESC LIMIT 1"
        );
        $stmt->execute([$orderId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function findAllPending(): array
    {
        $stmt = $this->db->query(
            "SELECT rr.*, o.total_amount, o.created_at AS order_date,
                    u.name AS user_name, u.email AS user_email
             FROM return_requests rr
             INNER JOIN orders o ON o.id = rr.order_id
             INNER JOIN users u ON u.id = rr.user_id
             WHERE rr.status = 'pending'
             ORDER BY rr.created_at ASC"
        );

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT rr.*, o.total_amount, u.name AS user_name, u.email AS user_email
             FROM return_requests rr
             INNER JOIN orders o ON o.id = rr.order_id
             INNER JOIN users u ON u.id = rr.user_id
             WHERE rr.id = ?'
        );
        $stmt->execute([$id]);

        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function resolve(int $id, string $status, ?string $adminNote = null): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE return_requests SET status = ?, admin_note = ?, resolved_at = NOW() WHERE id = ?'
        );

        return $stmt->execute([$status, $adminNote, $id]);
    }
}
