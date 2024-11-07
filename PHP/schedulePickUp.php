<?php

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include necessary files
$filename = basename(__FILE__);
include "header.php";
include "sidebar.php";
include "../SQL_FILE/database.php";

// Assume user_id is stored in session
$user_id = $_SESSION['User_ID'] ?? null; // Get user_id from session

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    // Retrieve form data
    $schedule_id = $_POST['schedule_id'] ?? null;
    $waste_type = $_POST['waste_type'] ?? null;
    $quantity = $_POST['quantity'] ?? null;

    // Initialize an array to track missing fields
    $missing_fields = [];

    // Check each field and add missing fields to the array
    if (empty($schedule_id))
    {
        $missing_fields[] = "Schedule";
    }
    if (empty($waste_type))
    {
        $missing_fields[] = "Waste Type";
    }
    if (empty($quantity))
    {
        $missing_fields[] = "Quantity";
    }
    if (!$user_id)
    {
        $user_id = $_SESSION['User_ID'] ?? null;
        echo $user_id;
        if (!$user_id)
        {
            echo "<script>alert('Error: User ID not found. Please log in again.');</script>";
            exit; // Prevents further execution if user ID is not set
        }
    }

    // Check if there are any missing fields
    if (!empty($missing_fields))
    {
        // Display specific errors for missing fields
        $missing = implode(", ", $missing_fields);
        echo "<script>alert('Error: The following fields are missing: $missing');</script>";
    }
    else
    {
        // Check if user has already selected this schedule
        $check_stmt = $conn->prepare("SELECT * FROM users_schedule WHERE User_ID = ? AND sch_id = ?");
        $check_stmt->bind_param("ii", $user_id, $schedule_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        // Fetch the selected schedule's date and time for the confirmation prompt
        $schedule_stmt = $conn->prepare("SELECT `sch-date`, `sch-time` FROM schedule WHERE Sch_id = ?");
        $schedule_stmt->bind_param("i", $schedule_id);
        $schedule_stmt->execute();
        $schedule_stmt->bind_result($date, $time);
        $schedule_stmt->fetch();
        $schedule_stmt->close();

        if ($result->num_rows > 0)
        {
            // Schedule already exists for this user
            echo "<script>alert('Error: You have already scheduled this pickup.');</script>";
        }
        else
        {
            // Insert data into users_schedule table
            $stmt = $conn->prepare("INSERT INTO users_schedule (user_id, Sch_Id) VALUES (?, ?)");

            if ($stmt === false)
            {
                die('Prepare() failed: ' . htmlspecialchars($conn->error));
            }

            // Bind parameters and execute the query
            $stmt->bind_param("ii", $user_id, $schedule_id);

            if ($stmt->execute()) {
                // Display a confirmation prompt before showing the success message
            echo "<script>
                if (confirm('Details:\\n- Waste Type: $waste_type\\n- Quantity: $quantity kg\\n- Date: $date\\n- Time: $time \\n\\n Are you sure you want to submit this pickup schedule?')) {
                    alert('Pickup scheduled successfully!');
                    setTimeout(() => { window.location.href = 'dashboard.php'; }, 100); // Redirect to the dashboard
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
                    if ($schedule_result->num_rows > 0)
                    {
                        while ($row = $schedule_result->fetch_assoc())
                        {
                            echo "<option value='{$row['Sch_id']}'>{$row['sch-date']} - {$row['sch-time']}</option>";
                        }
                    }
                    else
                    {
                        echo "<option disabled>No schedules available</option>";
                    }
                    ?>
                </select>

                <!-- Other form elements -->
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