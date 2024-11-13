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
<style>
    .image-grid{
        margin-top: 200px;
    }
    .image-item{
        height: 280px;
    }
</style>


<body style="margin: 0;">
    <div style="margin-left: 14vw;">
        <div class="image-grid">
            <div class="image-item">
                <div class="img-container"><a href="setDate.php"><img class="image" src="../Img/calander_admin.png" alt="Calendar"></a></div>
                <div class="label"><p>Add Schedule</p></div>
            </div>
            <div class="image-item">
                <div class="img-container"><a href="addNotification.php"><img class="image" src="../Img/notification.png" alt="Issue"></a></div>
                <div class="label"><p>Add Notification</p></div>
            </div>
            <div class="image-item">
                <div class="img-container"><a href="adminDashboard.php"><img class="image" src="../Img/return.png" alt="Issue"></a></div>
                <div class="label"><p>Report Issues</p></div>
            </div>
        </div>
    </div>
</body>

</html>