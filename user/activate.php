<?php
// Start the session
session_start();

// Include the database configuration
require_once '../includes/conf.php';

// Initialize variables
$errors = [];
$success = '';

// Check if the token is provided in the URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Prepare and execute the query to check for the token in the database
    $stmt = $conn->prepare('SELECT id, is_verified FROM users WHERE token = ?');
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->store_result();

    // If the token is found
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $is_verified);
        $stmt->fetch();

        // Check if the account is already verified
        if ($is_verified) {
            $success = 'Your account is already verified. You can now log in.';
        } else {
            // Activate the user's account
            $update_stmt = $conn->prepare('UPDATE users SET is_verified = 1 WHERE id = ?');
            $update_stmt->bind_param('i', $user_id);

            if ($update_stmt->execute()) {
                $success = 'Your account has been successfully verified! You can now log in.';
            } else {
                $errors[] = 'Something went wrong. Please try again later.';
            }
            $update_stmt->close();
        }
    } else {
        $errors[] = 'Invalid or expired token. Please check your email and try again.';
    }
    $stmt->close();
} else {
    $errors[] = 'No verification token provided.';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Activation - Online Diary</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Email Verification</h1>

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

        <!-- Redirect user to the login page after verification -->
        <p>Once your account is verified, you can <a href="login.php">log in here</a>.</p>
    </div>
</body>
</html>
