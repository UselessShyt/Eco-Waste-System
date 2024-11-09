<?php
$filename = basename(__FILE__);
include "header.php";
include "sidebar.php";
include "../SQL_FILE/database.php";

// Get the logged-in admin's email or identifier (from the session or another method)
$admin_email = $_SESSION['email']; // Assuming the admin's email is stored in the session

// Fetch the Community ID associated with the logged-in admin
$query = "SELECT C.Com_Id 
          FROM Community C 
          JOIN users U ON U.Com_Id = C.Com_Id 
          WHERE U.email = '$admin_email' AND U.role = 'ADMIN'";

$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    // Fetch the community ID
    $row = mysqli_fetch_assoc($result);
    $community_id = $row['Com_Id']; // Use the community ID from the query result
} else {
    // Error if the admin has no community assigned or is not recognized as admin
    echo "Error: Community ID could not be found. Please log in as an admin.";
    exit;
}

$success_message = ""; // Initialize the success message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['pickup_date'];
    $time = $_POST['pickup_time'];

    $query = "INSERT INTO schedule (`Com_Id`, `sch-date`, `sch-time`) 
              VALUES ('$community_id', '$date', '$time')";
    
    $result = mysqli_query($conn, $query);

    if ($result) {
        $success_message = "Schedule added successfully!";
    } else {
        // Display detailed error for troubleshooting
        echo "Error: " . mysqli_error($conn);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <title>Set Waste Pickup Times</title>
    <style>
        /* Include the CSS styles here */
        .form-container {
            margin-top: 50px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        .form-container label {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            display: block;
            margin-bottom: 10px;
        }

        .form-container input[type="date"],
        .form-container input[type="time"] {
            display: block;
            width: 100%;
            padding: 8px;
            margin-top: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-container input[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .form-container input[type="submit"]:hover {
            background-color: #218838;
        }

        /* Notification modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .modal button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>

<body style="margin: 0;">
    <div style="margin-left: 14vw; padding: 20px;">
        <h2>Set Waste Pickup Times</h2>

        <div class="form-container">
            <form action="" method="POST">
                <input type="hidden" name="community_id" value="<?php echo $community_id; ?>">

                <label for="pickup_date">Select Pickup Date:</label>
                <input type="date" id="pickup_date" name="pickup_date" required>

                <label for="pickup_time">Select Pickup Time:</label>
                <input type="time" id="pickup_time" name="pickup_time" required>

                <input type="submit" value="Set Schedule">
            </form>
        </div>

        <!-- Notification modal -->
        <div id="successModal" class="modal">
            <div class="modal-content">
                <p id="successMessage"></p>
                <button onclick="closeModal()">OK</button>
            </div>
        </div>
    </div>

    <script>
        // Display success message if available
        var successMessage = "<?php echo $success_message; ?>";
        if (successMessage) {
            document.getElementById('successMessage').innerText = successMessage;
            document.getElementById('successModal').style.display = 'flex';
        }

        // Function to close the modal
        function closeModal() {
            document.getElementById('successModal').style.display = 'none';
        }
    </script>
</body>
</html>
