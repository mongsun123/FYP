<?php
session_start();
error_reporting(E_ALL); // Report all types of errors
ini_set('display_errors', 1); // Display errors on the screen

include 'connection.php'; // Include your database connection

// Read JSON data
$data = json_decode(file_get_contents('php://input'), true);

// Check if data is valid
if (!isset($data['item_id']) || !isset($data['user_id']) || !isset($data['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Missing data fields.']);
    exit;
}

$item_id = $data['item_id'];
$user_id = $data['user_id'];
$quantity = $data['quantity'];

if ($quantity >= 0) {
    $query = "UPDATE inventory SET quantity = ? WHERE user_id = ? AND item_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $quantity, $user_id, $item_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update inventory.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid item quantity.']);
}

$conn->close();
?>
