<?php
session_start();
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if the username or email already exists
        $stmt = $conn->prepare("SELECT id FROM user WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username or email already taken!";
        } else {
            // Hash the password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user into the database
            $stmt = $conn->prepare("INSERT INTO user (username, email, password_hash) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password_hash);

            if ($stmt->execute()) {
                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                $error = "Something went wrong. Please try again.";
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
    <title>Register</title>
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
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1 class="game-title">Register</h1>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?= $error ?></p>
            <?php elseif (isset($success)): ?>
                <p style="color: green;"><?= $success ?></p>
            <?php endif; ?>
            <form class="login-form" method="POST" action="register.php">
                <input type="text" name="username" placeholder="Username" class="login-input" required>
                <input type="email" name="email" placeholder="Email" class="login-input" required>
                <input type="password" name="password" placeholder="Password" class="login-input" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" class="login-input" required>
                <button type="submit" class="login-button">Register</button>
            </form>
            <div class="login-links">
                <a href="login.php">Already have an account? Login here</a>
            </div>
        </div>
    </div>
</body>
</html>
