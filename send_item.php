<?php
include('connection.php'); 
session_start();

$data = json_decode(file_get_contents('php://input'), true);
$senderId = $_SESSION['user_id'];
$recipientId = $data['userId'];
$itemName = $data['itemName'];

// Find the item ID from the item table
$stmt = $conn->prepare("SELECT id FROM item WHERE id = ?");
$stmt->bind_param("s", $itemName);
$stmt->execute();
$stmt->bind_result($item_id);
$stmt->fetch();
$stmt->close();

if ($item_id) {
    // Check if the item exists in the sender's inventory
    $stmt = $conn->prepare("SELECT quantity FROM inventory WHERE user_id = ? AND item_id = ?");
    $stmt->bind_param("ii", $senderId, $item_id);
    $stmt->execute();
    $stmt->bind_result($sender_quantity);
    $stmt->fetch();
    $stmt->close();

    if ($sender_quantity > 0) {
        // Deduct one item from the sender's inventory
        $stmt = $conn->prepare("UPDATE inventory SET quantity = quantity - 1 WHERE user_id = ? AND item_id = ?");
        $stmt->bind_param("ii", $senderId, $item_id);
        $stmt->execute();
        $stmt->close();

        // Add the item to the recipient's inventory
        $stmt = $conn->prepare("SELECT quantity FROM inventory WHERE user_id = ? AND item_id = ?");
        $stmt->bind_param("ii", $recipientId, $item_id);
        $stmt->execute();
        $stmt->bind_result($recipient_quantity);
        $stmt->fetch();
        $stmt->close();

        if ($recipient_quantity > 0) {
            // Update quantity if item already exists in recipient's inventory
            $stmt = $conn->prepare("UPDATE inventory SET quantity = quantity + 1 WHERE user_id = ? AND item_id = ?");
            $stmt->bind_param("ii", $recipientId, $item_id);
        } else {
            // Insert a new row if item does not exist in recipient's inventory
            $stmt = $conn->prepare("INSERT INTO inventory (user_id, item_id, quantity, acquired_at) VALUES (?, ?, 1, NOW())");
            $stmt->bind_param("ii", $recipientId, $item_id);
        }

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Item sent successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to send item']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'You do not have enough of this item']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Item not found']);
}

$conn->close();
?>
