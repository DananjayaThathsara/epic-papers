<?php
/* ============================================
   HELPERS JSON responses, escaping, image uploads
   ============================================ */

function json_response($data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

/**
 * Handles one uploaded image (from $_FILES['fieldName']).
 * Returns the stored path relative to the project root (e.g.
 * "uploads/products/ab12cd34.jpg"), or null if no file was sent.
 * Throws RuntimeException on an invalid or oversized file.
 */
function handle_image_upload(?array $file, string $subdir = 'products'): ?string
{
    if (!$file || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Image upload failed (error code ' . $file['error'] . ').');
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('Image must be smaller than 5MB.');
    }

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $mime = mime_content_type($file['tmp_name']);
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Only JPG, PNG, WEBP or GIF images are allowed.');
    }

    $destDir = __DIR__ . '/../uploads/' . $subdir;
    if (!is_dir($destDir) && !mkdir($destDir, 0755, true) && !is_dir($destDir)) {
        throw new RuntimeException('Could not create the uploads folder.');
    }

    $filename = bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
    $destPath = $destDir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        throw new RuntimeException('Could not save the uploaded file.');
    }

    return 'uploads/' . $subdir . '/' . $filename;
}

/** Deletes a previously uploaded file given its project-relative path. */
function delete_uploaded_file(?string $relativePath): void
{
    if (!$relativePath) return;
    $full = __DIR__ . '/../' . $relativePath;
    if (is_file($full)) {
        @unlink($full);
    }
}

/** Reads all site settings (contact details, WhatsApp number) as an associative array,
    with sensible defaults if the settings table is empty or a key is missing. */
function get_settings(): array
{
    $defaults = [
        'whatsapp_number' => '94771234567',
        'phone_1'         => '',
        'phone_2'         => '',
        'email'           => '',
        'address'         => '',
        'facebook_url'    => '',
    ];
    try {
        $rows = get_db()->query('SELECT `key`, `value` FROM settings')->fetchAll();
        foreach ($rows as $r) {
            $defaults[$r['key']] = $r['value'];
        }
    } catch (Throwable $e) {
        // settings table missing/unreachable fall back to defaults rather than breaking the page
    }
    return $defaults;
}
