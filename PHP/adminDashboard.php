<?php
$filename = basename(__FILE__);
include "header.php";
include "sidebar.php";
include "../SQL_FILE/database.php";
if (!isset($_SESSION['User_ID']))
{
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $user_id = $_POST['user_id'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if the new password matches the confirmation password
    if ($new_password === $confirm_password)
    {
        // Update the password and is_password_changed in one query
        $sql = "UPDATE users SET password = ?, is_password_changed = 1 WHERE User_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $new_password, $user_id); // Bind the new password and user ID

        if ($stmt->execute())
        {
            // On success, remove the session variable and redirect
            unset($_SESSION['force_password_change']);
            $_SESSION['password_change_success'] = true; // Set success flag
        }
        else
        {
            echo "Error updating password.";
        }
    }
    else
    {
        echo "Passwords do not match.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<style>
    .label p {
        font-size: 14px;
        font-weight: bold;
    }
</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <title>Document</title>
</head>
<style>
    .modal-content {
        background-color: white;
        padding: 20px;
        border-radius: 5px;
        width: 30%;
        margin: auto;
    }

    .input-group {
        margin-bottom: 15px;
    }

    .input-group label {
        display: block;
        margin-bottom: 5px;
    }

    .input-group input {
        width: 100%;
        padding: 8px;
        box-sizing: border-box;
    }

    button {
        padding: 10px 20px;
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
    }

    button:hover {
        background-color: #45a049;
    }
</style>

<body style="margin: 0;">
    <div style="margin-left: 14vw;">
        <div class="image-grid">
            <div class="image-item">
                <div class="img-container"><a href="adminFunction.php"><img class="image" src="../Img/Admin.png"
                            alt="Calendar"></a></div>
                <div class="label">
                    <p>Admin Page</p>
                </div>
            </div>
            <div class="image-item">
                <div class="img-container"><a href="schedulePickup.php"><img class="image" src="../Img/calander.png"
                            alt="Calendar"></a></div>
                <div class="label">
                    <p>Schedule Pickup</p>
                </div>
            </div>
            <div class="image-item">
                <div class="img-container"><a href="Report-Issues.php"><img class="image" src="../Img/Issue.png"
                            alt="Issue"></a></div>
                <div class="label">
                    <p>Report Issues</p>
                </div>
            </div>
            <div class="image-item">
                <div class="img-container"><a href="#notifications"><img class="image" src="../Img/notification_1.png"
                            alt="Notification"></a></div>
                <div class="label">
                    <p>Notifications</p>
                </div>
            </div>
            <div class="image-item">
                <div class="img-container"><a href="view-Pickup-History.php"><img class="image" src="../Img/history.png"
                            alt="History"></a></div>
                <div class="label">
                    <p>View History</p>
                </div>
            </div>
            <div class="image-item">
                <div class="img-container"><a href="#generate-report"><img class="image" src="../Img/report.png"
                            alt="Report"></a></div>
                <div class="label">
                    <p>Generate Report</p>
                </div>
            </div>
            <div class="image-item">
                <div class="img-container"><a href="#Settings"><img class="image" src="../Img/setting_1.png"
                            alt="Settings"></a></div>
                <div class="label">
                    <p>Settings</p>
                </div>
            </div>
        </div>
    </div>
    <?php if (isset($_SESSION['force_password_change']) && $_SESSION['force_password_change']): ?>
        <div id="force-password-change-modal" class="modal">
            <div class="modal-content">
                <h2>Change Your Password</h2>
                <!-- Display error message if available -->
                <?php if (isset($_SESSION['password_change_error'])): ?>
                    <p style="color: red;"><?php echo $_SESSION['password_change_error']; ?></p>
                <?php endif; ?>

                <form method="POST" action="adminDashboard.php">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['User_ID']; ?>">
                    <div class="input-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <div class="input-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit">Change Password</button>
                </form>
            </div>
        </div>

        <script>
            // JavaScript to show modal on page load
            window.onload = function () {
                document.getElementById('force-password-change-modal').style.display = 'block';
            };
        </script>
    <?php endif; ?>

    <!-- Success message modal -->
    <?php if (isset($_SESSION['password_change_success']) && $_SESSION['password_change_success']): ?>
        <div id="password-change-success-modal" class="modal">
            <div class="modal-content">
                <h2>Password Changed Successfully</h2>
                <p>Your password has been updated successfully. You may continue using the dashboard.</p>
                <button onclick="hideSuccessModal()">Close</button>
            </div>
        </div>

        <script>
            // JavaScript to show success modal
            window.onload = function () {
                document.getElementById('password-change-success-modal').style.display = 'block';
            };

            function hideSuccessModal() {
                document.getElementById('password-change-success-modal').style.display = 'none';
            }
        </script>
        <?php
        // Unset the success message after showing it once
        unset($_SESSION['password_change_success']);
        ?>
    <?php endif; ?>
</body>

</html>