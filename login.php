<?php
session_start();
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $db = get_db();
    $user = $db['user'] ?? [];

    if ($username === ($user['username'] ?? 'admin') && $password === ($user['password'] ?? 'password123')) {
        $_SESSION['user'] = $user;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid credentials. Default: admin / password123';
    }
}

require_once 'header.php';
?>

<div class="container-sm">
    <div class="card">
        <h1 style="font-size: 1.4rem; font-weight: 800; margin-top: 0; color: #fff;">Sign In</h1>

        <?php if ($error): ?>
            <div class="alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label class="form-label">USERNAME</label>
                <input type="text" name="username" value="admin" required class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">PASSWORD</label>
                <input type="password" name="password" value="password123" required class="form-input">
            </div>
            <button type="submit" class="btn-primary" style="margin-top: 0.5rem;">
                Log In
            </button>
        </form>
    </div>
</div>

</body>
</html>