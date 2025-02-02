<?php
session_start();
require_once '../includes/conf.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$entry_id = $_GET['id'] ?? null;

if ($entry_id) {
    // Fetch the entry to get the file paths
    $stmt = $conn->prepare('SELECT * FROM diary_entries WHERE id = ? AND user_id = ?');
    $stmt->bind_param('ii', $entry_id, $user_id);
    $stmt->execute();
    $entry = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($entry) {
        // Delete the related files from the server
        if ($entry['photo_path'] && file_exists($entry['photo_path'])) {
            unlink($entry['photo_path']); // Delete photo
        }

        if ($entry['audio_path'] && file_exists($entry['audio_path'])) {
            unlink($entry['audio_path']); // Delete audio
        }

        // Delete the entry from the database
        $stmt = $conn->prepare('DELETE FROM diary_entries WHERE id = ?');
        $stmt->bind_param('i', $entry_id);
        if ($stmt->execute()) {
            // Successfully deleted
            header('Location: dashboard.php');
            exit;
        } else {
            // Error deleting entry
            echo "Error deleting entry. Please try again.";
        }
    } else {
        echo "Entry not found.";
    }
} else {
    echo "Invalid request.";
}
?>
