<?php
// Start the session
session_start();

// --- Define your dummy credentials ---
$correct_username = "admin";
$correct_password = "admin123"; // Super insecure! For testing only.

// Variable to hold an error message
$error_message = "";

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the data from the form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the username and password are correct
    if ($username == $correct_username && $password == $correct_password) {
        
        // --- CREDENTIALS ARE CORRECT ---
        
        // 1. Set the session variable to prove the user is logged in
        $_SESSION['user_id'] = $username; 

        // 2. Redirect to file_list.php
        header("Location: file_list.php");
        exit; // Important to stop the script after a redirect

    } else {
        // Credentials are wrong
        $error_message = "Invalid username or password!";
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
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f7f6;
            display: grid;
            place-items: center;
            min-height: 90vh;
            margin: 0;
        }
        .login-container {
            background: #ffffff;
            padding: 2rem 2.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            width: 300px;
            text-align: center;
        }
        .login-container h2 {
            margin-top: 0;
            color: #333;
        }
        .form-group {
            margin-bottom: 1.25rem;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #555;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box; /* Important for padding to work with 100% width */
        }
        .login-button {
            width: 100%;
            padding: 0.85rem;
            border: none;
            border-radius: 8px;
            background-color: #007bff;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .login-button:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: #d93025;
            font-weight: 500;
            margin-top: 1rem;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Login</h2>
        
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-button">Login</button>

            <?php
            // If there's an error message, display it
            if (!empty($error_message)) {
                echo '<p class="error-message">' . $error_message . '</p>';
            }
            ?>
        </form>
    </div>

</body>
</html>