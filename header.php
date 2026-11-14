<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archery Meet Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav>
    <a href="dashboard.php" class="logo">ARCHERY MANAGER</a>
    <div class="nav-links">
        <?php if ($isLoggedIn): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="scoring.php">Live Scoring</a>
            <a href="create_meet.php">Create Meet</a>
            <a href="roster.php">Roster</a>
            <a href="public_board.php" target="_blank" style="color: var(--primary);">Spectator Board ↗</a>
            <a href="logout.php" class="btn-logout">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</nav>