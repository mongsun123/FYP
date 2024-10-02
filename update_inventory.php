<?php
session_start();
error_reporting(E_ALL); 
ini_set('display_errors', 1);
include 'connection.php'; 

$data = json_decode(file_get_contents('php://input'), true);
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