<?php
// Start the session
session_start();

// Include database configuration and PHPMailer
require_once '../includes/conf.php';
require_once '../includes/env_loader.php';
require_once '../lib/phpmailer/src/PHPMailer.php';
require_once '../lib/phpmailer/src/SMTP.php';
require_once '../lib/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    loadEnv(__DIR__ . '/../.env');
} catch (Exception $e) {
    die($e->getMessage());
}

// Initialize variables
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate inputs
    if (empty($username)) {
        $errors[] = 'Username is required.';
    }

    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address.';
    }

    if (empty($password)) {
        $errors[] = 'Password is required.';
    }

    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }

    // Proceed if there are no errors
    if (empty($errors)) {
        // Check if the email or username already exists
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? OR username = ?');
        $stmt->bind_param('ss', $email, $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = 'This email or username is already registered.';
        } else {
            // Generate a verification token
            $token = bin2hex(random_bytes(16));
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insert the user into the database
            $stmt = $conn->prepare('INSERT INTO users (username, email, password_hash, token, is_verified) VALUES (?, ?, ?, ?, 0)');
            $stmt->bind_param('ssss', $username, $email, $password_hash, $token);

            if ($stmt->execute()) {
                // Send the verification email
                $mail = new PHPMailer(true);
                $activate = getenv('BASE_URL') . "/user/activate.php?token=$token";

                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = getenv('SMTP_USERNAME');
                    $mail->Password = getenv('SMTP_PASSWORD');
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Email settings
                    $mail->setFrom(getenv('SMTP_USERNAME'), 'Online Diary');
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject = 'Verify Your Email';
                    $mail->Body = "Hi,<br><br>Click the link below to verify your email:<br><br>
                                    <a href='$activate' style='color:blue;'>Activate Account</a><br><br>
                                    If you did not register, please ignore this email.";

                    $mail->send();
                    $success = 'Registration successful! Please check your email to activate your account.';
                } catch (Exception $e) {
                    $errors[] = 'Could not send Activation email. Please try again.';
                }
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
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
    <title>Register - Online Diary</title>
    <style>
       
       /* General Styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

.container {
    width: 400px; 
    margin: 100px auto;
    padding: 30px; 
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

/* Headings */
h1 {
    text-align: center;
    color: #333;
}

/* Form Styles */
form {
    display: flex;
    flex-direction: column;
}

form div {
    margin-bottom: 15px; 
}

label {
    margin-bottom: 5px;
    color: #555;
    font-weight: bold;
    display: block; 
}

input[type="text"],
input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
}

/* Button Styles */
.button {
    padding: 10px;
    background-color: #5cb85c;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    width: 100%;
}

.button:hover {
    background-color: #4cae4c;
}

/* Success and Error Messages */
.success {
    color: #5cb85c;
    text-align: center;
    margin-bottom: 15px;
}

.error-list {
    list-style-type: none;
    padding: 0;
    color: #d9534f;
}

.error-list li {
    margin-bottom: 10px;
}

/* Link Styles */
p {
    text-align: center;
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>
    <div class="container">
        <h1>Register for Online Diary</h1>

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

        <form action="register.php" method="post" enctype="multipart/form-data">
            <div>
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div>
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            <div>
                <button type="submit" class="button">Register</button>
            </div>
        </form>

        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
</body>
</html>

