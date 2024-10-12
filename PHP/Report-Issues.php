<?php
    include "header.php";
    include "sidebar.php";
    include "../SQL_FILE/database.php";
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
                        <option value="technicalIssues">Technical Issues</option>
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
            <div class="reported-issues">
                <div class="issue" onclick="toggleDetails(this)">
                    <div class="issue-summary">
                    <div>
                        <p><strong>Overflowing Bin</strong></p>
                        <p>Main Street, near City Hall</p>
                    </div>
                    <div class="issue-meta">
                        <span class="status new">New</span>
                        <p class="date">Reported on 28th Sept, 2024</p>
                    </div>
                    </div>
                    <div class="issue-details">
                    <p>Details: The bin has not been emptied for 3 days and is overflowing with waste, causing foul smell.</p>
                    </div>
                </div>

                <div class="issue" onclick="toggleDetails(this)">
                    <div class="issue-summary">
                    <div>
                        <p><strong>Littering</strong></p>
                        <p>Central Market, near Pasar Seni</p>
                    </div>
                    <div class="issue-meta">
                        <span class="status in-progress">In Progress</span>
                        <p class="date">Reported on 28th Sept, 2024</p>
                    </div>
                    </div>
                    <div class="issue-details">
                    <p>Details: Frequent littering reported in the area. Trash bins are available but not being used properly.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $type = $_POST['dropdown'];
  $details = $_POST['details'];
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }
  $sql = "INSERT INTO issue (issue_type, description, issue_Date, user_id) VALUES ('$type', '$details', NOW(), '1')";
  if ($conn->query($sql)) {
    echo "<script>alert('Issue reported successfully!');</script>";
  } else {
    echo "<script>alert('Error reporting issue. Please try again later.');</script>";
  }
}
$conn->close();
?>