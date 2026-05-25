<?php

require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Admin.php';

class UserRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM users ORDER BY id ASC');
        $users = [];

        foreach ($stmt->fetchAll() as $row) {
            $users[] = $this->mapRow($row);
        }

        return $users;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE LOWER(email) = LOWER(?) LIMIT 1');
        $stmt->execute([trim($email)]);
        $row = $stmt->fetch();

        return $row ? $this->mapRow($row) : null;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ? $this->mapRow($row) : null;
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM users WHERE LOWER(email) = LOWER(?)';
        $params = [trim($email)];

        if ($excludeId !== null) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(string $name, string $email, string $role, string $plainPassword): int
    {
        $hash = password_hash($plainPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$name, strtolower(trim($email)), $hash, $role]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $name, string $email, string $role): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?'
        );

        return $stmt->execute([$name, strtolower(trim($email)), $role, $id]);
    }

    public function updatePassword(int $id, string $plainPassword): bool
    {
        $hash = password_hash($plainPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare('UPDATE users SET password = ? WHERE id = ?');

        return $stmt->execute([$hash, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');

        return $stmt->execute([$id]);
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    public function countAdmins(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
    }

    public function findByGoogleId(string $googleId): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE google_id = ? LIMIT 1');
        $stmt->execute([$googleId]);
        $row = $stmt->fetch();

        return $row ? $this->mapRow($row) : null;
    }

    public function linkGoogleId(int $userId, string $googleId): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET google_id = ? WHERE id = ?');

        return $stmt->execute([$googleId, $userId]);
    }

    public function createFromGoogle(string $googleId, string $name, string $email): int
    {
        $hash = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password, role, google_id) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$name, strtolower(trim($email)), $hash, 'customer', $googleId]);

        return (int) $this->db->lastInsertId();
    }

    private function mapRow(array $row): User
    {
        if ($row['role'] === 'admin') {
            return new Admin(
                (int) $row['id'],
                $row['name'],
                $row['email'],
                $row['password']
            );
        }

        return new User(
            (int) $row['id'],
            $row['name'],
            $row['email'],
            $row['role'],
            $row['password']
        );
    }
}
