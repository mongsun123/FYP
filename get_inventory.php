<?php
include('connection.php'); 
session_start();
$username = $_SESSION['username'];

// Securely fetch the user ID from the database
$stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id']; // Fetch the user ID

$stmt->close(); // Close the statement

// Securely fetch inventory items for the user
$stmt = $conn->prepare("SELECT i.item_name, i.item_description, i.item_type, i.item_value, i.item_effect, inv.quantity, inv.item_id,
                        CASE 
                            WHEN i.id BETWEEN 1 AND 7 THEN 1 
                            WHEN i.id BETWEEN 8 AND 14 THEN 2 
                            WHEN i.id BETWEEN 15 AND 21 THEN 3 
                            WHEN i.id BETWEEN 22 AND 28 THEN 4 
                            ELSE 0 -- For items outside the specified range
                        END AS item_level
                        FROM inventory inv
                        JOIN item i ON inv.item_id = i.id
                        WHERE inv.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$items = array();
while($row = $result->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode($items);

$stmt->close(); // Close the statement
$conn->close(); // Close the connection
?>
