<?php
require_once 'db.php';

$db = get_db();

// Search strictly for a meet that is set to live
$liveMeet = null;
foreach ($db['meets'] ?? [] as $m) {
    if (!empty($m['is_live'])) {
        $liveMeet = $m;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Spectator Leaderboard</title>
    <meta http-equiv="refresh" content="10">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <?php if (!$liveMeet): ?>
        <div class="card offline-box">
            <div class="offline-title">EVENT NOT LIVE</div>
            <div class="offline-subtitle">There is currently no meet being actively scored. Please check back later.</div>
        </div>
    <?php else: ?>
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-subtle); padding-bottom: 1rem; margin-bottom: 1rem;">
                <div>
                    <h1 style="margin: 0; font-size: 1.75rem; font-weight: 800; color: #fff;"><?= htmlspecialchars($liveMeet['name']) ?></h1>
                    <span style="color: var(--text-dim); font-size: 0.85rem;"><?= htmlspecialchars($liveMeet['date']) ?> <?= !empty($liveMeet['location']) ? '&bull; ' . htmlspecialchars($liveMeet['location']) : '' ?></span>
                </div>
                <div class="live-indicator">
                    &bull; LIVE SCORING
                </div>
            </div>

            <?php
            $archers = $liveMeet['archers'] ?? [];
            $scores = $liveMeet['scores'] ?? [];

            // Sort archers descending by score
            usort($archers, function($a, $b) use ($scores) {
                $scoreA = $scores[$a['id']] ?? 0;
                $scoreB = $scores[$b['id']] ?? 0;
                return $scoreB <=> $scoreA;
            });
            ?>

            <table class="leaderboard-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">Rank</th>
                        <th>Archer</th>
                        <th>Team</th>
                        <th style="text-align: right;">Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($archers)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-dim); padding: 2rem;">No scores registered yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php $rank = 1; ?>
                        <?php foreach ($archers as $archer): ?>
                            <tr>
                                <td style="color: var(--text-dim); font-weight: 700;"><?= $rank++ ?></td>
                                <td style="font-weight: 700; color: #fff;"><?= htmlspecialchars($archer['name']) ?></td>
                                <td style="color: var(--text-muted);"><?= htmlspecialchars($archer['team_name'] ?? 'Home') ?></td>
                                <td style="text-align: right;" class="score-badge"><?= intval($scores[$archer['id']] ?? 0) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

</body>
</html>