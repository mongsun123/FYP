<?php
session_start();
include 'connection.php'; // Include your database connection file

// Query to get the top players by level
$query = "SELECT u.username, cs.level, cs.experience 
          FROM user u 
          JOIN character_stats cs ON u.id = cs.user_id 
          ORDER BY cs.level DESC, cs.experience DESC 
          LIMIT 10"; // Limit to top 10 players or adjust as needed

$result = $conn->query($query);

$leaderboard = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $leaderboard[] = $row;
    }
}

echo json_encode($leaderboard);

$conn->close();
?>