<?php
require_once __DIR__ . '/../includes/security_headers.php'; send_security_headers();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers.php';

header('Access-Control-Allow-Origin: *');

$db = get_db();
$products = $db->query('SELECT id, name, description, icon_key, image_path FROM products ORDER BY sort_order, id')->fetchAll();

$sizeStmt = $db->prepare('SELECT label, image_path FROM product_sizes WHERE product_id = ? ORDER BY sort_order, id');
$colorStmt = $db->prepare('SELECT label, hex, image_path FROM product_colors WHERE product_id = ? ORDER BY sort_order, id');

foreach ($products as &$p) {
    $sizeStmt->execute([$p['id']]);
    $sizeRows = $sizeStmt->fetchAll();
    $p['sizes'] = array_map(fn($r) => $r['label'], $sizeRows);
    $p['sizeImages'] = array_map(fn($r) => $r['image_path'], $sizeRows);

    $colorStmt->execute([$p['id']]);
    $colorRows = $colorStmt->fetchAll();
    $p['colors'] = array_map(fn($r) => $r['label'], $colorRows);
    $p['colorHex'] = array_map(fn($r) => $r['hex'], $colorRows);
    $p['colorImages'] = array_map(fn($r) => $r['image_path'], $colorRows);
}
unset($p);

json_response($products);
