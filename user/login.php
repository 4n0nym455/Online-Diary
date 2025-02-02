<?php
// Start the session
session_start();

// Include database configuration
require_once '../includes/conf.php';

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Initialize variables for error messages
$errors = [];

// Process the login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate input
    if (empty($email) || empty($password)) {
        $errors[] = 'Please fill in both fields.';
    } 
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email';

    } else {
        // Query the database for the user
        $stmt = $conn->prepare('SELECT id, password_hash ,is_verified FROM users WHERE email = ? AND is_verified = 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $password_hash, $is_verified);
            $stmt->fetch();

            // Verify the password
            if (password_verify($password, $password_hash)) {
                // Store user information in the session
                $_SESSION['user_id'] = $user_id;
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid password.';
            }
            
            $stmt->close();
        }else{
            $errors[] = 'Account Not Verifed';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 300px;
            margin: 100px auto;
            padding: 20px;
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

        label {
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }

        input[type="email"],
        input[type="password"] {
            padding: 10px;
            margin-bottom: 15px;
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
        <h1>Login</h1>

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

        <form method="post" action="login.php">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
            
            <button type="submit" class="button">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>


</body>
</html>


