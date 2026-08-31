<?php
require_once __DIR__ . '/../includes/security_headers.php';
send_security_headers();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers.php';

header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'POST only'], 405);
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    json_response(['error' => 'Invalid JSON payload'], 422);
}

$productId = isset($data['product_id']) ? (int) $data['product_id'] : 0;
$productName = trim((string) ($data['product_name'] ?? ''));
$size = trim((string) ($data['size'] ?? ''));
$color = trim((string) ($data['color'] ?? ''));
$quantity = trim((string) ($data['quantity'] ?? ''));
$customerName = trim((string) ($data['customer_name'] ?? ''));
$phone = trim((string) ($data['phone'] ?? ''));
$location = trim((string) ($data['location'] ?? ''));
$notes = trim((string) ($data['notes'] ?? ''));

if ($productId <= 0 || $productName === '' || $size === '' || $quantity === '' || $customerName === '' || $phone === '' || $location === '') {
    json_response(['error' => 'Missing required order fields'], 422);
}

$db = get_db();
$stmt = $db->prepare('INSERT INTO orders (product_id, product_name, size, color, quantity, customer_name, phone, location, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, "new")');
$stmt->execute([$productId, $productName, $size, $color, $quantity, $customerName, $phone, $location, $notes]);

json_response(['success' => true, 'order_id' => $db->lastInsertId()]);
