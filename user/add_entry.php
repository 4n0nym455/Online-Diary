<?php
// Start session and include database configuration
session_start();
require_once '../includes/conf.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = '';

// Define directory paths
$imageDir = '../uploads/images/';
$audioDir = '../uploads/audio/';

// Function to create directory and set permissions
function createDirectory($dirPath) {
    if (!file_exists($dirPath)) {
        // Create directory with 0777 permission and set to allow file writing
        mkdir($dirPath, 0777, true);
    }

    // Ensure the directory is writable
    if (!is_writable($dirPath)) {
        chmod($dirPath, 0777);
    }
}

// Create directories if they don't exist
createDirectory($imageDir);
createDirectory($audioDir);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $photo_path = null;
    $audio_path = null;

    // Handle uploaded photo
    if (!empty($_FILES['photo']['name'])) {
        $photo_path = $imageDir . uniqid() . '-' . $_FILES['photo']['name'];
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
            $errors[] = 'Error uploading photo. Please try again.';
        }
    }

    // Handle recorded audio
    if (!empty($_POST['recorded_audio'])) {
        $audio_data = $_POST['recorded_audio'];

        // Decode Base64 audio data
        list($type, $audio_data) = explode(';', $audio_data);
        list(, $audio_data) = explode(',', $audio_data);
        $audio_data = base64_decode($audio_data);
        
        // Generate unique file name for audio
        $audio_path = $audioDir . uniqid() . '.webm';

        if (!file_put_contents($audio_path, $audio_data)) {
            $errors[] = 'Error uploading audio. Please try again.';
        }
    }

    // Validate inputs
    if (empty($title)) {
        $errors[] = 'Title is required.';
    }

    if (empty($errors)) {
        // Insert entry into the database
        $stmt = $conn->prepare('INSERT INTO diary_entries (user_id, title, content, photo_path, audio_path) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('issss', $user_id, $title, $content, $photo_path, $audio_path);

        if ($stmt->execute()) {
            $success = 'Diary entry added successfully!';
        } else {
            $errors[] = 'Failed to add diary entry. Please try again.';
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Entry - Online Diary</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <script src="../public/js/add.js" defer></script>
</head>
<body>
    <div class="container">
        <h1>Add New Entry</h1>
        <p><a href="dashboard.php">Back to Dashboard</a></p>

        <!-- Display success or error messages -->
        <?php if (!empty($success)): ?>
            <p class="success"><?= htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <ul class="error-list">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form action="add_entry.php" method="post" enctype="multipart/form-data">
            <div>
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" required>
            </div>
            <div>
                <label for="content">Content:</label>
                <textarea name="content" id="content" rows="5" required></textarea>
            </div>
            <div>
                <label for="photo">Photo (optional):</label>
                <input type="file" name="photo" id="photo" accept="image/*">
            </div>
            <div>
                <label>Record Audio:</label>
                <div id="audio-controls">
                    <button type="button" id="start-recording">Start Recording</button>
                    <button type="button" id="stop-recording" disabled>Stop Recording</button>
                </div>
                <audio id="audio-preview" controls style="display: none;"></audio>
                <input type="hidden" name="recorded_audio" id="recorded-audio">
            </div>
            <div>
                <button type="submit" class="button">Add Entry</button>
            </div>
        </form>
    </div>
</body>    
</html>
