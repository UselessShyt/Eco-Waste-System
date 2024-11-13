<?php

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include necessary files for the header, sidebar, and database connection
$filename = basename(__FILE__);
include "header.php";
include "sidebar.php";
include "../SQL_FILE/database.php";

// Assume user_id and user_role are stored in session
$user_id = $_SESSION['User_ID'] ?? null;
$user_role = $_SESSION['role'] ?? null; // Get user role from session

// Handle form submission when the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    // Retrieve form data from the POST request
    $schedule_id = $_POST['schedule_id'] ?? null;
    $waste_type = $_POST['waste_type'] ?? null;
    $quantity = $_POST['quantity'] ?? null;

    // Initialize an array to track missing fields
    $missing_fields = [];

    // Check each field and add to the missing fields array if empty
    if (empty($schedule_id)) {
        $missing_fields[] = "Schedule";
    }
    if (empty($waste_type)) {
        $missing_fields[] = "Waste Type";
    }
    if (empty($quantity)) {
        $missing_fields[] = "Quantity";
    }
    if (!$user_id) {
        echo "<script>alert('Error: User ID not found. Please log in again.');</script>";
        exit;
    }

    // Check if there are any missing fields
    if (!empty($missing_fields)) {
        $missing = implode(", ", $missing_fields);
        echo "<script>alert('Error: The following fields are missing: $missing');</script>";
    } else {
        // Fetch schedule date and time from the database for the selected schedule
        $schedule_stmt = $conn->prepare("SELECT `sch-date`, `sch-time` FROM schedule WHERE Sch_id = ?");
        $schedule_stmt->bind_param("i", $schedule_id);
        $schedule_stmt->execute();
        $schedule_stmt->bind_result($date, $time);
        $schedule_stmt->fetch();
        $schedule_stmt->close();

        // Check if the user has already scheduled this pickup
        $check_stmt = $conn->prepare("SELECT * FROM users_schedule WHERE user_id = ? AND Sch_Id = ?");
        $check_stmt->bind_param("ii", $user_id, $schedule_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            echo "<script>alert('Error: You have already scheduled this pickup.');</script>";
        } else {
            // Insert data into users_schedule table, including waste_type and quantity
            $stmt = $conn->prepare("INSERT INTO users_schedule (user_id, Sch_Id, waste_type, sch_quantity) VALUES (?, ?, ?, ?)");
            if ($stmt === false) {
                die('Prepare() failed: ' . htmlspecialchars($conn->error));
            }

            // Bind parameters and execute the insert query
            $stmt->bind_param("iisd", $user_id, $schedule_id, $waste_type, $quantity);
            if ($stmt->execute()) {
                // Define redirect URL based on user role
                $redirect_url = $user_role === 'ADMIN' ? 'adminDashboard.php' : 'dashboard.php';

                // Show confirmation prompt with date, time, quantity, and redirect based on role
                echo "<script>
                    if (confirm('Details:\\n- Waste Type: $waste_type\\n- Quantity: $quantity kg\\n- Date: $date\\n- Time: $time \\n\\n Are you sure you want to submit this pickup schedule?')) {
                        alert('Pickup scheduled successfully!');
                        setTimeout(() => { 
                            window.location.href = '$redirect_url'; 
                        }, 100);
                    } else {
                        window.location.href = 'schedulePickUp.php';
                    }
                </script>";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}

// Fetch available schedules for dropdown
$schedule_query = "SELECT Sch_id, `sch-date`, `sch-time` FROM schedule";
$schedule_result = $conn->query($schedule_query);
?>

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
            <form class="pickup-form" method="POST" action="">

                <!-- Schedule Dropdown populated from schedule table -->
                <label for="schedule">Select Schedule</label>
                <select id="schedule" name="schedule_id" required>
                    <option value="" disabled selected>Select a Schedule</option>
                    <?php
                    if ($schedule_result->num_rows > 0) {
                        while ($row = $schedule_result->fetch_assoc()) {
                            echo "<option value='{$row['Sch_id']}'>{$row['sch-date']} - {$row['sch-time']}</option>";
                        }
                    } else {
                        echo "<option disabled>No schedules available</option>";
                    }
                    ?>
                </select>

                <!-- Waste Type Dropdown -->
                <label for="waste_type">Waste Type</label>
                <select id="waste_type" name="waste_type" required>
                    <option value="general">General</option>
                    <option value="recycling">Recycling</option>
                    <option value="hazardous">Hazardous</option>
                    <option value="others">Others</option>
                </select>

                <label for="quantity">Quantity (kg)</label>
                <input type="number" id="quantity" name="quantity" placeholder="0.0" min="0" step="0.1" required>

                <div class="form-buttons">
                    <button type="button" class="cancel-btn">Cancel</button>
                    <button type="submit" class="submit-btn">Submit</button>
                </div>
            </form>
        </section>
    </div>
</body>

</html>
