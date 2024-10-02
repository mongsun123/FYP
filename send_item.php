<?php
include('connection.php'); 
session_start();

$data = json_decode(file_get_contents('php://input'), true);
$senderId = $_SESSION['user_id'];
$recipientId = $data['userId'];
$itemName = $data['itemName'];
$otp = $data['otp'] ?? '';
$totp = $data['totp'] ?? '';
$session_otp = (string) $_SESSION['otp'];
if ($otp != $session_otp) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid OTP']);
    exit;
}

$stmt = $conn->prepare("SELECT otp_secret FROM user WHERE id = ?");
$stmt->bind_param("i", $senderId);
$stmt->execute();
$stmt->bind_result($secret);
$stmt->fetch();
$stmt->close();

if (!$secret) {
    echo json_encode(['status' => 'error', 'message' => 'TOTP secret not found']);
    exit;
}

require 'vendor/autoload.php'; 
use Sonata\GoogleAuthenticator\GoogleAuthenticator;
$googleAuthenticator = new GoogleAuthenticator();
if (!$googleAuthenticator->checkCode($secret, $totp)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid TOTP']);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM item WHERE id = ?");
$stmt->bind_param("s", $itemName);
$stmt->execute();
$stmt->bind_result($item_id);
$stmt->fetch();
$stmt->close();

if ($item_id) {
    $stmt = $conn->prepare("SELECT quantity FROM inventory WHERE user_id = ? AND item_id = ?");
    $stmt->bind_param("ii", $senderId, $item_id);
    $stmt->execute();
    $stmt->bind_result($sender_quantity);
    $stmt->fetch();
    $stmt->close();

    if ($sender_quantity > 0) {
        $stmt = $conn->prepare("UPDATE inventory SET quantity = quantity - 1 WHERE user_id = ? AND item_id = ?");
        $stmt->bind_param("ii", $senderId, $item_id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("SELECT quantity FROM inventory WHERE user_id = ? AND item_id = ?");
        $stmt->bind_param("ii", $recipientId, $item_id);
        $stmt->execute();
        $stmt->bind_result($recipient_quantity);
        $stmt->fetch();
        $stmt->close();

        if ($recipient_quantity > 0) {
            $stmt = $conn->prepare("UPDATE inventory SET quantity = quantity + 1 WHERE user_id = ? AND item_id = ?");
            $stmt->bind_param("ii", $recipientId, $item_id);
        } else {
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