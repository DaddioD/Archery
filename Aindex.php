<?php
session_start();
require_once 'db.php';

// If user is not logged in, send directly to login screen
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$db = get_db();

// If logged in, find the current active/live meet to start scoring immediately
$liveMeetId = null;
if (!empty($db['meets'])) {
    foreach ($db['meets'] as $meet) {
        if (!empty($meet['is_live'])) {
            $liveMeetId = $meet['id'];
            break;
        }
    }
}

// Redirect to live scoring (or create meet if none exist)
if ($liveMeetId) {
    header('Location: scoring.php?meet_id=' . $liveMeetId);
} else if (!empty($db['meets'])) {
    // If no meet is currently live, default to the latest created meet
    $latestMeet = end($db['meets']);
    header('Location: scoring.php?meet_id=' . $latestMeet['id']);
} else {
    // No meets exist yet
    header('Location: create_meet.php');
}
exit;
