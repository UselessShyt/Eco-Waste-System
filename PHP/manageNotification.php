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

    // Load existing notification preferences
    $preferences_query = "SELECT pickup_reminders, unscheduled_notifications FROM preferences WHERE user_id = ?";
    $preferences_stmt = $conn->prepare($preferences_query);
    $preferences_stmt->bind_param("i", $user_id);
    $preferences_stmt->execute();
    $preferences_result = $preferences_stmt->get_result();
    $preferences = $preferences_result->fetch_assoc();
 
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
    $notification_history_query = "SELECT Title, Message, notice_date, status FROM notification WHERE com_id = ? ORDER BY notice_date DESC";
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
    
</head>

<body>
    <div style="margin-left: 14vw; padding: 20px;">
        <!-- <h2>Manage Notification</h2>-->

        <!-- Notification Preferences Section -->
        <section class="notification-section notification-preferences-section">
            <h3>Notification Preferences</h3>
            <form method="POST">
                <div class="preference-item">
                    <label class="preference-toggle">
                        Receive pickup reminders
                        <input type="checkbox" name="pickup_reminders" <?php echo $pickup_reminders ? 'checked' : ''; ?>>
                    </label>
                </div>
                <div class="preference-item">
                    <label class="preference-toggle">
                        Receive unscheduled pickup notifications
                        <input type="checkbox" name="unscheduled_notifications" <?php echo $unscheduled_notifications ? 'checked' : ''; ?>>
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
                                    <td class="status-badge">Scheduled</td>
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
                                    Sent on <?php echo date("jS M, Y - h:i A", strtotime($notification['notice_date'])); ?>
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