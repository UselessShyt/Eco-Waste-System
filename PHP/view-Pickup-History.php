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
    <title>Pickup History</title>
    <link rel="stylesheet" href="view_History.css">
</head>

<body style="margin: 0;">
    <div style="margin-left: 15vw;">
        <h1>Pickup History</h1>

        <!-- Filter Input -->
        <input type="text" id="filterInput" placeholder="Filter by Date, Waste Type, or Community"
            onkeyup="filterTable()">

        <!-- Pickup History Table -->
        <table id="pickupHistoryTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Waste Type</th>
                    <th>Quantity</th>
                    <th>Community</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "ecodatabase";

                // Create database connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error)
                {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Query to fetch data from users_schedule and schedule tables
                // Query to fetch data from the users_schedule and schedule tables with the correct column names
                $query = "
SELECT 
    schedule.Sch_id, 
    schedule.`sch-date` AS sch_date, 
    schedule.`sch-time` AS sch_time, 
    schedule.waste_type, 
    schedule.sch_quantity, 
    community.com_name
FROM 
    users_schedule
INNER JOIN 
    schedule ON users_schedule.schedule_id = schedule.Sch_id
INNER JOIN 
    community ON schedule.com_id = community.com_id";


                $result = $conn->query($query);

                if ($result->num_rows > 0)
                {
                    // Output data of each row
                    while ($row = $result->fetch_assoc())
                    {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['Date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Time']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Waste Type']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Quantity']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Community']) . "</td>";
                        echo "</tr>";
                    }
                }
                else
                {
                    echo "<tr><td colspan='5'>No records found</td></tr>";
                }

                // Close the database connection
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <script src="../JavaScript/view_History.js"></script>
</body>

</html>