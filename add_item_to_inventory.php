<?php
// Database connection
require 'connection.php';

$data = json_decode(file_get_contents('php://input'), true);

$playerId = $data['playerId'];
$itemName = $data['itemName'];

// Find the item ID from the item table
$stmt = $conn->prepare("SELECT id FROM item WHERE item_name = ?");
$stmt->bind_param("s", $itemName);
$stmt->execute();
$stmt->bind_result($item_id);
$stmt->fetch();
$stmt->close();

if ($item_id) {
    // Check if the item already exists in the inventory
    $stmt = $conn->prepare("SELECT quantity FROM inventory WHERE user_id = ? AND item_id = ?");
    $stmt->bind_param("ii", $playerId, $item_id);
    $stmt->execute();
    $stmt->bind_result($quantity);
    $stmt->fetch();
    $stmt->close();

    if ($quantity < 0) {
        echo '1';
        // If item already exists in the inventory, update quantity
        $stmt = $conn->prepare("UPDATE inventory SET quantity = quantity + 1 WHERE user_id = ? AND item_id = ?");
        $stmt->bind_param("ii", $playerId, $item_id);
    } else {
        echo '2';
        // If item does not exist, insert a new row
        $stmt = $conn->prepare("INSERT INTO inventory (user_id, item_id, quantity, acquired_at) VALUES (?, ?, 1, NOW())");
        $stmt->bind_param("ii", $playerId, $item_id);
    }

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Inventory updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update inventory']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Item not found']);
}
?>
