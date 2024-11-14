<?php
// Start session to access user role

// Get user role from session
$user_role = $_SESSION['role'] ?? null; // Get user role from session
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../CSS/sidebar.css">
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
</head>

<body>
    <aside id="sidebar">
        <div style="height: 85%;">
            <div style="margin-bottom: 50px;">
                <h3>MAIN MENU</h3>
                <ul>
                    <!-- Dynamically set the dashboard link based on user role -->
                    <li>
                        <i class="fa fa-home"></i>
                        <a href="<?= $user_role === 'ADMIN' ? 'adminDashboard.php' : 'dashboard.php'; ?>" class="nav-link">
                            Dashboard
                        </a>
                    </li>
                    <li><i class="fa fa-calendar"></i><a href="schedulePickUp.php" class="nav-link">Schedule Pickup</a></li>
                    <li><i class="fa fa-clock-o"></i><a href="view-Pickup-History.php" class="nav-link">Pickup History</a></li>
                    <li><i class='fa fa-pencil'></i><a href="Report-Issues.php" class="nav-link">Report Issues</a></li>
                    <li><i class="fa fa-file-pdf-o"></i><a href="generate-reports.php" class="nav-link">Generate Reports</a></li>
                </ul>
            </div>
            <div>
                <h3>PREFERENCES</h3>
                <ul>
                    <li><i class="fa fa-thumbs-up"></i><a href="preferences.php" class="nav-link">Preferences</a></li>
                    <li><i class="fa fa-bell"></i><a href="manageNotifications.php" class="nav-link">Manage Notification</a></li>
                    <li><i class="fa fa-gear"></i><a href="settings.php" class="nav-link">Settings</a></li>
                    <li><i class="fa fa-question-circle"></i><a href="help-center.php" class="nav-link">Help & Center</a></li>
                </ul>
            </div>
        </div>
        <div>
            <ul>
                <li><i class="fa fa-sign-out"></i><a href="login.php">Log Out</a></li>
            </ul>
        </div>
    </aside>

    <script>
        // Get all sidebar links with class "nav-link"
        const links = document.querySelectorAll('.nav-link');

        // Get the current page URL path (just the filename part)
        const currentPath = window.location.pathname.split('/').pop();

        // Loop through each link and check if its href matches the current path
        links.forEach(link => {
            const linkPath = link.getAttribute('href').split('/').pop(); // Get just the filename part of the href

            // If the link's href matches the current path, add the active class
            if (linkPath === currentPath) {
                link.parentElement.classList.add('active'); // Add 'active' class to the <li> containing the link
            }

            // Add click event listener to update active class on click
            link.addEventListener('click', function () {
                // Remove active class from all menu items
                links.forEach(l => l.parentElement.classList.remove('active'));
                // Add active class to the clicked item
                link.parentElement.classList.add('active');
            });
        });
    </script>
</body>

</html>
