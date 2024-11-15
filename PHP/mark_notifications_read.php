<?php
session_start();
include "../SQL_FILE/database.php";

$user_id = $_SESSION['User_ID'] ?? null;

if (!$user_id) {
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit;
}

// Mark unread notifications as read
$query = "
    INSERT INTO notification_read (notice_id, user_id)
    SELECT n.notice_id, ?
    FROM notification n
    LEFT JOIN notification_read nr ON n.notice_id = nr.notice_id AND nr.user_id = ?
    WHERE nr.notice_id IS NULL
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();

echo json_encode(["success" => true]);
?>
