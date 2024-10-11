<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/login.css?v=1.0">
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
                    <p>———————— &nbsp;&nbsp;&nbsp;&nbsp;  OR   &nbsp;&nbsp;&nbsp;&nbsp;  ————————</p>
                    <a href="register.php" class="btn-create">Create an account</a>
                </form>
            </div>
        </div>

        <!-- Register Form -->
        <div id="register-form" class="form-container">
            <h2>Register</h2>
            <form method="POST" action="register.php">
                <input type="text" name="fullname" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="text" name="phone" placeholder="Phone Number" required>
                <input type="text" name="address" placeholder="Address">
                <input type="text" name="community" placeholder="Community" required>
                <button type="submit">Register</button>
            </form>
        </div>
    </div>

</body>
    <script src="../Javascript/ManageAccount.js?v=1.0"></script>
</html>
