<?php
require_once __DIR__ . '/../includes/security_headers.php'; send_security_headers();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers.php';

header('Access-Control-Allow-Origin: *');

json_response(get_settings());
