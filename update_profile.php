<?php
include('connection.php'); 
session_start();

// Fetch and decode the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

$userId = $data['userId'];
$username = $data['username'];
$oldPassword = $data['oldPassword'];
$newPassword = $data['newPassword'];

// Fetch the current password hash from the database for the user
$stmt = $conn->prepare("SELECT password_hash FROM user WHERE id = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$stmt->bind_result($current_password_hash);
$stmt->fetch();
$stmt->close();

// Check if the old password matches the current password
if (!password_verify($oldPassword, $current_password_hash)) {
    echo json_encode(['status' => 'error', 'message' => 'Old password is incorrect']);
    exit;
}

// Validate the new password with the regex
$password_regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/';
if (!preg_match($password_regex, $newPassword)) {
    echo json_encode(['status' => 'error', 'message' => 'New password does not meet the required criteria']);
    exit;
}

// Hash the new password
$new_password_hash = password_hash($newPassword, PASSWORD_DEFAULT);

// Update the user's username and password in the database
$stmt = $conn->prepare("UPDATE user SET username = ?, password_hash = ? WHERE id = ?");
$stmt->bind_param('ssi', $username, $new_password_hash, $userId);

// Execute the query
if ($stmt->execute()) {
    $_SESSION['username'] = $username;
    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update profile']);
}

$stmt->close();
$conn->close();
?>
