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
        $notice_time = $_POST['notice_time'] ?? null;
        $com_id = $_POST['com_id'] ?? null; // Assuming community ID is selected

        // Insert notification into the database
        if ($title && $message && $notice_date && $notice_time && $com_id) {
            // Prepare the INSERT query
            $stmt = $conn->prepare("INSERT INTO notification (Title, Message, notice_date, notice_time, com_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $title, $message, $notice_date, $notice_time, $com_id);
        
            // Execute the query and check for success
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

    <style>
        /* General styling for form section */
        .form-section {
            margin-top: 20px;
            padding: 20px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-section h2 {
            text-align: center;
            color: #418952;
            font-size: 1.5em;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .form-section label {
            font-size: 1em;
            font-weight: 600;
            color: #333;
            display: block;
            margin-top: 15px;
        }

        .form-section input[type="text"],
        .form-section input[type="date"],
        .form-section input[type="time"], /* Add this line */
        .form-section select,
        .form-section textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 0.9em;
        }

        .form-section textarea {
            resize: vertical;
            height: 80px;
        }

        .form-section input[type="submit"] {
            display: inline-block;
            background-color: #418952;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            margin-top: 15px;
            width: 100%;
            font-weight: 600;
        }

        .form-section input[type="submit"]:hover {
            background-color: #367a4a;
        }

        .success-message {
            color: green;
            font-weight: 600;
            text-align: center;
            margin-bottom: 15px;
        }

    </style>
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

                <!-- Notification Time -->
                <label for="notice_time">Notification Time:</label>
                <input type="time" id="notice_time" name="notice_time" required>

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