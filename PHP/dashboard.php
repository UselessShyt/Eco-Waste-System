<?php
$filename = basename(__FILE__);
include "header.php";
include "sidebar.php";
include "../SQL_FILE/database.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <title>Document</title>
</head>

<body style="margin: 0;">
    <div style="margin-left: 14vw;">
        <div class="image-grid">
            <div class="image-item">
                <img src="../Img/calander.png" alt="Calendar">
                <p>Schedule Pickup</p>
            </div>
            <div class="image-item">
                <img src="../Img/Issue.png" alt="Issue">
                <p>Manage Issues</p>
            </div>
            <div class="image-item">
                <img src="../Img/notification_1.png" alt="Notification">
                <p>Notifications</p>
            </div>
            <div class="image-item">
                <img src="../Img/history.png" alt="History">
                <p>View History</p>
            </div>
            <div class="image-item">
                <img src="../Img/report.png" alt="Report">
                <p>Generate Report</p>
            </div>
            <div class="image-item">
                <img src="../Img/setting_1.png" alt="Settings">
                <p>Settings</p>
            </div>
        </div>
    </div>
</body>

</html>