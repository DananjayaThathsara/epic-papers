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

$allowedKeys = ['whatsapp_number', 'phone_1', 'phone_2', 'email', 'address', 'facebook_url'];

$whatsapp = trim($input['whatsapp_number'] ?? '');
if ($whatsapp !== '' && !preg_match('/^\d{6,15}$/', $whatsapp)) {
    json_response(['error' => 'WhatsApp number should be digits only, with country code, no spaces or +.'], 422);
}

$db = get_db();
$stmt = $db->prepare('INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)');

foreach ($allowedKeys as $key) {
    if (array_key_exists($key, $input)) {
        $stmt->execute([$key, trim((string) $input[$key])]);
    }
}

json_response(['success' => true, 'settings' => get_settings()]);
