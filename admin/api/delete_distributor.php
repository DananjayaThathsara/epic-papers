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
$stmt = $db->prepare('DELETE FROM distributors WHERE id = ?');
$stmt->execute([$id]);

if ($stmt->rowCount() === 0) {
    json_response(['error' => 'Distributor not found'], 404);
}

json_response(['success' => true]);
