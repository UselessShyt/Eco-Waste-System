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

// Check if user has existing notification preferences
$check_preferences_query = "SELECT 1 FROM preferences WHERE user_id = ?";
$check_preferences_stmt = $conn->prepare($check_preferences_query);
$check_preferences_stmt->bind_param("i", $user_id);
$check_preferences_stmt->execute();
$has_preferences = $check_preferences_stmt->get_result()->num_rows > 0;

// If no preferences exist, set defaults (off/0 for all notifications)
if (!$has_preferences) {
    $default_preferences_query = "INSERT INTO preferences (user_id, pickup_reminders, unscheduled_notifications) VALUES (?, 0, 0)";
    $default_preferences_stmt = $conn->prepare($default_preferences_query);
    $default_preferences_stmt->bind_param("i", $user_id);
    $default_preferences_stmt->execute();
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

// Fetch Notification History for the user's community
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

$notification_history_query = "SELECT Title, Message, notice_date, notice_time, status FROM notification WHERE com_id = ? ORDER BY notice_date DESC";
$notification_stmt = $conn->prepare($notification_history_query);
$notification_stmt->bind_param("i", $com_id);
$notification_stmt->execute();
$notification_history = $notification_stmt->get_result();

if (!$notification_history) {
    echo "Error fetching notifications: " . $conn->error;
    exit;
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
        /* Container styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            margin-left: 14vw;
            padding: 20px;
        }
        
        /* Section Styling */
        .notification-section {
            background-color: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .notification-section h3 {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }

        /* Notification Preferences Styling */
        .preference-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
            font-size: 1.1em;
            color: #333;
        }

        /* Toggle switch styling */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

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

        input:checked + .slider {
            background-color: #4CAF50;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .submit {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
            margin-top: 15px;
        }

        .submit:hover {
            background-color: #45a049;
        }

        /* Upcoming Pickups Table */
        .upcoming-pickups-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 1em;
            color: #333;
        }

        .upcoming-pickups-table tr {
            border-bottom: 1px solid #ddd;
        }

        .upcoming-pickups-table td {
            padding: 15px;
            text-align: left;
        }

        .upcoming-pickups-table td:first-child {
            font-weight: bold;
            color: #4CAF50;
        }

        /* Notification History Styling */
        .notification-history {
            list-style-type: none;
            padding: 0;
            font-size: 1em;
            color: #333;
        }

        .notification-history li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            transition: background-color 0.3s;
        }

        .notification-history li:hover {
            background-color: #f1f1f1;
        }

        .notification-history li span {
            display: block;
            padding: 0 5px;
        }

        .notification-history li .title {
            font-weight: bold;
            color: #4CAF50;
            flex: 2;
        }

        .notification-history li .message {
            flex: 3;
        }

        .notification-history li .date-time {
            flex: 1;
            text-align: center;
            font-size: 0.9em;
            color: #777;
        }

        /* Status badge styling */
        .status-badge1 {
            background-color: #ffe4b3;
            color: #d35400;
            padding: 4px 12px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 0.9em;
            display: inline-block;
            margin-left: 5px;
        }

        /* Add some space between sections */
        .pickup-date-time,
        .notification-message {
            font-size: 1em;
            color: #666;
        }

    </style>
</head>
<body>
    <div class="container">
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
            <table class="upcoming-pickups-table">
                <tbody>
                    <?php if ($upcoming_pickups->num_rows > 0): ?>
                        <?php while ($pickup = $upcoming_pickups->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($pickup['waste_type']); ?></td>
                                <td><?php echo date("jS M, Y - h:i A", strtotime($pickup['pickup_date'] . ' ' . $pickup['pickup_time'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">No upcoming pickups scheduled.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <!-- Notification History Section -->
        <section class="notification-section">
            <h3>Notification History</h3>
            <ul class="notification-history">
                <?php if ($notification_history->num_rows > 0): ?>
                    <?php while ($notification = $notification_history->fetch_assoc()): ?>
                        <li>
                            <span class="title"><?php echo htmlspecialchars($notification['Title']); ?></span>
                            <span class="message"><?php echo htmlspecialchars($notification['Message']); ?></span>
                            <span class="date-time"><?php echo date("jS M, Y - h:i A", strtotime($notification['notice_date'] . ' ' . $notification['notice_time'])); ?></span>
                            <span class="status-badge1"><?php echo htmlspecialchars($notification['status']); ?></span>
                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No notification history available.</p>
                <?php endif; ?>
            </ul>
        </section>
    </div>
</body>
</html>
