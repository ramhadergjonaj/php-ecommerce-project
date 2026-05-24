<?php

require_once __DIR__ . '/Product.php';

class ProductRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    private const SELECT_JOIN = '
        SELECT p.*, c.name AS category_name
        FROM products p
        INNER JOIN categories c ON p.category_id = c.id
    ';

    private function mapRow(array $row): Product
    {
        return new Product(
            (int) $row['id'],
            $row['name'],
            (float) $row['price'],
            $row['category_name'],
            $row['image'],
            $row['description'] ?? '',
            (int) $row['category_id']
        );
    }

    public function findAll(): array
    {
        $stmt = $this->db->query(self::SELECT_JOIN . ' ORDER BY p.id ASC');
        $products = [];

        foreach ($stmt->fetchAll() as $row) {
            $products[] = $this->mapRow($row);
        }

        return $products;
    }

    public function findById(int $id): ?Product
    {
        $stmt = $this->db->prepare(self::SELECT_JOIN . ' WHERE p.id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ? $this->mapRow($row) : null;
    }

    public function create(
        string $name,
        float $price,
        int $categoryId,
        string $image,
        string $description
    ): int {
        $stmt = $this->db->prepare(
            'INSERT INTO products (name, price, category_id, image, description)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$name, $price, $categoryId, $image, $description]);

        return (int) $this->db->lastInsertId();
    }

    public function update(
        int $id,
        string $name,
        float $price,
        int $categoryId,
        string $image,
        string $description
    ): bool {
        $stmt = $this->db->prepare(
            'UPDATE products
             SET name = ?, price = ?, category_id = ?, image = ?, description = ?
             WHERE id = ?'
        );

        return $stmt->execute([$name, $price, $categoryId, $image, $description, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = ?');

        return $stmt->execute([$id]);
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM products')->fetchColumn();
    }
}
//Product repository.php