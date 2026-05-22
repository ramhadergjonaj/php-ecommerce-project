<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../classes/Product.php';

require_once __DIR__ . '/../classes/Admin.php';
require_once __DIR__ . '/../classes/CategoryRepository.php';
require_once __DIR__ . '/../classes/ProductRepository.php';
require_once __DIR__ . '/../classes/UserRepository.php';

function getUsers(): array
{
    return app_repositories()['users']->findAll();
}

function getProducts(): array
{
    return app_repositories()['products']->findAll();
}

function getCategories(): array
{
    return app_repositories()['categories']->findAll();
}

function getUserByEmail(string $email): ?User
{
    return app_repositories()['users']->findByEmail($email);
}

function getProductById(int $id): ?Product
{
    return app_repositories()['products']->findById($id);
}

function sortProductsByPrice(array $products, string $order = 'asc'): array
{
    usort($products, function (Product $a, Product $b) use ($order) {
        if ($order === 'asc') {
            return $a->getPrice() <=> $b->getPrice();
        }

        return $b->getPrice() <=> $a->getPrice();
    });

    return $products;
}

/**
 * Ngarkim i imazhit të produktit.
 */
function handleProductImageUpload(array $file, ?string $currentImage = null): array
{
    $uploadDir = dirname(__DIR__) . '/assets/uploads/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => true, 'path' => $currentImage ?? ''];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Gabim gjatë ngarkimit të skedarit.'];
    }

    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed, true)) {
        return ['success' => false, 'error' => 'Lejohen vetëm imazhe JPG, PNG, WEBP ose GIF.'];
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        return ['success' => false, 'error' => 'Imazhi nuk mund të jetë më i madh se 2MB.'];
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'product_' . uniqid('', true) . '.' . strtolower($ext);
    $destination = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => false, 'error' => 'Skedari nuk u ruajt.'];
    }

    return ['success' => true, 'path' => base_url('assets/uploads/' . $filename)];
}

/**
 * Konvertim USD → EUR (API e jashtme).
 */
function fetchUsdToEurRate(): ?float
{
    $url = 'https://api.frankfurter.app/latest?from=USD&to=EUR';

    try {
        $context = stream_context_create([
            'http' => ['timeout' => 5],
        ]);
        $json = @file_get_contents($url, false, $context);

        if ($json === false) {
            return null;
        }

        $data = json_decode($json, true);

        return isset($data['rates']['EUR']) ? (float) $data['rates']['EUR'] : null;
    } catch (Throwable $e) {
        error_log('Exchange API error: ' . $e->getMessage());

        return null;
    }
}

function convertUsdToEur(float $usd, ?float $rate = null): ?float
{
    $rate = $rate ?? fetchUsdToEurRate();

    return $rate !== null ? round($usd * $rate, 2) : null;
}

function sendContactEmailAndSave(
    string $name,
    string $email,
    ?string $phone,
    string $subject,
    string $message,
    ?int $userId = null
): array
{
    require_once __DIR__ . '/messaging.php';

    try {
        $db = getDB();
        $stmt = $db->prepare(
            "INSERT INTO contact_messages (user_id, name, email, phone, subject, message, status)
             VALUES (?, ?, ?, ?, ?, ?, 'new')"
        );
        $stmt->execute([$userId, $name, $email, $phone, $subject, $message]);
    } catch (Throwable $e) {
        try {
            $stmt = $db->prepare(
                'INSERT INTO contact_messages (user_id, name, email, phone, subject, message)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([$userId, $name, $email, $phone, $subject, $message]);
        } catch (Throwable $e2) {
            error_log($e2->getMessage());

            return ['success' => false, 'error' => 'Mesazhi nuk u ruajt. Provoni përsëri më vonë.'];
        }
    }

    $adminSent = notifyAdminNewContact($name, $email, $subject, $message, $phone);
    $userSent = notifyCustomerContactReceived($email, $name, $subject);

    return [
        'success' => true,
        'mail_sent' => $adminSent,
        'message' => ($adminSent && $userSent)
            ? 'Faleminderit! Mesazhi u pranua dhe do t\'ju përgjigjemi me email.'
            : 'Mesazhi u regjistrua. Do t\'ju kontaktojmë së shpejti.',
    ];
}
function shopName(): string
{
    return 'Tech Store';
}
