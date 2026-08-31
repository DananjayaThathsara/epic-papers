<?php
require_once __DIR__ . '/../includes/security_headers.php'; send_security_headers();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers.php';

header('Access-Control-Allow-Origin: *');

$db = get_db();
$rows = $db->query('SELECT id, name, district, phone, lat, lng FROM distributors ORDER BY id')->fetchAll();

foreach ($rows as &$r) {
    $r['lat'] = (float) $r['lat'];
    $r['lng'] = (float) $r['lng'];
}
unset($r);

json_response($rows);
