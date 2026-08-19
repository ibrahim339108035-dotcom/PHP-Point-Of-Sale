<?php
header('Content-Type: application/json; charset=utf-8');

$storageDirectory = dirname(__DIR__) . '/storage';
$productsFile = $storageDirectory . '/products.json';
$defaultProducts = [];

function respond($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if (!is_dir($storageDirectory) && !mkdir($storageDirectory, 0755, true)) respond(['error' => 'Storage folder create nahi ho saka.'], 500);
$products = is_file($productsFile) ? json_decode(file_get_contents($productsFile), true) : $defaultProducts;
if (!is_array($products)) $products = $defaultProducts;

if ($_SERVER['REQUEST_METHOD'] === 'GET') respond(['products' => $products]);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') respond(['error' => 'Method allowed nahi hai.'], 405);
if (!is_writable($storageDirectory)) respond(['error' => 'Storage folder writable nahi hai.'], 500);
$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload) || !isset($payload['products']) || !is_array($payload['products'])) respond(['error' => 'Invalid product data.'], 422);

$cleanProducts = [];
foreach ($payload['products'] as $product) {
    if (!is_array($product) || trim((string) ($product['name'] ?? '')) === '') continue;
    $cleanProducts[] = [
        'id' => (int) ($product['id'] ?? 0),
        'name' => trim((string) $product['name']),
        'category' => trim((string) ($product['category'] ?? 'Grocery')),
        'barcode' => trim((string) ($product['barcode'] ?? '')),
        'icon' => trim((string) ($product['icon'] ?? '📦')),
        'price' => max(0, (float) ($product['price'] ?? 0)),
        'stock' => max(0, (int) ($product['stock'] ?? 0)),
        'unit' => trim((string) ($product['unit'] ?? 'item')) ?: 'item',
    ];
}
if (file_put_contents($productsFile, json_encode($cleanProducts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX) === false) respond(['error' => 'Products save nahi ho sake.'], 500);
respond(['products' => $cleanProducts, 'message' => 'Products save ho gaye.']);
