<?php
include('connection.php'); 
session_start();

$data = json_decode(file_get_contents('php://input'), true);
$search = $data['search'];

// Search for users by username or ID
$stmt = $conn->prepare("SELECT id, username FROM user WHERE username LIKE ? OR id = ?");
$searchTerm = "%$search%";
$stmt->bind_param("si", $searchTerm, $search);
$stmt->execute();
$result = $stmt->get_result();

$users = array();
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);

$stmt->close();
$conn->close();
?>
