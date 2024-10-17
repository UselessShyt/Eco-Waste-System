<?php
//Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecodatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Include PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

session_start(); // Start a new PHP session

// Initialize error messages
$email_error = $address_error = $community_error = $password_error = $general_error = "";
$error_flag = false; // Flag to check for any errors

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle Registration
    if (isset($_POST['register'])) {
        $fullname = mysqli_real_escape_string($conn, $_POST["fullname"]);
        $email = mysqli_real_escape_string($conn, $_POST["email"]);
        $phone = mysqli_real_escape_string($conn, $_POST["phone"]);
        $address = mysqli_real_escape_string($conn, $_POST["address"]);
        $community = mysqli_real_escape_string($conn, $_POST["community"]);

        // Check if email already exists
        $check_email_query = "SELECT * FROM users WHERE email = '$email'";
        $check_email_result = $conn->query($check_email_query);

        if ($check_email_result->num_rows > 0) {
            $email_error = "Error: This email is already registered.";
            $error_flag = true;
        }

        if (!empty($address)) {
            $check_address_query = "SELECT * FROM users WHERE address = '$address'";
            $check_address_result = $conn->query($check_address_query);

            if ($check_address_result->num_rows > 0) {
                $address_error = "Error: This address is already in use.";
                $error_flag = true;
            }
        }

        // Detect role based on the name prefix
        if (strpos($fullname, "ADMIN") === 0) {
            $role = "ADMIN";

            // Admins can create new communities
            if (!empty($community)) {
                $check_community_query = "SELECT Com_Id FROM Community WHERE Area = '$community'";
                $check_result = $conn->query($check_community_query);

                if ($check_result->num_rows > 0) {
                    $community_error = "Error: Community already exists.";
                    $error_flag = true;
                } else {
                    $create_community_query = "INSERT INTO Community (Area) VALUES ('$community')";
                    if ($conn->query($create_community_query) === TRUE) {
                        $community_id = $conn->insert_id;
                    } else {
                        $general_error = "Error creating community: " . $conn->error;
                        $error_flag = true;
                    }
                }
            } else {
                $community_error = "Admin must create a community.";
                $error_flag = true;
            }
        } else {
            $role = "USER";
            if (!empty($community)) {
                $community_query = "SELECT Com_Id FROM Community WHERE Area = '$community'";
                $community_result = $conn->query($community_query);

                if ($community_result->num_rows > 0) {
                    $row = $community_result->fetch_assoc();
                    $community_id = $row['Com_Id'];
                } else {
                    $community_error = "Community not found. You can register without a community.";
                    $community_id = NULL;
                }
            } else {
                $community_id = NULL;
            }
        }

        if (!$error_flag) {
            // Generate a random password (8 characters with letters, numbers, and symbols)
            $generated_password = bin2hex(random_bytes(4));
            $hashed_password = password_hash($generated_password, PASSWORD_DEFAULT);

            // Insert user details into the users table
            $sql = "INSERT INTO users (name, email, phone, address, role, com_id, password)
                    VALUES ('$fullname', '$email', '$phone', '$address', '$role'," . ($community_id ? $community_id : "NULL") . ", '$hashed_password')";

            if ($conn->query($sql) === TRUE) {
                // Initialize PHPMailer object
                $mail = new PHPMailer(true);

                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';  // Set the SMTP server to send through
                    $mail->SMTPAuth = true;
                    $mail->Username = 'yapfongkiat53@gmail.com'; // Your email address
                    $mail->Password = 'momfaxlauusnbnvl';  // Your Gmail app password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom($email, 'Eco Waste System');
                    $mail->addAddress($email);  // Add the recipient

                    // Content
                    $mail->isHTML(true);  // Set email format to HTML
                    $mail->Subject = "Your Account Password";
                    $mail->Body = "Hello $fullname, your account has been created successfully. Here is your password: $generated_password. Please log in and change it.";

                    $mail->send();
                    $general_error = "Registration Successful! Check your email for the password.";
                } catch (Exception $e) {
                    $general_error = "Registration successful, but failed to send the email. Error: {$mail->ErrorInfo}";
                }
            } else {
                $general_error = "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            $general_error = "Please fix the errors above and try again.";
        }
    }

    // Handle Login
    if (isset($_POST['login'])) {
        $email = mysqli_real_escape_string($conn, $_POST["email"]);
        $password = mysqli_real_escape_string($conn, $_POST["password"]);

        // Check if the user exists
        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $row['password'])) {
                echo "Login successful!";
                header('Location: dashboard.php');  // Redirect to dashboard or another page
                $_SESSION['User_ID'] = $row['User_ID']; // Save the User ID in the session
            } else {
                echo "Incorrect password!";
            }
        } else {
            echo "Account not found. Please register first.";
        }
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
    <script>
        // Use PHP to output a JavaScript variable that indicates whether the user should stay on the registration form
        var showRegisterForm = <?php echo $error_flag ? 'true' : 'false'; ?>;
    </script>
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
                    <img src="../Img/logo.png" alt="Eco Waste System Logo">
                    <h1>Eco Waste System</h1>
                </div>
                <form class="login-form" method="POST" action="login.php">
                    <input type="hidden" name="login" value="1"> <!-- Hidden input to indicate login -->
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
                </form>
            </div>
        </div>

        <!-- Register Form -->
        <div id="register-form" class="form-container">
            <h2>Register</h2>
            <form method="POST" action="login.php" onsubmit="return validateForm()">
                <input type="hidden" name="register" value="1"> <!-- Hidden input to indicate registration -->
                <div class="input-group">
                    <input type="text" name="fullname" placeholder="Full Name" required>
                </div>
                <div class="input-group">
                    <input type="email" name="email" placeholder="Email" required>
                    <span class="error-message"><?php echo $email_error; ?></span> <!-- Display email error -->
                </div>
                <div class="input-group">
                    <input type="text" name="phone" placeholder="Phone Number" required>
                </div>
                <div class="input-group">
                    <input type="text" name="address" placeholder="Address">
                    <span class="error-message"><?php echo $address_error; ?></span> <!-- Display address error -->
                </div>
                <div class="input-group">
                    <input type="text" name="community" placeholder="Community" required>
                    <span class="error-message"><?php echo $community_error; ?></span> <!-- Display community error -->
                </div>
                <button type="submit">Register</button>
                <span class="general-message"><?php echo $general_error; ?></span> <!-- Display general message -->
            </form>
        </div>
    </div>

</body>
<script src="../Javascript/ManageAccount.js?v=1.0"></script>

</html>