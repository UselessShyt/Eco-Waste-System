<?php
    $filename = basename(__FILE__);
    include "header.php";
    include "sidebar.php";
    include "../SQL_FILE/database.php";

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;

    require '../PHPMailer-master/src/Exception.php';
    require '../PHPMailer-master/src/PHPMailer.php';
    require '../PHPMailer-master/src/SMTP.php';
?>
<script src="../Javascript/Report-Issues.js"></script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Issues</title>
    <link rel="stylesheet" href="../CSS/Report-Issues.css">
    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
    </script>
</head>
<body style="margin: 0;">
    <div style="margin-left: 14vw;">
        <div class="container">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="reportForm">
                <div id="type-c">
                    <label for="type">Type of Issue</label>
                    <select name="dropdown" id="type">
                        <option value="" selected>--- Select a Type ---</option>
                        <option value="operational">Operational Issues</option>
                        <option value="technical">Technical Issues</option>
                        <option value="environmental">Environmental Issues</option>
                        <option value="other">Other Issues</option>
                    </select>
                </div>
                <div id="details-c">
                    <div id="details-c1"><label for="details">Details</label></div>
                    <div id="details-c2"><textarea name="details"></textarea></div>
                </div>
                <input type="submit" value="Submit Report" name="submit">
            </form>
        </div>
        <div class="container">
            <h1>My Reported Issues</h1>
            <?php
                // Fetch data from the table (replace with your actual query)
                $query = "
                    SELECT i.*, u.address AS address
                    FROM issue i
                    JOIN users u ON i.user_id = u.User_ID
                    WHERE i.user_id = $user_id
                ";

                $result = $conn->query($query);

                // Check if there are any results
                if ($result->num_rows > 0) {
                    // Get the number of rows to display
                    $rows_to_display = min(10, $result->num_rows); // Display up to 10 issues

                    // Loop through the results and create a div for each issue
                    for ($i = 0; $i < $rows_to_display; $i++) {
                        $row = $result->fetch_assoc();
                        ?>
                        <div class="reported-issues">
                            <div class="issue" onclick="toggleDetails(this)">
                                <div class="issue-summary">
                                    <div>
                                        <?php
                                            $issuetype = "";
                                            switch ($row['issue_type']) {
                                            case 'operational':
                                                $issuetype = "Operational Issue";
                                                break;
                                            case 'technical':
                                                $issuetype = "Technical Issues";
                                                break;
                                            case 'environmental':
                                                $issuetype = 'Environmental Issues';
                                                break;
                                            case 'other':
                                                $issuetype = 'Other Issues';
                                                break;
                                            }
                                        ?>
                                        <p><strong><?php echo $issuetype; ?></strong></p>
                                        <p><?php echo $row['address']; ?></p>
                                    </div>
                                    <div class="issue-meta">
                                        <?php
                                            $status = '';
                                            switch ($row['status']) {
                                                case 'new':
                                                    $status = 'new';
                                                    break;
                                                case 'in-progress':
                                                    $status = 'in-progress';
                                                    break;
                                                case 'solved':
                                                    $status = 'solved';
                                                    break;
                                            }
                                        ?>
                                        <span id="<?php echo $status?>">
                                            <?php
                                            $status = $row['status'];
                                            switch ($status) {
                                                case 'new':
                                                    echo 'New';
                                                    break;
                                                case 'in-progress':
                                                    echo 'In Progress';
                                                    break;
                                                case 'solved':
                                                    echo 'Solved';
                                                    break;
                                            }
                                            ?>
                                        </span>
                                        <p class="date"><?php echo $row['issue_Date']; ?></p>
                                    </div>
                                </div>
                                <div class="issue-details">
                                    <p><?php echo $row['Description']; ?></p>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    // Display a message if there are no issues
                    ?>
                    <p>No reported issues.</p>
                    <?php
                }
                ?>
        </div>
    </div>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $type = $_POST['dropdown'];
  $details = $_POST['details'];
  $user_id = $_SESSION['User_ID'];
  $email = $_SESSION['email'];
  $fullname = $_SESSION['fullname'];

  if (empty($type)) {
    echo "<script>alert('Please select Type of Issue');</script>";
  } elseif (empty($details)) {
    echo "<script>alert('Details cannot be empty!');</script>";
  } else {
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    $sql = "INSERT INTO issue (issue_type, description, issue_Date, status, user_id) VALUES ('$type', '$details', NOW(), 'new', '$user_id')";
    if ($conn->query($sql)) {
      echo "<script>alert('Issue reported successfully!');</script>";
    } else {
      echo "<script>alert('Error reporting issue. Please try again later.');</script>";
    }
  }

    $mail = new PHPMailer(true);

    try
    {
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
        $mail->Subject = "Notification Of Reported Issue";
        $mail->Body = "Hello $fullname, the reported issues of '$type' is successfully reported. Please wait for our admin to solve the issue. Thanks";

        $mail->send();
        $general_error = "Report Successful! Check your email for the password.";
    }
    catch (Exception $e)
    {
        $general_error = "Report successful, but failed to send the email. Error: {$mail->ErrorInfo}";
    }
}
$conn->close();
?>