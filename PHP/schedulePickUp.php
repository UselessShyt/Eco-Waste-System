
<?php
    include "header.php";
    include "sidebar.php";
    include "../SQL_FILE/database.php"
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    /* Main Content */
.main-content {
    flex-grow: 1;
    padding: 20px;
    background-color: #f9f9f9;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.header h1 {
    font-size: 24px;
    color: #333;
}

.user-actions a {
    margin-left: 10px;
    color: #333;
    text-decoration: none;
}

.user-profile {
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

.form-section {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form-section .tab {
    display: flex;
    margin-bottom: 20px;
}

.tablinks {
    padding: 10px 20px;
    cursor: pointer;
    background: none;
    border: none;
    font-size: 16px;
    transition: background 0.3s;
}

.tablinks.active {
    border-bottom: 2px solid #4CAF50;
}

.pickup-form label {
    display: block;
    margin-top: 10px;
    margin-bottom: 5px;
    font-size: 14px;
}

.pickup-form input, .pickup-form select {
    width: 100%;
    padding: 8px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.form-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.cancel-btn, .submit-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.cancel-btn {
    background-color: #ccc;
    color: white;
}

.submit-btn {
    background-color: #4CAF50;
    color: white;
}

.cancel-btn:hover {
    background-color: #999;
}

.submit-btn:hover {
    background-color: #45a049;
}
</style>
    
<body style="margin: 0;">
    <div style="margin-left: 14vw;">
    <section class="form-section">
                <div class="tab">
                    <button class="tablinks active">Schedule Pickup</button>
                    <button class="tablinks">My Pickups</button>
                </div>
                <form class="pickup-form">
                    <label for="waste-type">Waste Type</label>
                    <select id="waste-type">
                        <option value="general">General</option>
                        <option value="recyclabling">Recycling</option>
                        <option value="hazardous">Hazardous</option>
                    </select>
                    <label for="quantity">Quantity (kg)</label>
                    <input type="number" id="quantity" placeholder="0.0">

                    <label for="date">Date to Apply</label>
                    <input type="date" id="date">

                    <label for="time">Pickup Time</label>
                    <input type="time" id="time">

                    <div class="form-buttons">
                        <button type="button" class="cancel-btn">Cancel</button>
                        <button type="submit" class="submit-btn">Submit</button>
                    </div>
                </form>
            </section>

    </div>
</body>
</html>