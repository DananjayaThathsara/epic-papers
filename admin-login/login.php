<?php
require_once __DIR__ . '/../includes/security_headers.php';
send_security_headers();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

if (is_logged_in()) {
  header('Location: index.php');
  exit;
}

$error = '';
$lockedOut = login_is_locked_out();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$lockedOut) {
  if (!csrf_valid($_POST['csrf_token'] ?? null)) {
    $error = 'Your session expired. Please try again.';
  } else {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (attempt_login($username, $password)) {
      header('Location: index.php');
      exit;
    }
    $lockedOut = login_is_locked_out();
    $error = $lockedOut
      ? 'Too many failed attempts. Try again in ' . ceil(login_seconds_remaining() / 60) . ' minute(s).'
      : 'Incorrect username or password.';
  }
} elseif ($lockedOut) {
  $error = 'Too many failed attempts. Try again in ' . ceil(login_seconds_remaining() / 60) . ' minute(s).';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login Epic Paper</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../src/css/style.css?v=3">
</head>

<body>

  <div class="login-wrap">
    <div class="login-card">
      <div class="footer-logo" style="margin-bottom:20px;justify-content:center;">
        <svg width="36" height="36" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M24 3 L44 14 V34 L24 45 L4 34 V14 Z" fill="#2e8b47" />
          <path d="M24 3 L44 14 L24 24 L4 14 Z" fill="#37995a" />
          <path d="M24 24 V45 L44 34 V14 Z" fill="#155724" />
        </svg>
        <span class="logo-text"><strong style="color:var(--ink)">EPIC PAPER</strong><span>PACKAGING SOLUTIONS</span></span>
      </div>

      <h1>Admin Login</h1>
      <p>Sign in to manage products and distributor points.</p>

      <?php if ($error): ?>
        <div class="status-msg show err"><?= h($error) ?></div>
      <?php endif; ?>

      <form method="post" novalidate>
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <div class="field">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" required autofocus value="<?= h($_POST['username'] ?? '') ?>" <?= $lockedOut ? 'disabled' : '' ?>>
        </div>
        <div class="field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required <?= $lockedOut ? 'disabled' : '' ?>>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;" <?= $lockedOut ? 'disabled' : '' ?>>Sign In</button>
      </form>
    </div>
  </div>

</body>

</html>