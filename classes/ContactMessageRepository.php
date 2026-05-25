<?php

class ContactMessageRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query(
            'SELECT cm.*, u.name AS account_name
             FROM contact_messages cm
             LEFT JOIN users u ON u.id = cm.user_id
             ORDER BY cm.created_at DESC'
        );

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM contact_messages WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function countNew(): int
    {
        try {
            return (int) $this->db->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn();
        } catch (Throwable $e) {
            return (int) $this->db->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();
        }
    }

    public function saveReply(int $id, string $adminReply): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE contact_messages SET status = 'replied', admin_reply = ?, replied_at = NOW() WHERE id = ?"
        );

        return $stmt->execute([$adminReply, $id]);
    }
}
