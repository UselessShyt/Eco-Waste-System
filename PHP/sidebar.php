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
                    <li class="active"><i class="fa fa-home"></i><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                    <li><i class="fa fa-calendar"></i><a href="#" class="nav-link">Schedule Pickup</a></li>
                    <li><i class="fa fa-clock-o"></i><a href="#" class="nav-link">Pickup History</a></li>
                    <li><i class='fa fa-pencil'></i><a href="Report-Issues.php" class="nav-link">Report Issues</a></li>
                    <li><i class="fa fa-file-pdf-o"></i><a href="#" class="nav-link">Generate Reports</a></li>
                </ul>
            </div>
            <div>
                <h3>PREFERENCES</h3>
                <ul>
                    <li><i class="fa fa-thumbs-up"></i><a href="#" class="nav-link">Preferences</a></li>
                    <li><i class="fa fa-bell"></i><a href="#" class="nav-link">Manage Notification</a></li>
                    <li><i class="fa fa-gear"></i><a href="#" class="nav-link">Settings</a></li>
                    <li><i class="fa fa-question-circle"></i><a href="#" class="nav-link">Help & Center</a></li>
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
        // Get all links with class "nav-link"
        const links = document.querySelectorAll('.nav-link');

        // Loop through the links and add a click event listener
        links.forEach(link => {
            link.addEventListener('click', function () {
                // Remove "active" class from all li elements
                document.querySelectorAll('li').forEach(li => li.classList.remove('active'));

                // Add "active" class to the clicked li element
                this.parentElement.classList.add('active');
            });
        });

    </script>
</body>