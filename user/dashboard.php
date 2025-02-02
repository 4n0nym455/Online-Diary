<?php
session_start();
require_once '../includes/conf.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$entries = [];

// Get all diary entries for the logged-in user
$stmt = $conn->prepare('SELECT * FROM diary_entries WHERE user_id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $entries[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Online Diary</title>
    <style>
        /* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

.dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Header Styles */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #5cb85c;
    padding: 10px 20px;
    border-radius: 8px;
    color: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.header h1 {
    margin: 0;
    font-size: 24px;
}

.add-entry-btn, .logout-button {
    text-decoration: none;
    color: white;
    background-color: #4cae4c;
    padding: 8px 15px;
    border-radius: 5px;
    font-size: 14px;
    font-weight: bold;
    transition: background-color 0.3s;
}

.add-entry-btn:hover, .logout-button:hover {
    background-color: #3a8e3a;
}

/* Entries Container */
.entries-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

/* Entry Card Styles */
.entry-card {
    background-color: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.entry-title {
    margin: 0 0 10px;
    color: #333;
    font-size: 18px;
    font-weight: bold;
}

.entry-content {
    margin-bottom: 10px;
    color: #555;
    line-height: 1.6;
}

.entry-photo img {
    max-width: 100%;
    border-radius: 8px;
    margin-top: 10px;
}

.entry-audio {
    margin-top: 10px;
}

.entry-actions {
    margin-top: 15px;
}

.delete-btn {
    text-decoration: none;
    color: white;
    background-color: #d9534f;
    padding: 8px 15px;
    border-radius: 5px;
    font-size: 14px;
    font-weight: bold;
    transition: background-color 0.3s;
}

.delete-btn:hover {
    background-color: #c9302c;
}

/* Media Queries for Responsiveness */
@media (max-width: 600px) {
    .header {
        flex-direction: column;
        align-items: flex-start;
    }

    .add-entry-btn, .logout-button {
        margin-top: 10px;
    }
}

    </style>
    
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>Welcome to Your Dashboard</h1>
            <a href="add_entry.php" class="add-entry-btn">Add New Entry</a>
            <a href="/logout.php" class="logout-button">Logout</a>
        </div>

        <div class="entries-container">
            <!-- Display entries -->
            <?php if (!empty($entries)): ?>
                <?php foreach ($entries as $entry): ?>
                    <div class="entry-card">
                        <h2 class="entry-title"><?= htmlspecialchars($entry['title']); ?></h2>
                        <p class="entry-content"><?= nl2br(htmlspecialchars($entry['content'])); ?></p>

                        <?php if ($entry['photo_path']): ?>
                            <div class="entry-photo">
                                <img src="<?= $entry['photo_path']; ?>" alt="Entry Photo">
                            </div>
                        <?php endif; ?>

                        <?php if ($entry['audio_path']): ?>
                            <div class="entry-audio">
                                <audio controls>
                                    <source src="<?= $entry['audio_path']; ?>" type="audio/webm">
                                    Your browser does not support the audio element.
                                </audio>
                            </div>
                        <?php endif; ?>

                        <div class="entry-actions">
                            <a href="delete_entry.php?id=<?= $entry['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this entry?')">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No entries found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
