<?php
include('connection.php'); 
session_start();

$data = json_decode(file_get_contents('php://input'), true);

$userId = $data['userId'];
$username = $data['username'];
$oldPassword = $data['oldPassword'];
$newPassword = $data['newPassword'];

$stmt = $conn->prepare("SELECT password_hash FROM user WHERE id = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$stmt->bind_result($current_password_hash);
$stmt->fetch();
$stmt->close();

if (!password_verify($oldPassword, $current_password_hash)) {
    echo json_encode(['status' => 'error', 'message' => 'Old password is incorrect']);
    exit;
}

$password_regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/';
if (!preg_match($password_regex, $newPassword)) {
    echo json_encode(['status' => 'error', 'message' => 'New password does not meet the required criteria']);
    exit;
}

$username_regex = '/^[a-zA-Z0-9_-]+$/';
if (!preg_match($username_regex, $username)) {
    echo json_encode(['status' => 'error', 'message' => 'Username can only contain letters, numbers, and underscores']);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM user WHERE username = ? AND id != ?");
$stmt->bind_param('si', $username, $userId);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Username is already taken']);
    $stmt->close();
    exit;
}
$stmt->close();

$new_password_hash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE user SET username = ?, password_hash = ? WHERE id = ?");
$stmt->bind_param('ssi', $username, $new_password_hash, $userId);

if ($stmt->execute()) {
    $_SESSION['username'] = $username;
    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update profile']);
}
$stmt->close();
$conn->close();
?>
