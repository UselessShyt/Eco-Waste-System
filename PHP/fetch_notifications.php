<?php
session_start();
include "../SQL_FILE/database.php";

$user_id = $_SESSION['User_ID'] ?? null;

if (!$user_id) {
    echo json_encode(["notifications" => [], "error" => "User not logged in"]);
    exit;
}

// Corrected query to fetch only unread notifications for the logged-in user
$query = "
    SELECT n.notice_id, n.title, n.message
    FROM notification n
    LEFT JOIN notification_read nr ON n.notice_id = nr.notice_id AND nr.user_id = ?
    WHERE nr.notice_id IS NULL
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = [
        "id" => $row['notice_id'],
        "title" => $row['title'],
        "message" => $row['message']
    ];
}

echo json_encode(["notifications" => $notifications]);
?>
