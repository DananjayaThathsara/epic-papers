<?php
require_once __DIR__ . '/../../includes/security_headers.php'; send_security_headers();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';

require_login_api();
require_csrf_api();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'POST only'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

$id = $input['id'] ?? null;
if (!$id) {
    json_response(['error' => 'id is required'], 422);
}

$db = get_db();

// collect image paths so we can remove the files from disk after deleting the rows
$paths = [];
$stmt = $db->prepare('SELECT image_path FROM products WHERE id = ?');
$stmt->execute([$id]);
if ($row = $stmt->fetch()) {
    $paths[] = $row['image_path'];
} else {
    json_response(['error' => 'Product not found'], 404);
}

foreach (['product_sizes', 'product_colors'] as $table) {
    $stmt = $db->prepare("SELECT image_path FROM $table WHERE product_id = ?");
    $stmt->execute([$id]);
    foreach ($stmt->fetchAll() as $r) {
        $paths[] = $r['image_path'];
    }
}

$stmt = $db->prepare('DELETE FROM products WHERE id = ?'); // sizes/colors cascade via FK
$stmt->execute([$id]);

foreach ($paths as $p) {
    delete_uploaded_file($p);
}

json_response(['success' => true]);
