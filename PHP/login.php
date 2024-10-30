<?php
session_start();

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

// Include PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

// Initialize error messages
$email_error = $address_error = $community_error = $password_error = $general_error = "";
$error_flag = false; // Flag to check for any errors

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    // Handle Registration
    if (isset($_POST['register']))
{
    $fullname = mysqli_real_escape_string($conn, $_POST["fullname"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $phone = mysqli_real_escape_string($conn, $_POST["phone"]);
    $address = mysqli_real_escape_string($conn, $_POST["address"]);
    $community = mysqli_real_escape_string($conn, $_POST["community"]);

    $error_flag = false;

    // Check if email already exists
    $check_email_query = "SELECT * FROM users WHERE email = '$email'";
    $check_email_result = $conn->query($check_email_query);

    if ($check_email_result->num_rows > 0)
    {
        $email_error = "Error: This email is already registered.";
        $error_flag = true;
    }

    if (!empty($address))
    {
        $check_address_query = "SELECT * FROM users WHERE address = '$address'";
        $check_address_result = $conn->query($check_address_query);

        if ($check_address_result->num_rows > 0)
        {
            $address_error = "Error: This address is already in use.";
            $error_flag = true;
        }
    }

    // Detect role based on the name prefix
    if (strpos($fullname, "ADMIN") === 0)
    {
        $role = "ADMIN";

        // Admins must create new communities
        if (!empty($community))
        {
            $check_community_query = "SELECT Com_Id FROM Community WHERE Area = '$community'";
            $check_result = $conn->query($check_community_query);

            if ($check_result->num_rows > 0)
            {
                $community_error = "Error: Community already exists.";
                $error_flag = true;
            }
            else
            {
                // Create the new community without setting the admin_id yet
                $create_community_query = "INSERT INTO Community (Area) VALUES ('$community')";
                if ($conn->query($create_community_query) === TRUE)
                {
                    $community_id = $conn->insert_id; // Retrieve new community ID
                }
                else
                {
                    $general_error = "Error creating community: " . $conn->error;
                    $error_flag = true;
                }
            }
        }
        else
        {
            $community_error = "Admin must create a community.";
            $error_flag = true;
        }
    }
    else
    {
        $role = "USER";
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
                $community_error = "Community not found. You can register without a community.";
                $community_id = NULL;
            }
        }
        else
        {
            $community_id = NULL;
        }
    }

    if (!$error_flag)
    {
        // Generate a random password (8 characters with letters, numbers, and symbols)
        $generated_password = bin2hex(random_bytes(4));

        // Insert user details into the users table
        $sql = "INSERT INTO users (name, email, phone, address, role, com_id, password)
                VALUES ('$fullname', '$email', '$phone', '$address', '$role'," . ($community_id ? $community_id : "NULL") . ", '$generated_password')";

        if ($conn->query($sql) === TRUE)
        {
            $user_id = $conn->insert_id; // Get the new user's ID

            // If the user is an admin, update the Community table with the admin ID
            if ($role == "ADMIN" && isset($community_id))
            {
                $update_community_query = "UPDATE Community SET admin_Id = '$user_id' WHERE Com_Id = '$community_id'";
                if (!$conn->query($update_community_query)) {
                    $general_error = "Error updating community with admin ID: " . $conn->error;
                }
            }

            // Initialize PHPMailer object
            $mail = new PHPMailer(true);

            try
            {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'yapfongkiat53@gmail.com';
                $mail->Password = 'momfaxlauusnbnvl';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom($email, 'Eco Waste System');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = "Your Account Password";
                $mail->Body = "Hello $fullname, your account has been created successfully. Here is your password: $generated_password. Please log in and change it.";

                $mail->send();
                $general_error = "Registration Successful! Check your email for the password.";
            }
            catch (Exception $e)
            {
                $general_error = "Registration successful, but failed to send the email. Error: {$mail->ErrorInfo}";
            }
        }
        else
        {
            $general_error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    else
    {
        $general_error = "Please fix the errors above and try again.";
    }
}



    // Handle Login
    if (isset($_POST['login']))
    {
        $email = mysqli_real_escape_string($conn, $_POST["email"]);
        $password = mysqli_real_escape_string($conn, $_POST["password"]);
        $remember = isset($_POST["remember"]);

        // Check if the user exists
        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0)
        {
            $row = $result->fetch_assoc();

            // Verify password
            if ($password === $row['password'])
            {
                $_SESSION['User_ID'] = $row['User_ID'];
                $_SESSION['email'] = $email;
                $_SESSION['fullname'] = $row['name'];
                $_SESSION['role'] = $row['role']; // Store role in session

                // Check if the password has been changed
                if ($row['is_password_changed'] == 0)
                {
                    // Redirect to dashboard with a flag to force password change
                    $_SESSION['force_password_change'] = true;
                }

                // Set cookies if 'Remember Me' is checked
                if ($remember)
                {
                    setcookie("email", $email, time() + (86400 * 30), "/");
                    setcookie("password", $password, time() + (86400 * 30), "/");
                }
                else
                {
                    // Clear cookies if not remembering
                    if (isset($_COOKIE["email"]))
                    {
                        setcookie("email", "", time() - 3600, "/");
                    }
                    if (isset($_COOKIE["password"]))
                    {
                        setcookie("password", "", time() - 3600, "/");
                    }
                }

                // Redirect based on role after setting up the session
                if ($row['role'] === 'ADMIN')
                {
                    header('Location: adminDashboard.php');  // Redirect admins to admin dashboard
                }
                else
                {
                    header('Location: dashboard.php');  // Redirect users to user dashboard
                }
                exit(); // Ensure no further output is sent
            }
            else
            {
                $login_error = "Incorrect password. Please try again.";
            }
        }
        else
        {
            $login_error = "Account not found. Please register first.";
        }
    }

    if (isset($_POST['forgot_password']))
    {
        $email = mysqli_real_escape_string($conn, $_POST["email"]);

        // Check if the email exists
        $check_email_query = "SELECT * FROM users WHERE email = '$email'";
        $check_email_result = $conn->query($check_email_query);

        if ($check_email_result->num_rows > 0)
        {
            $row = $check_email_result->fetch_assoc(); // Fetch the user data

            // Generate a new random password
            $new_password = bin2hex(random_bytes(4));  // 8-character password (hex encoded)

            // Reset is_password_changed to 0 to enforce password change after login
            $update_password_query = "UPDATE users 
                                  SET password = '$new_password', is_password_changed = 0 
                                  WHERE email = '$email'";

            if ($conn->query($update_password_query) === TRUE)
            {
                // Send password reset email
                $mail = new PHPMailer(true);
                try
                {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'yapfongkiat53@gmail.com';
                    $mail->Password = 'momfaxlauusnbnvl';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom($email, 'Eco Waste System');
                    $mail->addAddress($email);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = "Your Password Reset Request";
                    $mail->Body = "Hello, <br>Your password has been reset. Your new password is: <b>$new_password</b><br>Please log in and change it.";

                    $mail->send();

                    // Force the user to change password after login
                    $_SESSION['force_password_change'] = true;

                    echo "<script>alert('A new password has been sent to your email.'); window.location.href='login.php';</script>";
                }
                catch (Exception $e)
                {
                    echo "Failed to send email. Error: {$mail->ErrorInfo}";
                }
            }
            else
            {
                echo "Error updating password: " . $conn->error;
            }
        }
        else
        {
            echo "<script>alert('Email not found. Please try again.'); window.location.href='login.php';</script>";
        }
    }

}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/login.css?v=1.0">
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
                        <input type="email" id="email" name="email" placeholder="Enter your email"
                            value="<?php echo isset($_COOKIE['email']) ? $_COOKIE['email'] : ''; ?>" required>
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password"
                            value="<?php echo isset($_COOKIE['password']) ? $_COOKIE['password'] : ''; ?>" required>
                    </div>
                    <div class="options">
                        <label><input type="checkbox" name="remember" <?php echo isset($_COOKIE['email']) ? 'checked' : ''; ?>> Remember me</label>
                        <a href="#" id="forgot-password" onclick="showForgotPasswordForm()">Forgot Password?</a>
                    </div>
                    <?php if (!empty($login_error)): ?>
                        <div class="error-message-login"><?php echo $login_error; ?></div>
                    <?php endif; ?>
                    <button type="submit" class="btn-signin">Sign In</button>
                </form>
            </div>
        </div>

        <div id="forgot-password-modal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Reset Password</h2>
                <form method="POST" action="login.php">
                    <input type="hidden" name="forgot_password" value="1">
                    <!-- Hidden input to indicate registration -->
                    <div class="input-group">
                        <label for="reset-email"></label>
                        <input type="email" id="reset-email" name="email" placeholder="Enter your registered email"
                            required>
                    </div>
                    <div class="button-container">
                        <button type="submit" class="btn-reset-password">Submit</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Register Form -->
        <div id="register-form" class="form-container">
            <h2>Register</h2>
            <form method="POST" action="login.php" onsubmit="return validateForm()">
                <input type="hidden" name="register" value="1"> <!-- Hidden input to indicate registration -->

                <!-- Role selection -->
                <div class="input-group role-selection">
                    <label for="role">Register as:</label>
                    <div class="role-buttons-container">
                        <button type="button" id="adminBtn" class="role-btn admin"
                            onclick="selectRole('admin')">Admin</button>
                        <button type="button" id="userBtn" class="role-btn user"
                            onclick="selectRole('user')">User</button>
                    </div>
                    <input type="hidden" id="roleInput" name="role" value="user"> <!-- Hidden input for role -->
                </div>

                <!-- Full Name input -->
                <div class="input-group">
                    <input type="text" name="fullname" id="fullname" placeholder="Full Name" required>
                </div>

                <!-- Email input -->
                <div class="input-group">
                    <input type="email" name="email" placeholder="Email" required>
                    <span class="error-message"><?php echo $email_error; ?></span> <!-- Display email error -->
                </div>

                <!-- Phone input -->
                <div class="input-group">
                    <input type="text" name="phone" placeholder="Phone Number" required>
                </div>

                <!-- Address input -->
                <div class="input-group">
                    <input type="text" name="address" placeholder="Address">
                    <span class="error-message"><?php echo $address_error; ?></span> <!-- Display address error -->
                </div>

                <!-- Community input (will change based on role) -->
                <div class="input-group">
                    <label for="communityField" id="communityLabel">Community</label>

                    <!-- Admin community input -->
                    <input type="text" name="community" id="communityField" placeholder="Enter Community Name">

                    <!-- User community select -->
                    <select name="communitySelect" id="communitySelect" style="display: none;">
                        <option value="">Select a Community</option>
                        <?php
                        // Fetch the list of communities from the database
                        $query = "SELECT Area FROM community"; // Replace 'communities' with your table name
                        $result = mysqli_query($conn, $query);

                        // Populate the dropdown
                        while ($row = mysqli_fetch_assoc($result))
                        {
                            echo '<option value="' . $row['Area'] . '">' . $row['Area'] . '</option>';
                        }
                        ?>
                    </select>
                    <span class="error-message"><?php echo $community_error; ?></span> <!-- Display community error -->
                </div>

                <!-- Submit button -->
                <button type="submit" class="submit-btn">Register</button>
                <span class="general-message"><?php echo $general_error; ?></span> <!-- Display general message -->
            </form>

        </div>
        <?php
        $conn->close();
        ?>

    </div>
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <p id="generalErrorMessage"></p>
            <button id="closeModalBtn">OK</button>
        </div>
    </div>
</body>

<script>
    var generalError = "<?php echo $general_error; ?>";

    // Function to display modal if there is an error
    if (generalError) {
        document.getElementById('generalErrorMessage').innerText = generalError;
        document.getElementById('errorModal').style.display = 'flex';
    }

    // Function to close modal
    document.getElementById("closeModalBtn").addEventListener("click", function () {
        document.getElementById('errorModal').style.display = 'none';
    });

    // Close modal if clicking outside the modal content
    window.onclick = function (event) {
        if (event.target == document.getElementById('errorModal')) {
            document.getElementById('errorModal').style.display = 'none';
        }
    };
</script>
<script src="../Javascript/ManageAccount.js?v=1.0"></script>

</html>