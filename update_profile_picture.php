<?php
session_start();
require 'connection.php'; // Database connection file

$response = ['success' => false, 'message' => ''];

// Get user ID from the request
$userId = $_POST['userId'];

// Handle profile picture upload
if (isset($_FILES['profile-upload']) && $_FILES['profile-upload']['error'] === 0) {
    $targetDir = "profilePicture/";
    $fileName = basename($_FILES['profile-upload']['name']);
    $targetFilePath = $targetDir . $fileName;

    // Validate file type (allow only images)
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png'];

    if (in_array($fileType, $allowedTypes)) {
        // Move the uploaded file to the "uploads" directory
        if (move_uploaded_file($_FILES['profile-upload']['tmp_name'], $targetFilePath)) {
            // Update the user's profile picture in the database
            $stmt = $conn->prepare("UPDATE user SET profile_pic = ? WHERE id = ?");
            $stmt->bind_param("si", $fileName, $userId);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Profile picture uploaded successfully!';
            } else {
                $response['message'] = 'Failed to update profile picture in database.';
            }

            $stmt->close();
        } else {
            $response['message'] = 'Failed to upload the file.';
        }
    } else {
        $response['message'] = 'Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.';
    }
} else {
    $response['message'] = 'No file uploaded or an error occurred.';
}

// Return response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
