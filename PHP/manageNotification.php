<?php
    //session_start();
    $filename = basename(__FILE__);
    include "header.php";
    include "sidebar.php";
    include "../SQL_FILE/database.php";

    // Enable error reporting for debugging
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Assume user_id is stored in session
    $user_id = $_SESSION['User_ID'] ?? null;
    if (!$user_id) {
        echo "Error: User not logged in.";
        exit;
    }

    // Set notification threshold (e.g., 24 hours before the pickup)
    $time_threshold = date("Y-m-d H:i:s", strtotime('+24 hours'));

    // Query to find upcoming pickups within 24 hours for users who opted for reminders
    $upcoming_pickups_query = "
        SELECT u.user_id, u.email, s.`sch-date` AS pickup_date, s.`sch-time` AS pickup_time, us.waste_type
        FROM users u
        JOIN users_schedule us ON u.user_id = us.user_id
        JOIN schedule s ON us.Sch_Id = s.Sch_Id
        JOIN preferences p ON u.user_id = p.user_id
        WHERE s.`sch-date` = CURDATE() + INTERVAL 1 DAY 
        AND p.pickup_reminders = 1
        AND NOT EXISTS (
            SELECT 1 FROM notification_timely nh
            WHERE nh.user_id = u.user_id
                AND nh.pickup_date = s.`sch-date`
                AND nh.pickup_time = s.`sch-time`
        )
    ";

    // Execute query
    $upcoming_pickups_result = $conn->query($upcoming_pickups_query);

    while ($pickup = $upcoming_pickups_result->fetch_assoc()) {
        $user_id = $pickup['user_id'];
        $email = $pickup['email'];
        $pickup_date = $pickup['pickup_date'];
        $pickup_time = $pickup['pickup_time'];
        $waste_type = $pickup['waste_type'];

        // Send notification (e.g., via email or logging for testing)
        echo "Notification for User ID: $user_id - Upcoming $waste_type pickup on $pickup_date at $pickup_time<br>";
        
        // Record this notification in notification_timely to avoid duplicates
            $record_query = "
            INSERT INTO notification_timely (user_id, pickup_date, pickup_time, status)
            VALUES (?, ?, ?, 'Sent')
        ";
        $stmt = $conn->prepare($record_query);
        $stmt->bind_param("iss", $user_id, $pickup_date, $pickup_time);
        $stmt->execute();

        // Log after insertion
        error_log("Inserted notification for user: $user_id on $pickup_date at $pickup_time");
    }

    // Load existing notification preferences
    $preferences_query = "SELECT pickup_reminders, unscheduled_notifications FROM preferences WHERE user_id = ?";
    $preferences_stmt = $conn->prepare($preferences_query);
    $preferences_stmt->bind_param("i", $user_id);
    $preferences_stmt->execute();
    $preferences_result = $preferences_stmt->get_result();
    $preferences = $preferences_result->fetch_assoc();

    // Set the initial values for the toggle switches
    $pickup_reminders = $preferences['pickup_reminders'] ?? 0;
    $unscheduled_notifications = $preferences['unscheduled_notifications'] ?? 0;

    // Save updated preferences if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Set the values based on whether the checkboxes are checked
        $pickup_reminders = isset($_POST['pickup_reminders']) ? 1 : 0;
        $unscheduled_notifications = isset($_POST['unscheduled_notifications']) ? 1 : 0;
    
        // Update preferences in the database
        $update_query = "
            INSERT INTO preferences (user_id, pickup_reminders, unscheduled_notifications)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                pickup_reminders = VALUES(pickup_reminders),
                unscheduled_notifications = VALUES(unscheduled_notifications)
        ";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("iii", $user_id, $pickup_reminders, $unscheduled_notifications);
        $update_stmt->execute();
    
        // Set session variable for alert
        $_SESSION['preferences_saved'] = true;
    }
    
    // Display alert if preferences were saved
    if (isset($_SESSION['preferences_saved'])) {
        echo "<script>alert('Notification Preferences saved!');</script>";
        unset($_SESSION['preferences_saved']); // Clear the session variable after showing alert
    }

    // Set a time threshold for notifications (e.g., 24 hours for reminders)
    $time_threshold = date("Y-m-d H:i:s", strtotime('+24 hours'));

    // Fetch Upcoming Pickups for the user
    $upcoming_pickups_query = "
        SELECT s.`sch-date` AS pickup_date, s.`sch-time` AS pickup_time, us.waste_type
        FROM schedule s
        JOIN users_schedule us ON s.Sch_Id = us.Sch_Id
        WHERE us.user_id = ? AND s.`sch-date` >= CURDATE()
        ORDER BY s.`sch-date`, s.`sch-time`
    ";

    $upcoming_pickups_stmt = $conn->prepare($upcoming_pickups_query);
    $upcoming_pickups_stmt->bind_param("i", $user_id);
    $upcoming_pickups_stmt->execute();
    $upcoming_pickups = $upcoming_pickups_stmt->get_result();

    // Fetch the user's community ID if not already set
    $com_id = $_SESSION['Com_Id'] ?? null;

    if (!$com_id) {
        // Fetch com_id from the database
        $stmt = $conn->prepare("SELECT Com_Id FROM users WHERE User_ID = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($com_id);
        $stmt->fetch();
        $stmt->close();

        // Store com_id in session for future use
        $_SESSION['Com_Id'] = $com_id;
    }

    // Fetch Notification History for the user's community
    $notification_history_query = "SELECT Title, Message, notice_date, notice_time, status FROM notification WHERE com_id = ? ORDER BY notice_date DESC";
    $notification_stmt = $conn->prepare($notification_history_query);
    $notification_stmt->bind_param("i", $com_id);
    $notification_stmt->execute();
    $notification_history = $notification_stmt->get_result();

    if (!$notification_history) {
        echo "Error fetching notifications: " . $conn->error;
        exit;
    }

    // Update status to 'Delivered' for notifications where the scheduled time has passed
    $current_datetime = date("Y-m-d H:i:s");

    $update_status_query = "
        UPDATE notification
        SET status = 'Delivered'
        WHERE status = 'Pending'
        AND CONCAT(notice_date, ' ', notice_time) <= ?
    ";

    $stmt = $conn->prepare($update_status_query);
    $stmt->bind_param("s", $current_datetime);
    $stmt->execute();

    // Log the result (optional)
    if ($stmt->affected_rows > 0) {
        error_log("Updated status to 'Delivered' for " . $stmt->affected_rows . " notifications.");
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Notifications</title>
    <link rel="stylesheet" href="../CSS/manageNotification.css">

    <style>
        /* Aligning columns */
        .pickup-date-time{
            text-align: center; /* Center aligns the date and time */
            padding-right: 20px; /* Adds some space to the right */
            font-size: 1.0em ;
        }

        /* Upcoming Pickups table styling */
        .upcoming-pickups-table {
            width: 100%;
            border-collapse: collapse;
        }

        .upcoming-pickups-table tr {
            border-bottom: 1px solid #ddd;
        }

        .upcoming-pickups-table td {
            padding: 10px;
            vertical-align: middle;
        }

        /* Optional: Add width to the table columns for more spacing */
        .upcoming-pickups-table td,
        .notification-history li {
            padding: 10px 15px; /* Adds padding for better spacing */
        }

        .pickup-type {
            background-color: transparent;
            color: inherit;
            padding: 0;
            font-weight: normal;
            text-align: left;
            
        }

        /* Status badge styling */
        .status-badge1 {
            background-color: #ffe4b3;
            color: #d35400;
            float: right;
            padding: 4px 12px;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.9em;
            width: 70px;
            text-align: center;
            font-weight: bold;
            display: inline-block;
            margin: 8px; 
        }

        /* Column styling */
        .pickup-type1, .title {
            width: 300px; /* Adjust width as needed */
        }
        
        .pickup-type1, .notification-message {
            width: 200px; /* Adjust width as needed */

        }

        .pickup-date-time, .date-time {
            width: 40%; /* Adjust width as needed */
            text-align: center;
            padding-right: 10px;

        }

        /* Aligns the status badge column */
        .status-column {
            width: 20%;
            text-align: right;

        }

        /* Optional: Add width to table columns for better alignment */
        .upcoming-pickups-table td, .notification-history li {
            padding: 10px 15px;
            vertical-align: middle; /* Aligns content vertically */
        }

        /* Container styling for the preference items */
        .preference-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        /* Toggle switch styling */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        /* Hide default checkbox */
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        /* Background of the toggle switch */
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            border-radius: 24px;
            transition: 0.4s;
        }

        /* Circle inside the toggle switch */
        .slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            border-radius: 50%;
            transition: 0.4s;
        }

        /* Toggle switch when checked */
        input:checked + .slider {
            background-color: #4CAF50; /* Green color when active */
        }

        input:checked + .slider:before {
            transform: translateX(26px); /* Moves the circle to the right */
        }

        

        .notification-item .date-time {
            text-align: center;
            
        }

        .notification-history .status-label.delivered-label {
        background-color: #d1f5d3;
        color: #27ae60; 
    }
    </style>
</head>

<body>
    <div style="margin-left: 14vw; padding: 20px;">
        <!-- <h2>Manage Notification</h2>-->

        <!-- Notification Preferences Section -->
        <section class="notification-section notification-preferences-section">
            <h3>Notification Preferences</h3>
            <form method="POST">
                <div class="preference-item">
                    <label>Receive pickup reminders</label>
                    <label class="toggle-switch">
                        <input type="checkbox" name="pickup_reminders" <?php echo $pickup_reminders ? 'checked' : ''; ?>>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="preference-item">
                    <label>Receive unscheduled pickup notifications</label>
                    <label class="toggle-switch">
                        <input type="checkbox" name="unscheduled_notifications" <?php echo $unscheduled_notifications ? 'checked' : ''; ?>>
                        <span class="slider"></span>
                    </label>
                </div>
                <button type="submit" class="submit">Save Preferences</button>
            </form>
        </section>

        <!-- Upcoming Pickups Section -->
        <section class="notification-section">
            <h3>Upcoming Pickups</h3>
            <div class="scrollable-content">
                <table class="upcoming-pickups-table">
                    <tbody>
                        <?php if ($upcoming_pickups->num_rows > 0): ?>
                            <?php while ($pickup = $upcoming_pickups->fetch_assoc()): ?>
                                <tr>
                                    <td class="pickup-type1"><?php echo htmlspecialchars($pickup['waste_type']); ?></td>
                                    <td class="pickup-date-time">
                                        <?php echo date("jS M, Y - h:i A", strtotime($pickup['pickup_date'] . ' ' . $pickup['pickup_time'])); ?>
                                    </td>
                                    <td class="status-badge1">Scheduled</td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">No upcoming pickups scheduled.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Notification History Section -->
        <section class="notification-section">
            <h3>Notification History</h3>
            <div class="scrollable-content">
                <?php if ($notification_history->num_rows > 0): ?>
                    <ul class="notification-history">
                        <?php while ($notification = $notification_history->fetch_assoc()): ?>
                            <li class="notification-item">
                                <div class="title"><?php echo htmlspecialchars($notification['Title']); ?></div>
                                <div class="notification-message"><?php echo htmlspecialchars($notification['Message']); ?></div>
                                <div class="date-time">
                                    <?php 
                                        // Combine date and time if notice_time is available
                                        $datetime = $notification['notice_date'];
                                        if (!empty($notification['notice_time'])) {
                                            $datetime .= ' ' . $notification['notice_time'];
                                        }
                                        echo date("jS M, Y - h:i A", strtotime($datetime));
                                    ?>
                                </div>
                                <span class="status-label 
                                    <?php echo ($notification['status'] === 'Delivered') ? 'delivered-label' : ''; ?>">
                                    <?php echo htmlspecialchars($notification['status']); ?>
                                </span>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No notifications in history.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</body>
</html>