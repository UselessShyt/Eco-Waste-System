<?php

    // Enable error reporting for debugging
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $filename = basename(__FILE__);
    include "header.php";
    include "sidebar.php";
    include "../SQL_FILE/database.php";

    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        /*
        // Debug: Print the incoming POST data to confirm it's being received
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        */
        // Retrieve form data using proper 'name' attributes from the form
        $waste_type = $_POST['waste_type'] ?? null;
        $quantity = $_POST['quantity'] ?? null;
        $date = $_POST['date'] ?? null;
        $time = $_POST['time'] ?? null;
        $com_id = 1; // You can dynamically assign this if needed

        // Check if any fields are empty
        if (empty($waste_type) || empty($quantity) || empty($date) || empty($time)) {
            echo "Error: All fields are required!";
            exit; // Stop script execution if validation fails
        }
        /*
        // Debug: Print form variables to ensure they're correctly captured
        echo "Waste Type: $waste_type<br>";
        echo "Quantity: $quantity<br>";
        echo "Date: $date<br>";
        echo "Time: $time<br>";
        */
        // Insert data into the database
        $stmt = $conn->prepare("INSERT INTO schedule (`sch-date`, `sch-time`, `waste_type`, `Com_Id`, `sch-quantity`) VALUES (?, ?, ?, ?, ?)");

        if ($stmt === false) {
            die('Prepare() failed: ' . htmlspecialchars($conn->error)); // Debugging statement
        }

        // Bind the parameters to the SQL query
        $stmt->bind_param("sssdi", $date, $time, $waste_type, $com_id, $quantity);
        
        // Execute the query
        if ($stmt->execute()) {
            echo "<script>alert('Pickup scheduled successfully!');</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
?>
<script src="../Javascript/schedulePickUp.js"></script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Pickup</title>
    <link rel="stylesheet" href="../CSS/schedulePickUp.css">
</head>

<body style="margin: 0;">
    <div style="margin-left: 14vw;">
        <section class="form-section">
          
            <!-- Form to schedule pickup -->
            <form class="pickup-form" method="POST" action="">
                <label for="waste_type">Waste Type</label>
                <select id="waste_type" name="waste_type">
                    <option value="general">General</option>
                    <option value="recycling">Recycling</option>
                    <option value="hazardous">Hazardous</option>
                </select>

                <label for="quantity">Quantity (kg)</label>
                <input type="number" id="quantity" name="quantity" placeholder="0.0" min="0" step="0.1" required>

                <label for="date">Date to Apply</label>
                <input type="date" id="date" name="date" required>

                <label for="time">Pickup Time</label>
                <input type="time" id="time" name="time" required>

                <div class="form-buttons">
                    <button type="button" class="cancel-btn">Cancel</button>
                    <button type="submit" class="submit-btn">Submit</button>
                </div>
            </form>
        </section>       
    </div>
</body>
</html>