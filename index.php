<?php
// Start the session
session_start();

// Include database configuration
require_once 'includes/conf.php';

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Redirect to the dashboard
    header('Location: user/dashboard.php');
    exit();
}

// If not logged in, display the homepage
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Diary</title>
   <style>
 
/* General Reset */
body {
    margin: 0;
    padding: 0;
    font-family: 'Arial', sans-serif;
    background: linear-gradient(to bottom, #d4f8e8, #f7fff7); /* Greenish-white gradient */
    color: #333;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Navbar Styling */
.navbar {
    width: 100%;
    background-color: #28a745; /* Green */
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
}

.navbar-brand a {
    font-size: 1.5rem;
    font-weight: bold;
    color: #fff; /* White text for contrast */
    text-decoration: none;
}

.navbar-links {
    display: flex;
    gap: 15px;
}

.nav-button {
    text-decoration: none;
    padding: 8px 15px;
    font-size: 0.9rem;
    color: #fff; /* White text */
    background-color: #155724; /* Darker green */
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.nav-button:hover {
    background-color: #19692c; /* Even darker green */
}

.nav-button.register {
    background-color: #28a745; /* Match navbar green */
}

.nav-button.register:hover {
    background-color: #218838;
}

/* Container Styling */
.container {
    margin-top: 100px; /* Push content below the navbar */
    background: #ffffff; /* White background for contrast */
    padding: 40px; /* Increased padding */
    border-radius: 15px;
    box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.3); /* Enhanced shadow for prominence */
    width: 90%;
    max-width: 800px;
}

/* Content Layout */
.content {
    display: flex;
    align-items: center;
    gap: 20px; /* Space between text and image */
}

.text {
    flex: 2; /* Occupy more space for the text */
}

.image-container {
    flex: 1; /* Occupy less space for the image and buttons */
    text-align: center; /* Center align content */
}

.container-image {
    width: 100%; /* Let the flexbox control size */
    max-width: 300px; /* Limit the maximum width */
    height: auto; /* Maintain aspect ratio */
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2); /* Subtle shadow */
    margin-bottom: 20px; /* Add spacing below the image */
}

/* Button Container */
.button-container {
    display: flex;
    justify-content: center;
    gap: 15px;
}

/* Heading Styling */
h1 {
    font-size: 2.5rem; /* Adjusted size */
    margin-bottom: 20px;
    color: #155724; /* Dark green */
}

/* Paragraph Styling */
p {
    font-size: 1.2rem;
    line-height: 1.8; /* Increased line height for readability */
    color: #333; /* Neutral text color */
}

/* Responsive Design */
@media (max-width: 768px) {
    .content {
        flex-direction: column; /* Stack text and image vertically */
    }

    .container-image {
        max-width: 100%; /* Allow full width for smaller screens */
    }

    .button-container {
        flex-direction: column; /* Stack buttons vertically on smaller screens */
    }
}



   </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-brand">
            <a href="#">Online Diary</a>
        </div>
        <div class="navbar-links">
            <a href="user/login.php" class="nav-button">Login</a>
            <a href="user/register.php" class="nav-button register">Register</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="content">
            <div class="text">
                <h1>Your Personal Diary, Anywhere...</h1>
                <p>
                    Discover the ultimate platform for documenting your life's journey. 
                    Our online diary offers a secure and easy-to-use space to record your thoughts, upload cherished memories, 
                    and revisit them anytime, anywhere. Stay organized, express yourself, and create a legacy that lasts.
                </p>
            </div>
            <div class="image-container">
                <img src="uploads/images/Diary.jpeg" alt="Diary" class="container-image">

                <div class="button-container">
                    <a href="user/login.php" class="nav-button">Write Now</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

