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

function extract_file_at(array $filesField, int $index): ?array {
    if (!isset($filesField['name'][$index]) || $filesField['name'][$index] === '') return null;
    return [
        'name'     => $filesField['name'][$index],
        'type'     => $filesField['type'][$index],
        'tmp_name' => $filesField['tmp_name'][$index],
        'error'    => $filesField['error'][$index],
        'size'     => $filesField['size'][$index],
    ];
}

$productId = $_POST['id'] ?? null;
$isEdit = !empty($productId);

$name = trim($_POST['name'] ?? '');
$desc = trim($_POST['desc'] ?? '');
$iconKey = $_POST['iconKey'] ?? 'plain';
$allowedIcons = ['plain', 'printed', 'custom'];
if (!in_array($iconKey, $allowedIcons, true)) $iconKey = 'plain';

$sizeLabelsRaw = $_POST['sizeLabel'] ?? [];
$sizeExistingImages = $_POST['sizeExistingImage'] ?? [];
$colorLabelsRaw = $_POST['colorLabel'] ?? [];
$colorHexes = $_POST['colorHex'] ?? [];
$colorExistingImages = $_POST['colorExistingImage'] ?? [];

if ($name === '') {
    json_response(['error' => 'Product name is required.'], 422);
}
$hasAnySize = count(array_filter(array_map('trim', $sizeLabelsRaw), fn($s) => $s !== '')) > 0;
if (!$hasAnySize) {
    json_response(['error' => 'Add at least one size.'], 422);
}

$db = get_db();

try {
    $db->beginTransaction();

    if ($isEdit) {
        // confirm the product exists and grab its current main image
        $stmt = $db->prepare('SELECT image_path FROM products WHERE id = ?');
        $stmt->execute([$productId]);
        $existing = $stmt->fetch();
        if (!$existing) {
            throw new RuntimeException('Product not found.');
        }

        $newMainImage = handle_image_upload($_FILES['mainImage'] ?? null, 'products');
        if ($newMainImage) {
            delete_uploaded_file($existing['image_path']);
            $mainImagePath = $newMainImage;
        } else {
            $mainImagePath = $existing['image_path']; // keep current photo
        }

        $stmt = $db->prepare('UPDATE products SET name = ?, description = ?, icon_key = ?, image_path = ? WHERE id = ?');
        $stmt->execute([$name, $desc, $iconKey, $mainImagePath, $productId]);

        // collect old child-row image paths so we can clean up any that are
        // no longer used, once the new rows are in place
        $oldPaths = [];
        foreach (['product_sizes', 'product_colors'] as $table) {
            $s = $db->prepare("SELECT image_path FROM $table WHERE product_id = ?");
            $s->execute([$productId]);
            foreach ($s->fetchAll() as $r) {
                if ($r['image_path']) $oldPaths[] = $r['image_path'];
            }
        }

        $db->prepare('DELETE FROM product_sizes WHERE product_id = ?')->execute([$productId]);
        $db->prepare('DELETE FROM product_colors WHERE product_id = ?')->execute([$productId]);
    } else {
        $mainImagePath = handle_image_upload($_FILES['mainImage'] ?? null, 'products');

        $stmt = $db->prepare('INSERT INTO products (name, description, icon_key, image_path, sort_order) VALUES (?, ?, ?, ?, (SELECT n FROM (SELECT COALESCE(MAX(sort_order),0)+1 AS n FROM products) t))');
        $stmt->execute([$name, $desc, $iconKey, $mainImagePath]);
        $productId = (int) $db->lastInsertId();
        $oldPaths = [];
    }

    $keptPaths = [];

    $sizeStmt = $db->prepare('INSERT INTO product_sizes (product_id, label, image_path, sort_order) VALUES (?, ?, ?, ?)');
    foreach (array_values($sizeLabelsRaw) as $i => $label) {
        $label = trim($label);
        if ($label === '') continue;
        $newImg = handle_image_upload(extract_file_at($_FILES['sizeImage'] ?? ['name' => []], $i), 'products');
        $imgPath = $newImg ?: (trim($sizeExistingImages[$i] ?? '') ?: null);
        if ($imgPath) $keptPaths[] = $imgPath;
        $sizeStmt->execute([$productId, $label, $imgPath, $i]);
    }

    $colorStmt = $db->prepare('INSERT INTO product_colors (product_id, label, hex, image_path, sort_order) VALUES (?, ?, ?, ?, ?)');
    foreach (array_values($colorLabelsRaw) as $i => $label) {
        $label = trim($label);
        if ($label === '') continue;
        $hex = trim($colorHexes[$i] ?? '') ?: null;
        $newImg = handle_image_upload(extract_file_at($_FILES['colorImage'] ?? ['name' => []], $i), 'products');
        $imgPath = $newImg ?: (trim($colorExistingImages[$i] ?? '') ?: null);
        if ($imgPath) $keptPaths[] = $imgPath;
        $colorStmt->execute([$productId, $label, $hex, $imgPath, $i]);
    }

    $db->commit();

    // now that everything committed, remove any old files that are no longer referenced
    foreach ($oldPaths as $p) {
        if (!in_array($p, $keptPaths, true)) {
            delete_uploaded_file($p);
        }
    }

    json_response(['success' => true, 'id' => (int) $productId]);
} catch (Throwable $e) {
    $db->rollBack();
    json_response(['error' => $e->getMessage()], 422);
}
