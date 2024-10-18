<?php
include('connection.php'); 
session_start();
$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id']; 
$stmt->close(); 

$stmt = $conn->prepare("
    SELECT i.id, i.item_name, i.item_description, i.item_type, i.item_value, i.item_effect, inv.quantity
    FROM inventory inv
    JOIN item i ON inv.item_id = i.id
    WHERE inv.user_id = ? AND inv.quantity > 0
    ORDER BY i.id
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$items = array();
while($row = $result->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode($items);

$stmt->close();
$conn->close(); 
?>