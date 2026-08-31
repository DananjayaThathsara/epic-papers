<?php
/* ============================================
   DATABASE CONFIG
   Values are read from the .env file at the project root (see
   .env.example for the template). Falls back to sensible local
   defaults if a key is missing, so the site still runs without one.
   ============================================ */

require_once __DIR__ . '/../includes/env.php';
load_env(__DIR__ . '/../.env');

define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_NAME', env('DB_NAME', 'epic_paper'));
define('DB_USER', env('DB_USER', 'epic_user'));
define('DB_PASS', env('DB_PASS', 'epic_pass123'));
define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));

/**
 * Returns a shared PDO connection (created once per request).
 */
function get_db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Database connection failed. Check your .env file.']);
            exit;
        }
    }
    return $pdo;
}
