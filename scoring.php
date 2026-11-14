<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$db = get_db();
$meetId = $_GET['meet_id'] ?? '';

// If no specific meet_id provided, look for the active live meet
$targetKey = null;
if ($meetId) {
    foreach ($db['meets'] ?? [] as $key => $m) {
        if ($m['id'] === $meetId) {
            $targetKey = $key;
            break;
        }
    }
} else {
    foreach ($db['meets'] ?? [] as $key => $m) {
        if (!empty($m['is_live'])) {
            $targetKey = $key;
            break;
        }
    }
}

if ($targetKey === null && !empty($db['meets'])) {
    // Fallback to the latest meet
    $targetKey = count($db['meets']) - 1;
}

if ($targetKey === null) {
    header('Location: create_meet.php');
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'save_scores';

    if ($action === 'save_scores') {
        $scoresInput = $_POST['scores'] ?? [];
        foreach ($scoresInput as $archerId => $val) {
            $db['meets'][$targetKey]['scores'][$archerId] = intval($val);
        }

        if (save_db($db)) {
            $message = 'Scores saved successfully!';
        } else {
            $error = 'Failed to save scores.';
        }
    } elseif ($action === 'toggle_live') {
        $makeLive = isset($_POST['is_live']);
        
        // Clear all live flags first if setting this one live
        if ($makeLive) {
            foreach ($db['meets'] as $k => $m) {
                $db['meets'][$k]['is_live'] = false;
            }
        }
        $db['meets'][$targetKey]['is_live'] = $makeLive;
        
        save_db($db);
        $message = $makeLive ? 'Meet is now LIVE on spectator board.' : 'Meet is now offline.';
    }
}

// Refresh meet state
$db = get_db();
$meet = $db['meets'][$targetKey];
$archers = $meet['archers'] ?? [];
$scores = $meet['scores'] ?? [];

require_once 'header.php';
?>

<div class="container">

    <div class="card-header">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; margin: 0; color: #fff;"><?= htmlspecialchars($meet['name']) ?></h1>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0.25rem 0 0 0;">
                Date: <?= htmlspecialchars($meet['date']) ?> &bull; Status: 
                <strong style="color: <?= !empty($meet['is_live']) ? 'var(--primary)' : 'var(--danger)' ?>;">
                    <?= !empty($meet['is_live']) ? 'LIVE' : 'OFFLINE' ?>
                </strong>
            </p>
        </div>

        <form method="POST" style="display: flex; align-items: center; gap: 0.5rem;">
            <input type="hidden" name="action" value="toggle_live">
            <label style="font-size: 0.85rem; color: var(--text-muted); cursor: pointer;">
                <input type="checkbox" name="is_live" value="1" class="form-checkbox" <?= !empty($meet['is_live']) ? 'checked' : '' ?> onchange="this.form.submit()">
                Live Spectator Mode
            </label>
        </form>
    </div>

    <?php if ($message): ?>
        <div class="alert-success">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="scoring.php?meet_id=<?= htmlspecialchars($meet['id']) ?>">
        <input type="hidden" name="action" value="save_scores">
        
        <div class="card">
            <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                <?php foreach ($archers as $archer): ?>
                    <?php $currentScore = $scores[$archer['id']] ?? 0; ?>
                    <div class="item-row">
                        <div>
                            <strong style="color: #fff; font-size: 0.95rem; display: block;"><?= htmlspecialchars($archer['name']) ?></strong>
                            <span style="font-size: 0.8rem; color: var(--text-dim);"><?= htmlspecialchars($archer['division'] ?? 'Elementary') ?></span>
                        </div>

                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <label class="form-label" style="margin: 0;">SCORE:</label>
                            <input type="number" name="scores[<?= htmlspecialchars($archer['id']) ?>]" value="<?= intval($currentScore) ?>" min="0" max="300" class="score-input">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="submit" class="btn-primary" style="font-size: 1rem;">
            Save & Publish Scores
        </button>
    </form>

</div>

</body>
</html>