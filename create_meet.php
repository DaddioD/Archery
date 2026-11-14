<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$currentUser = $_SESSION['user'];
$db = get_db();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $meetName = trim($_POST['meet_name'] ?? '');
    $meetDate = trim($_POST['meet_date'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $isLive = isset($_POST['is_live']) ? true : false;

    if (!$meetName) {
        $error = 'Meet name is required.';
    } else {
        // If set to live, set all other meets to offline
        if ($isLive && isset($db['meets'])) {
            foreach ($db['meets'] as $idx => $m) {
                $db['meets'][$idx]['is_live'] = false;
            }
        }

        $meetId = 'meet_' . uniqid();
        
        // Populate meet with default team roster archers
        $initialArchers = [];
        $initialScores = [];
        foreach ($db['roster'] ?? [] as $archer) {
            $initialArchers[] = [
                'id' => $archer['id'],
                'name' => $archer['name'],
                'team_name' => $currentUser['team_name'] ?? 'Home Team',
                'division' => $archer['division'] ?? 'Elementary'
            ];
            $initialScores[$archer['id']] = 0;
        }

        $newMeet = [
            'id' => $meetId,
            'name' => $meetName,
            'date' => $meetDate ?: date('Y-m-d'),
            'location' => $location,
            'is_live' => $isLive,
            'created_at' => date('Y-m-d H:i:s'),
            'archers' => $initialArchers,
            'scores' => $initialScores
        ];

        if (!isset($db['meets'])) {
            $db['meets'] = [];
        }

        $db['meets'][] = $newMeet;

        if (save_db($db)) {
            header('Location: scoring.php?meet_id=' . $meetId);
            exit;
        } else {
            $error = 'Failed to save meet to database.';
        }
    }
}

require_once 'header.php';
?>

<div class="container-narrow">
    <div class="card">
        <h1 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 1.5rem; color: #fff;">Create New Meet</h1>

        <?php if ($error): ?>
            <div class="alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="create_meet.php">
            <div class="form-group">
                <label class="form-label">MEET NAME</label>
                <input type="text" name="meet_name" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label">DATE</label>
                <input type="date" name="meet_date" class="form-input" value="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group">
                <label class="form-label">LOCATION</label>
                <input type="text" name="location" class="form-input">
            </div>

            <div class="form-checkbox-group">
                <input type="checkbox" name="is_live" id="is_live" value="1" class="form-checkbox" checked>
                <label for="is_live" style="font-size: 0.9rem; color: #fff; cursor: pointer;">Set as Active Live Meet</label>
            </div>

            <button type="submit" class="btn-primary" style="margin-top: 1rem;">
                Create Meet & Open Scoring
            </button>
        </form>
    </div>
</div>

</body>
</html>