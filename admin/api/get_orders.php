<?php
require_once __DIR__ . '/../../includes/security_headers.php';
send_security_headers();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';

require_login_api();

$db = get_db();
$stmt = $db->query('SELECT * FROM orders ORDER BY created_at DESC, id DESC');
$orders = $stmt->fetchAll();

json_response($orders);
