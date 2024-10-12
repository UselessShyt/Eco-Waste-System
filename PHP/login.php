<?php
//Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecodatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error)
{
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $fullname = mysqli_real_escape_string($conn, $_POST["fullname"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $age = mysqli_real_escape_string($conn, $_POST["age"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $phone = mysqli_real_escape_string($conn, $_POST["phone"]);
    $address = mysqli_real_escape_string($conn, $_POST["address"]);
    $community = mysqli_real_escape_string($conn, $_POST["community"]);

    if (strlen($password) < 8 || 
        !preg_match('/[A-Z]/', $password) || 
        !preg_match('/[a-z]/', $password) || 
        !preg_match('/[0-9]/', $password) || 
        !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        echo "Password does not meet the strength requirements.";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Detect role based on the name prefix
    if (strpos($fullname, "ADMIN") === 0)
    {
        $role = "ADMIN";

        // Admins can create new communities
        if (!empty($community))
        {
            // Check if the community already exists
            $check_community_query = "SELECT Com_Id FROM Community WHERE Area = '$community'";
            $check_result = $conn->query($check_community_query);

            if ($check_result->num_rows > 0)
            {
                echo "Error: Community already exists.";
                exit(); // Stop further processing
            }
            else
            {
                // If community does not exist, create it
                $create_community_query = "INSERT INTO Community (Area) VALUES ('$community')";
                if ($conn->query($create_community_query) === TRUE)
                {
                    $community_id = $conn->insert_id; // Get the newly created community ID
                }
                else
                {
                    echo "Error creating community: " . $conn->error;
                    exit();
                }
            }
        }
        else
        {
            echo "Admin must create a community.";
            exit();
        }
    }
    else
    {
        $role = "USER";

        // Check if community exists for users
        if (!empty($community))
        {
            $community_query = "SELECT Com_Id FROM Community WHERE Area = '$community'";
            $community_result = $conn->query($community_query);

            if ($community_result->num_rows > 0)
            {
                $row = $community_result->fetch_assoc();
                $community_id = $row['Com_Id'];
            }
            else
            {
                echo "Community not found. You can register without a community.";
                $community_id = NULL; // Community will be NULL if not found
            }
        }
        else
        {
            $community_id = NULL; // No community provided
        }
    }

    // Insert user details into the users table
    $sql = "INSERT INTO users (name, email, phone, address, age, role, com_id, password)
            VALUES ('$fullname', '$email', '$phone', '$address', '$age', '$role'," . ($community_id ? $community_id : "NULL") . ", '$password')";

    if ($conn->query($sql) === TRUE)
    {
        echo "Registration Successful!";
        header('Location: ManageAccount.php');
        exit();
    }
    else
    {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/login.css">
    <title>Recycling Triangle</title>
</head>

<body>
    <div class="main-container">
        <div class="triangle-container">
            <div class="rectangle red" id="register">Register</div>
            <div class="rectangle green" id="guest">Guest</div>
            <div class="rectangle blue" id="login">Login</div>
        </div>

        <!-- Login Form -->
        <div id="login-form" class="form-container active">
            <div class="login-box">
                <div class="logo">
                    <img src="../Image/logo.png" alt="Eco Waste System Logo">
                    <h1>Eco Waste System</h1>
                </div>
                <form class="login-form" method="POST" action="login.php">
                    <div class="input-group">
                        <label for="email">Your email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <div class="options">
                        <label><input type="checkbox" name="remember"> Remember me</label>
                        <a href="#" id="forgot-password">Forgot Password?</a>
                    </div>
                    <button type="submit" class="btn-signin">Sign In</button>
                    <p>———————— &nbsp;&nbsp;&nbsp;&nbsp; OR &nbsp;&nbsp;&nbsp;&nbsp; ————————</p>
                    <a href="login.php" class="btn-create">Create an account</a>
                </form>
            </div>
        </div>

        <!-- Register Form -->
        <div id="register-form" class="form-container">
            <h2>Register</h2>
            <form method="POST" action="login.php">
                <input type="text" name="fullname" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="age" name="age" placeholder="Age" required>
                <input type="text" name="phone" placeholder="Phone Number" required>
                <input type="text" name="address" placeholder="Address">
                <input type="text" name="community" placeholder="Community" required>
                <input type="password" id="password-register" name="password" placeholder="Password" required>
                <div class="progress-bar">
                    <div class="progress" id="progress-bar"></div>
                </div>
                <p class="password-hint" id="password-hint"></p>
                <button type="submit">Register</button>
            </form>
        </div>
    </div>

</body>
<script src="../Javascript/ManageAccount.js?v=2.0"></script>

</html>