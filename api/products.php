<?php

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/classes/Product.php';

header('Content-Type: application/json; charset=UTF-8');

if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Akses i ndaluar.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$repo = app_repositories()['products'];

try {
    switch ($method) {
        case 'GET':
            $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

            if ($id > 0) {
                $product = $repo->findById($id);

                if (!$product) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'error' => 'Produkti nuk u gjet.']);
                    exit;
                }

                echo json_encode([
                    'success' => true,
                    'data' => productToArray($product),
                ]);
                exit;
            }

            $list = array_map('productToArray', $repo->findAll());
            echo json_encode(['success' => true, 'data' => $list]);
            break;

        case 'POST':
            $input = getJsonInput();
            validateProductInput($input);
            $id = $repo->create(
                $input['name'],
                (float) $input['price'],
                (int) $input['category_id'],
                $input['image'],
                $input['description'] ?? ''
            );
            echo json_encode(['success' => true, 'id' => $id, 'message' => 'Produkti u krijua.']);
            break;

        case 'PUT':
        case 'PATCH':
            $input = getJsonInput();
            $id = (int) ($input['id'] ?? 0);

            if ($id <= 0) {
                throw new InvalidArgumentException('ID e produktit është e pavlefshme.');
            }

            validateProductInput($input);
            $repo->update(
                $id,
                $input['name'],
                (float) $input['price'],
                (int) $input['category_id'],
                $input['image'],
                $input['description'] ?? ''
            );
            echo json_encode(['success' => true, 'message' => 'Produkti u përditësua.']);
            break;

        case 'DELETE':
            $id = (int) ($_GET['id'] ?? 0);

            if ($id <= 0) {
                throw new InvalidArgumentException('ID e produktit është e pavlefshme.');
            }

            $repo->delete($id);
            echo json_encode(['success' => true, 'message' => 'Produkti u fshi (AJAX).']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Metoda nuk lejohet.']);
    }
} catch (InvalidArgumentException $e) {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} catch (Throwable $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Gabim serveri.']);
}

function getJsonInput(): array
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    return is_array($data) ? $data : $_POST;
}

function validateProductInput(array $input): void
{
    if (empty($input['name']) || !validateProductName($input['name'])) {
        throw new InvalidArgumentException('Emri i produktit nuk është valid.');
    }

    if (!isset($input['price']) || !validatePrice((string) $input['price'])) {
        throw new InvalidArgumentException('Çmimi nuk është valid.');
    }

    if (empty($input['category_id']) || (int) $input['category_id'] <= 0) {
        throw new InvalidArgumentException('Kategoria është e detyrueshme.');
    }

    if (empty($input['image'])) {
        throw new InvalidArgumentException('Imazhi/URL është i detyrueshëm.');
    }
}

function productToArray(Product $product): array
{
    return [
        'id' => $product->getId(),
        'name' => $product->getName(),
        'price' => $product->getPrice(),
        'formatted_price' => $product->getFormattedPrice(),
        'category' => $product->getCategory(),
        'category_id' => $product->getCategoryId(),
        'image' => $product->getImage(),
        'description' => $product->getDescription(),
    ];
}
