<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$db = get_db();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $division = trim($_POST['division'] ?? 'Elementary');

        if ($name) {
            if (!isset($db['roster'])) {
                $db['roster'] = [];
            }
            $db['roster'][] = [
                'id' => 'archer_' . uniqid(),
                'name' => $name,
                'division' => $division
            ];
            save_db($db);
            $message = 'Archer added to roster.';
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        $db['roster'] = array_values(array_filter($db['roster'] ?? [], function($a) use ($id) {
            return $a['id'] !== $id;
        }));
        save_db($db);
        $message = 'Archer removed.';
    }
}

$db = get_db();
$roster = $db['roster'] ?? [];

require_once 'header.php';
?>

<div style="max-width: 800px; margin: 2rem auto; padding: 0 1rem;">
    <div style="background: #141414; border: 1px solid #262626; border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem;">
        <h1 style="font-size: 1.4rem; font-weight: 800; margin-top: 0; color: #fff;">Team Roster Management</h1>

        <?php if ($message): ?>
            <div style="background: rgba(0, 191, 169, 0.15); border: 1px solid #00bfa9; color: #00bfa9; padding: 0.65rem; border-radius: 4px; font-size: 0.85rem; margin-bottom: 1rem;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" style="display: flex; gap: 0.75rem; align-items: flex-end;">
            <input type="hidden" name="action" value="add">
            <div style="flex: 1;">
                <label style="display: block; font-size: 0.75rem; color: #aaa; font-weight: 700; margin-bottom: 0.3rem;">ARCHER FULL NAME</label>
                <input type="text" name="name" required style="width: 100%; padding: 0.6rem; background: #0d0d0d; border: 1px solid #333; color: #fff; border-radius: 4px;">
            </div>
            <div style="width: 200px;">
                <label style="display: block; font-size: 0.75rem; color: #aaa; font-weight: 700; margin-bottom: 0.3rem;">DIVISION</label>
                <select name="division" style="width: 100%; padding: 0.6rem; background: #0d0d0d; border: 1px solid #333; color: #fff; border-radius: 4px;">
                    <option value="Elementary">Elementary</option>
                    <option value="Middle School">Middle School</option>
                    <option value="High School">High School</option>
                </select>
            </div>
            <button type="submit" style="background: #00bfa9; color: #000; font-weight: 800; padding: 0.65rem 1.25rem; border: none; border-radius: 4px; cursor: pointer;">
                Add Archer
            </button>
        </form>
    </div>

    <div style="background: #141414; border: 1px solid #262626; border-radius: 8px; padding: 1.5rem;">
        <h2 style="font-size: 1.1rem; font-weight: 700; margin-top: 0; color: #fff;">Current Roster (<?= count($roster) ?>)</h2>
        <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1rem;">
            <?php if (empty($roster)): ?>
                <p style="color: #666; font-size: 0.85rem;">No archers found in saved roster.</p>
            <?php else: ?>
                <?php foreach ($roster as $archer): ?>
                    <div style="background: #0d0d0d; border: 1px solid #222; padding: 0.75rem 1rem; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="color: #fff; font-size: 0.9rem; display: block;"><?= htmlspecialchars($archer['name']) ?></strong>
                            <span style="color: #666; font-size: 0.75rem;"><?= htmlspecialchars($archer['division']) ?></span>
                        </div>
                        <form method="POST" style="margin: 0;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($archer['id']) ?>">
                            <button type="submit" style="background: transparent; color: #ef4444; border: none; cursor: pointer; font-size: 0.8rem; font-weight: 700;">
                                Remove
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>