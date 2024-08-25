<?php
session_start();
include('connection.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //var_dump($_POST);
    //var_dump($_SESSION);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $otp_input = $_POST['otp_code']; // OTP input from user

    $otp_input = (string) $otp_input;
    $session_otp = (string) $_SESSION['otp'];
    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif ($otp_input !== $session_otp) {
        $error = "Invalid OTP!";
    }else {
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
                // Clear OTP from session after successful registration
                unset($_SESSION['otp']);
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
        /*    background-image: url('your-background-image.jpg'); */
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
                <input type="email" id="email" name="email" placeholder="Email" class="login-input" required>
                <!-- Container for OTP field and button -->
                <div style="display: flex; align-items: center; margin-bottom: 20px;">
                    <input type="text" name="otp_code" placeholder="Enter OTP" class="login-input" style="flex: 1; margin-right: 10px;" required>
                    <button type="button" id="send-otp-button" class="login-button" style="width: auto; padding: 10px 15px;" disabled>Send OTP</button>
                </div>
                <input type="password" name="password" placeholder="Password" class="login-input" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" class="login-input" required>
                <!-- New field for the email authentication code -->
                <!--<input type="text" name="otp_code" placeholder="Email OTP" class="login-input" required>-->
                <button type="submit" class="login-button">Register</button>
            </form>
            <div class="login-links">
                <a href="login.php">Already have an account? Login here</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
</body>
</html>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const emailInput = document.getElementById('email');
        const sendOtpButton = document.getElementById('send-otp-button');
        const otpStatus = document.getElementById('otp-status');
        emailInput.addEventListener('input', function() {
            if (emailInput.value.trim() !== '') {
                sendOtpButton.disabled = false;
            } else {
                sendOtpButton.disabled = true;
            }
        });
        sendOtpButton.addEventListener('click', function() {
            const email = emailInput.value.trim();
            
            // Show the SweetAlert with a progress bar
            Swal.fire({
                title: 'Sending OTP...',
                html: 'Please wait while we send your OTP.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                willClose: () => {
                    Swal.hideLoading();
                }
            });
            // Send AJAX request to the server
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'send_otp.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'OTP sent successfully!',
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to send OTP. Please try again.',
                        });
                    }
                }
            };
        
            xhr.onerror = function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again later.',
                });
            };
        
            xhr.send(`email=${encodeURIComponent(email)}`);
        });
    });
</script>
