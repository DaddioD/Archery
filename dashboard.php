<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$db = get_db();
$user = $_SESSION['user'];
$meets = $db['meets'] ?? [];
$roster = $db['roster'] ?? [];

// Find active live meet
$liveMeet = null;
foreach ($meets as $m) {
    if (!empty($m['is_live'])) {
        $liveMeet = $m;
        break;
    }
}

require_once 'header.php';
?>

<div class="container">
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 1.75rem; font-weight: 800; margin: 0; color: #fff;">Welcome back, <?= htmlspecialchars($user['username']) ?></h1>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">
            <?= htmlspecialchars($user['team_name'] ?? 'Archery Team') ?> &bull; <?= htmlspecialchars($user['school'] ?? '') ?>
        </p>
    </div>

    <!-- Quick Stats Bar -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="card" style="margin: 0;">
            <span class="form-label">TOTAL ARCHERS</span>
            <div style="font-size: 2rem; font-weight: 900; color: #fff; margin-top: 0.25rem;"><?= count($roster) ?></div>
        </div>
        <div class="card" style="margin: 0;">
            <span class="form-label">TOTAL MEETS</span>
            <div style="font-size: 2rem; font-weight: 900; color: #fff; margin-top: 0.25rem;"><?= count($meets) ?></div>
        </div>
        <div class="card" style="margin: 0;">
            <span class="form-label">CURRENT STATUS</span>
            <div style="font-size: 1.25rem; font-weight: 800; color: <?= $liveMeet ? 'var(--primary)' : 'var(--text-dim)' ?>; margin-top: 0.5rem;">
                <?= $liveMeet ? '● LIVE MEET ACTIVE' : '○ OFFLINE' ?>
            </div>
        </div>
    </div>

    <!-- Live Meet Card or Call to Action -->
    <div class="card">
        <h2 style="font-size: 1.1rem; font-weight: 800; margin-top: 0; color: #fff;">Active Event Status</h2>
        <?php if ($liveMeet): ?>
            <div class="item-row" style="margin-top: 1rem;">
                <div>
                    <strong style="color: #fff; font-size: 1.1rem; display: block;"><?= htmlspecialchars($liveMeet['name']) ?></strong>
                    <span style="color: var(--text-muted); font-size: 0.85rem;"><?= htmlspecialchars($liveMeet['date']) ?> <?= !empty($liveMeet['location']) ? '&bull; ' . htmlspecialchars($liveMeet['location']) : '' ?></span>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="scoring.php?meet_id=<?= htmlspecialchars($liveMeet['id']) ?>" class="btn-primary" style="padding: 0.55rem 1rem; font-size: 0.85rem; width: auto;">
                        Open Live Scoring
                    </a>
                    <a href="public_board.php" target="_blank" class="btn-secondary">
                        View Board ↗
                    </a>
                </div>
            </div>
        <?php else: ?>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0.5rem 0 1rem 0;">No meet is currently set to live spectator mode.</p>
            <a href="create_meet.php" class="btn-primary" style="display: inline-block; width: auto; padding: 0.6rem 1.2rem;">
                + Create New Meet
            </a>
        <?php endif; ?>
    </div>

    <!-- Meets History -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2 style="font-size: 1.1rem; font-weight: 800; margin: 0; color: #fff;">All Meets</h2>
            <a href="create_meet.php" class="btn-secondary">+ New Meet</a>
        </div>

        <?php if (empty($meets)): ?>
            <p style="color: var(--text-dim); font-size: 0.85rem;">No meets created yet.</p>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <?php foreach (array_reverse($meets) as $m): ?>
                    <div class="item-row">
                        <div>
                            <strong style="color: #fff; font-size: 0.95rem;"><?= htmlspecialchars($m['name']) ?></strong>
                            <span style="color: var(--text-dim); font-size: 0.8rem; display: block;"><?= htmlspecialchars($m['date']) ?></span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <?php if (!empty($m['is_live'])): ?>
                                <span style="color: var(--primary); font-size: 0.75rem; font-weight: 800;">[LIVE]</span>
                            <?php endif; ?>
                            <a href="scoring.php?meet_id=<?= htmlspecialchars($m['id']) ?>" class="btn-secondary">
                                Score Meet
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>