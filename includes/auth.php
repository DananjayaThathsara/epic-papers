<?php
/* ============================================
   AUTH session-based admin login, CSRF protection,
   and basic brute-force throttling.
   ============================================ */

require_once __DIR__ . '/../config/db.php';

function auth_start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

function is_logged_in(): bool
{
    auth_start_session();
    return !empty($_SESSION['admin_id']);
}

/** For normal admin pages redirects to the login page if not signed in. */
function require_login(): void
{
    auth_start_session();
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

/** For admin API endpoints called via fetch() returns JSON 401 instead of redirecting. */
function require_login_api(): void
{
    auth_start_session();
    if (empty($_SESSION['admin_id'])) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Not signed in. Please log in again.']);
        exit;
    }
}

/* ---------- CSRF protection ---------- */

/** Returns the current session's CSRF token, generating one if needed. */
function csrf_token(): string
{
    auth_start_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Checks a submitted token (form field or X-CSRF-Token header) against the session's. */
function csrf_valid(?string $submitted): bool
{
    auth_start_session();
    if (empty($_SESSION['csrf_token']) || empty($submitted)) return false;
    return hash_equals($_SESSION['csrf_token'], $submitted);
}

/** For admin API endpoints rejects the request with 403 if the CSRF token is missing/wrong. */
function require_csrf_api(): void
{
    $submitted = $_POST['csrf_token']
        ?? $_SERVER['HTTP_X_CSRF_TOKEN']
        ?? null;

    if (!$submitted) {
        // also allow a JSON body { csrf_token: "..." } for endpoints that post raw JSON
        $raw = json_decode(file_get_contents('php://input'), true);
        $submitted = $raw['csrf_token'] ?? null;
    }

    if (!csrf_valid($submitted)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Security check failed (invalid or expired form). Please refresh the page and try again.']);
        exit;
    }
}

/* ---------- Basic brute-force throttling ---------- */

const LOGIN_MAX_ATTEMPTS = 5;
const LOGIN_LOCKOUT_SECONDS = 300; // 5 minutes

function login_is_locked_out(): bool
{
    auth_start_session();
    $until = $_SESSION['login_locked_until'] ?? 0;
    return $until > time();
}

function login_seconds_remaining(): int
{
    auth_start_session();
    return max(0, ($_SESSION['login_locked_until'] ?? 0) - time());
}

function login_register_failure(): void
{
    auth_start_session();
    $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
    if ($_SESSION['login_attempts'] >= LOGIN_MAX_ATTEMPTS) {
        $_SESSION['login_locked_until'] = time() + LOGIN_LOCKOUT_SECONDS;
        $_SESSION['login_attempts'] = 0;
    }
}

function login_reset_attempts(): void
{
    auth_start_session();
    unset($_SESSION['login_attempts'], $_SESSION['login_locked_until']);
}

function attempt_login(string $username, string $password): bool
{
    auth_start_session();
    $stmt = get_db()->prepare('SELECT id, password_hash FROM admins WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $row = $stmt->fetch();

    if ($row && password_verify($password, $row['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $row['id'];
        $_SESSION['admin_username'] = $username;
        login_reset_attempts();
        return true;
    }
    login_register_failure();
    return false;
}

function current_admin_username(): ?string
{
    auth_start_session();
    return $_SESSION['admin_username'] ?? null;
}

function do_logout(): void
{
    auth_start_session();
    $_SESSION = [];
    session_destroy();
}
