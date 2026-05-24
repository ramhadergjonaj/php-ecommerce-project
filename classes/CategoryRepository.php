<?php

require_once __DIR__ . '/Category.php';

class CategoryRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM categories ORDER BY name ASC');
        $rows = $stmt->fetchAll();
        $categories = [];

        foreach ($rows as $row) {
            $categories[] = new Category(
                (int) $row['id'],
                $row['name'],
                $row['description']
            );
        }

        return $categories;
    }

    public function findById(int $id): ?Category
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new Category((int) $row['id'], $row['name'], $row['description']);
    }

    public function create(string $name, ?string $description): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO categories (name, description) VALUES (?, ?)'
        );
        $stmt->execute([$name, $description]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $name, ?string $description): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE categories SET name = ?, description = ? WHERE id = ?'
        );

        return $stmt->execute([$name, $description, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM categories WHERE id = ?');

        return $stmt->execute([$id]);
    }

    public function countProducts(int $categoryId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
        $stmt->execute([$categoryId]);

        return (int) $stmt->fetchColumn();
    }
}
//CategoryRepository.php