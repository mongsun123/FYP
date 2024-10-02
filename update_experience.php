<?php
session_start();
include 'connection.php'; 

$playerId = $_SESSION['user_id']; 
$exp = $_POST['exp'];
$level = $_POST['level'];
$hp = $_POST['hp'];
$attackPower = $_POST['attack_power'];
$defense = $_POST['defense'];

$query = "UPDATE character_stats SET experience = ?, level = ?, health = ?, attack_power = ?, defense = ? WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiiiii", $exp, $level, $hp, $attackPower, $defense, $playerId);
if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update experience and level']);
}
$stmt->close();
$conn->close();
?>