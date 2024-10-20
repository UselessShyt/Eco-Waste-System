# Eco Waste System

**Eco Waste System** is a web-based platform designed to manage and optimize waste collection services. This system allows users to schedule pickups, report issues, view history, generate reports, and manage notifications.

## Features

- **Schedule Pickups:** Easily schedule waste pickups at your convenience.
- **Report Issues:** Report any issues directly from the dashboard.
- **Notifications:** Stay updated with notifications for upcoming pickups and issues.
- **View History:** Access a record of past pickups and activities.
- **Generate Reports:** Generate detailed reports on waste collection and activities.
- **User Preferences:** Customize settings and notification preferences.

## Technology Stack

- **Front-end:** HTML, CSS, JavaScript
- **Back-end:** PHP
- **Database:** MySQL (via phpMyAdmin)
- **Hosting:** Localhost (XAMPP)

## Installation

1. **Install XAMPP:**
   Download and install [XAMPP](https://www.apachefriends.org/index.html) to set up a local server.

2. **Clone the Repository:**
   ```bash
   git clone https://github.com/UeslessShyt/eco-waste-system.git
Set Up Database:

Open phpMyAdmin from XAMPP control panel.
Create a new database called ecodatabase.
Import the provided SQL file (ecodatabase.sql) into the new database.
Configure the Project:

Move the project folder into the htdocs directory in your XAMPP installation.
Ensure the database connection details are correct in the PHP configuration file (e.g., config.php).
Start the Server:

Open the XAMPP control panel and start Apache and MySQL.
Navigate to http://localhost/eco-waste-system in your browser to view the project.
Usage
Dashboard: Navigate to the dashboard to access all features, including scheduling pickups and viewing history.

Notifications: Manage and receive notifications for upcoming pickups or issues reported.

Reports: Generate and download reports from the "Generate Reports" section.

Database
The system is powered by a MySQL database named ecodatabase. Ensure that you import the correct database schema from the ecodatabase.sql file, and configure the database connection in your PHP code.
