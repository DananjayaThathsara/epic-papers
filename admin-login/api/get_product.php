<?php
require_once __DIR__ . '/../../includes/security_headers.php'; send_security_headers();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';

require_login_api();

$id = $_GET['id'] ?? null;
if (!$id) {
    json_response(['error' => 'id is required'], 422);
}

$db = get_db();

$stmt = $db->prepare('SELECT id, name, description, icon_key, image_path FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    json_response(['error' => 'Product not found'], 404);
}

$sizeStmt = $db->prepare('SELECT label, image_path FROM product_sizes WHERE product_id = ? ORDER BY sort_order, id');
$sizeStmt->execute([$id]);
$product['sizes'] = $sizeStmt->fetchAll();

$colorStmt = $db->prepare('SELECT label, hex, image_path FROM product_colors WHERE product_id = ? ORDER BY sort_order, id');
$colorStmt->execute([$id]);
$product['colors'] = $colorStmt->fetchAll();

json_response($product);
