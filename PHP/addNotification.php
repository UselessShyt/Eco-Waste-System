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

    // Initialize success message
    $success_message = "";

    // Handle form submission to add notification
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'] ?? null;
        $message = $_POST['message'] ?? null;
        $notice_date = $_POST['notice_date'] ?? null;
        $com_id = $_POST['com_id'] ?? null; // Assuming community ID is selected

        // Insert notification into the database
        if ($title && $message && $notice_date && $com_id) {
            $stmt = $conn->prepare("INSERT INTO notification (Title, Message, notice_date, com_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $title, $message, $notice_date, $com_id);

            if ($stmt->execute()) {
                $success_message = "Notification added successfully!";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "<script>alert('Please fill out all fields.');</script>";
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Notifications</title>
    <link rel="stylesheet" href="../CSS/addNotification.css">
</head>

<body>
    <div style="margin-left: 14vw; padding: 20px;">
        <div class="form-section"> <!-- Updated wrapper for CSS application -->
            <h2>Add Notification</h2>

            <?php if ($success_message): ?>
                <p class="success-message"><?php echo $success_message; ?></p>
            <?php endif; ?>

            <form method="POST" action="">
                <!-- Title -->
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>

                <!-- Message -->
                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="4" required></textarea>

                <!-- Notification Date -->
                <label for="notice_date">Notification Date:</label>
                <input type="date" id="notice_date" name="notice_date" required>

                <!-- Community ID -->
                <label for="com_id">Community:</label>
                <select id="com_id" name="com_id" required>
                    <option value="" disabled selected>Select Community</option>
                    <?php
                    // Fetch communities from the database
                    $community_query = "SELECT Com_Id, Area FROM community";
                    $community_result = $conn->query($community_query);

                    if ($community_result->num_rows > 0) {
                        while ($community = $community_result->fetch_assoc()) {
                            echo "<option value='{$community['Com_Id']}'>{$community['Area']}</option>";
                        }
                    } else {
                        echo "<option disabled>No communities available</option>";
                    }
                    ?>
                </select>

                <!-- Submit Button -->
                <input type="submit" value="Add Notification">
            </form>
        </div>
    </div>
</body>
</html>