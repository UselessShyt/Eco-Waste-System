<?php
$filename = basename(__FILE__);
include "header.php";
include "sidebar.php";
include "../SQL_FILE/database.php";

// Start session to get current user's ID
$current_user_id = $_SESSION['User_ID'] ?? null;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pickup History</title>
    <link rel="stylesheet" href="view_History.css">
</head>
<style>
/* Center container and add padding */
.container {
    width: 80%;
    margin: 0 auto;
    padding: 20px;
}

/* Title styling */
h1 {
    text-align: center;
    margin-bottom: 20px;
    font-family: Arial, sans-serif;
    font-size: 2em;
    color: #333;
    font-weight: 600;
}

/* Filter Input styling */
#filterInput {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    font-size: 1em;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    outline: none;
    transition: all 0.3s ease;
}

#filterInput:focus {
    border-color: #007bff;
    box-shadow: 0px 4px 12px rgba(0, 123, 255, 0.2);
}

/* Table container styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    font-family: Arial, sans-serif;
    background-color: #ffffff;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    overflow: hidden;
}

/* Table header styling */
th {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: #ffffff;
    padding: 15px 10px;
    text-align: left;
    font-size: 1em;
    font-weight: bold;
}

/* Table row and cell styling */
td {
    padding: 15px 10px;
    font-size: 1em;
    color: #555;
    border-bottom: 1px solid #f2f2f2;
}

/* Alternate row colors for better readability */
tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:nth-child(odd) {
    background-color: #ffffff;
}

/* Row hover effect */
tr:hover {
    background-color: #e9f3ff;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

/* Optional: Add smooth color transition on rows */
tr {
    transition: background-color 0.2s ease;
}

/* Add some border radius to the table */
table, th, td {
    border: none;
}
</style>

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
                if ($current_user_id)
                {
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

                    // SQL query to fetch data from the users_schedule, schedule, and community tables for the current user
                    $query = "
                        SELECT 
                            schedule.`sch-date` AS sch_date,
                            schedule.`sch-time` AS sch_time,
                            users_schedule.waste_type,
                            users_schedule.sch_quantity,
                            community.Area AS community_name
                        FROM 
                            users_schedule
                        INNER JOIN 
                            schedule ON users_schedule.sch_id = schedule.sch_id
                        INNER JOIN 
                            community ON schedule.com_id = community.com_id
                        WHERE 
                            users_schedule.user_id = ?
                    ";

                    $stmt = $conn->prepare($query);

                    if ($stmt)
                    {
                        // Bind and execute
                        $stmt->bind_param("i", $current_user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0)
                        {
                            // Output data of each row
                            while ($row = $result->fetch_assoc())
                            {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['sch_date']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['sch_time']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['waste_type']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['sch_quantity']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['community_name']) . "</td>";
                                echo "</tr>";
                            }
                        }
                        else
                        {
                            echo "<tr><td colspan='5'>No records found</td></tr>";
                        }

                        // Close the statement
                        $stmt->close();
                    }
                    else
                    {
                        // Display error message for query preparation issues
                        echo "<tr><td colspan='5'>Error in query preparation: " . htmlspecialchars($conn->error) . "</td></tr>";
                    }

                    // Close the database connection
                    $conn->close();
                }
                else
                {
                    // Inform the user to log in if the ID is not detected
                    echo "<tr><td colspan='5'>User ID not detected. Please log in.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="../JavaScript/view_History.js"></script>
</body>

</html>