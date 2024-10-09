<style>
    aside#sidebar {
        width: 200px;
        padding: 20px;
        height: 100vh; /* Adjust height as needed */
        position: fixed; /* Keep sidebar fixed */
        top: 121px;
        left: 0;
        border-right-style: solid;
        border-right-width: 1px;
    }

    aside#sidebar ul {
        list-style: none;
        padding: 0;
    }

    aside#sidebar ul li {
        margin-bottom: 10px;
        padding-top: 20px;
        padding-bottom: 20px;
    }

    aside#sidebar ul li a {
        text-decoration: none;
        color: #333;
        font-weight: bold;
        margin-left: 25px;
    }
</style>

<body>
    <aside id="sidebar">
        <div style="height: 80%;">
            <div style="margin-bottom: 50px;">
                <h3>MAIN MENU</h3>
                <ul>
                    <li><a href="#">Dashboard</a></li>
                    <li><a href="#">Schedule Pickup</a></li>
                    <li><a href="#">Pickup History</a></li>
                    <li><a href="#">Report Issues</a></li>
                    <li><a href="#">Generate Reports</a></li>
                </ul>
            </div>
            <div>
                <h3>PREFERENCES</h3>
                <ul>
                    <li><a href="#">Preferences</a></li>
                    <li><a href="#">Manage Notification</a></li>
                    <li><a href="#">Settings</a></li>
                    <li><a href="#">Help & Center</a></li>
                </ul>
            </div>
        </div>
        <div>
            <ul>
                <li><a href="#">Log Out</a></li>
            </ul>
        </div>

    </aside>
</body>