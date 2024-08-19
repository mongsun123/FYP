<?php
session_start();
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prevent SQL Injection
    $username = $conn->real_escape_string($username);

    // Retrieve the user from the database
    $sql = "SELECT * FROM user WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify the password
        if (password_verify($password, $user['password_hash'])) {
            // Password is correct, create a session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Redirect to the game or dashboard page
            header("Location: index.php");
            exit();
        } else {
            // Incorrect password
            $error = "Invalid username or password!";
        }
    } else {
        // User not found
        $error = "Invalid username or password!";
    }
}
?>

<style>
body, html {
    margin: 0;
    padding: 0;
    height: 100%;
    font-family: 'Exo', sans-serif;
    color: #fff;
    background-color: #2a2a2a; /* Dark background color */
}

.login-container {
    background-image: url('your-background-image.jpg');
    background-color: rgba(42, 42, 42, 0.8); /* Fallback color and slight overlay */
    background-blend-mode: overlay; /* Blend the color with the image */
    background-size: cover;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.login-box {
    background: rgba(0, 0, 0, 0.8); /* Semi-transparent background with color */
    padding: 40px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0px 0px 20px 5px rgba(0,0,0,0.5);
}

.game-title {
    font-size: 2.5em;
    margin-bottom: 20px;
    text-shadow: 0px 0px 10px #ff6f00;
}

.login-form .login-input {
    display: block;
    width: 100%;
    margin-bottom: 20px;
    padding: 10px;
    font-size: 1em;
    border: 2px solid #ff6f00;
    border-radius: 5px;
    background: #333;
    color: #fff;
    outline: none;
}

.login-button {
    width: 100%;
    padding: 10px;
    font-size: 1.2em;
    border: none;
    border-radius: 5px;
    background: linear-gradient(45deg, #ff6f00, #ff8e53);
    color: #fff;
    cursor: pointer;
    transition: 0.3s;
    box-shadow: 0px 0px 10px 2px #ff6f00;
}

.login-button:hover {
    box-shadow: 0px 0px 20px 5px #ff8e53;
}

.login-links a {
    color: #ff6f00;
    text-decoration: none;
    margin: 0 10px;
}

.login-links a:hover {
    text-decoration: underline;
}

</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        /* Reuse your existing CSS for styling */
        <?php include 'styles.css'; ?>
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1 class="game-title">Battle v1.0</h1>
            <form class="login-form" method="POST">
                <input type="text" name="username" placeholder="Username" class="login-input">
                <input type="password" name="password" placeholder="Password" class="login-input">
                <button type="submit" class="login-button">Login</button>
            </form>
            <div class="login-links">
                <a href="#">Forgot Password?</a> | <a href="register.php">Create Account</a>
            </div>
        </div>
    </div>
</body>
