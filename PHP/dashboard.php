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
                <div class="img-container"><a href="schedulePickUp.php"><img class="image" src="../Img/calander.png" alt="Calendar"></a></div>
                <div class="label"><p>Schedule Pickup</p></div>
            </div>
            <div class="image-item">
                <div class="img-container"><a href="Report-Issues.php"><img class="image" src="../Img/Issue.png" alt="Issue"></a></div>
                <div class="label"><p>Report Issues</p></div>
            </div>
            <div class="image-item">
                <div class="img-container"><a href="#notifications"><img class="image" src="../Img/notification_1.png" alt="Notification"></a></div>
                <div class="label"><p>Notifications</p></div>
            </div>
            <div class="image-item">
                <div class="img-container"><a href="#view-history"><img class="image" src="../Img/history.png" alt="History"></a></div>
                <div class="label"><p>View History</p></div>
            </div>
            <div class="image-item">
                <div class="img-container"><a href="#generate-report"><img class="image" src="../Img/report.png" alt="Report"></a></div>
                <div class="label"><p>Generate Report</p></div>
            </div>
            <div class="image-item">
                <div class="img-container"><a href="#Settings"><img class="image" src="../Img/setting_1.png" alt="Settings"></a></div>
                <div class="label"><p>Settings</p></div>
            </div>
        </div>
    </div>

    
</body>

</html>