<?php
require_once __DIR__ . '/../../includes/security_headers.php';
send_security_headers();
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

$name     = trim($input['name'] ?? '');
$district = trim($input['district'] ?? '');
$phone    = trim($input['phone'] ?? '');
$lat      = $input['lat'] ?? null;
$lng      = $input['lng'] ?? null;
$id       = $input['id'] ?? null;

if ($lat === null || $lng === null || !is_numeric($lat) || !is_numeric($lng)) {
    json_response(['error' => 'lat and lng are required'], 422);
}

$db = get_db();

if ($id) {
    $stmt = $db->prepare('UPDATE distributors SET name = ?, district = ?, phone = ?, lat = ?, lng = ? WHERE id = ?');
    $stmt->execute([$name, $district, $phone, $lat, $lng, $id]);
    json_response(['success' => true, 'id' => (int) $id]);
} else {
    $stmt = $db->prepare('INSERT INTO distributors (name, district, phone, lat, lng) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$name, $district, $phone, $lat, $lng]);
    json_response(['success' => true, 'id' => (int) $db->lastInsertId()]);
}
