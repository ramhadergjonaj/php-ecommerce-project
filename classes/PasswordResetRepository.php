<?php

class PasswordResetRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Krijon kod 6-shifror dhe e ruan të hash-uar. Kthen kodin e plain për email.
     */
    public function createCode(int $userId): string
    {
        $this->invalidateAllForUser($userId);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $hash = password_hash($code, PASSWORD_DEFAULT);
        $expires = date('Y-m-d H:i:s', time() + 900); // 15 minuta

        $stmt = $this->db->prepare(
            'INSERT INTO password_reset_tokens (user_id, code_hash, expires_at) VALUES (?, ?, ?)'
        );
        $stmt->execute([$userId, $hash, $expires]);

        return $code;
    }

    public function verifyCode(int $userId, string $code): bool
    {
        $code = preg_replace('/\D/', '', $code);

        if (strlen($code) !== 6) {
            return false;
        }

        $stmt = $this->db->prepare(
            'SELECT * FROM password_reset_tokens
             WHERE user_id = ? AND used_at IS NULL AND expires_at > NOW()
             ORDER BY id DESC LIMIT 5'
        );
        $stmt->execute([$userId]);

        foreach ($stmt->fetchAll() as $row) {
            if (password_verify($code, $row['code_hash'])) {
                $this->markUsed((int) $row['id']);

                return true;
            }
        }

        return false;
    }

    private function markUsed(int $tokenId): void
    {
        $stmt = $this->db->prepare('UPDATE password_reset_tokens SET used_at = NOW() WHERE id = ?');
        $stmt->execute([$tokenId]);
    }

    private function invalidateAllForUser(int $userId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE password_reset_tokens SET used_at = NOW() WHERE user_id = ? AND used_at IS NULL'
        );
        $stmt->execute([$userId]);
    }
}
