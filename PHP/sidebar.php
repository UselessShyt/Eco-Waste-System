<style>
    aside#sidebar {
        width: 10vw;
        padding-left: 2vw;
        padding-right: 2vw;
        padding-top: 1vh;
        height: 87.5vh; /* Adjust height as needed */
        position: fixed; /* Keep sidebar fixed */
        top: 12.5vh;
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
        border-radius: 10px;
        overflow: hidden;
    }

    aside#sidebar ul li a {
        display: block; /* Make the entire <a> fill the <li> */
        padding: 1vh;  /* Space inside the clickable box */
        text-decoration: none;
        color: #333;
        font-weight: bold;
        margin-left: 0; /* Remove the margin-left to align the text */
    }

    aside#sidebar ul li:hover {
        background-color: #418952;
    }

    aside#sidebar ul li:hover a {
        color: white; /* Change text color on hover */
    }
</style>

<body>
    <aside id="sidebar">
        <div style="height: 85%;">
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
