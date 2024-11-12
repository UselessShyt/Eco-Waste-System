<?php
$filename = basename(__FILE__);
include "header.php";
include "sidebar.php";
include "../SQL_FILE/database.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../CSS/generate-reports.css">
</head>
<body style="margin: 0;">
    <div style="margin-left: 15vw; display: flex;">
        <div class="container">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="generate-report">
                <div class="container2">
                    <label class="label" for="report_type">Select Report Type:&nbsp;</label>
                    <select name="report_type" id="report_type" style="padding: 5px;">
                        <option value="select">--Select--</option>
                        <option value="pickup_statistics">Pickup Statistics</option>
                        <option value="issues_reported">Issues Reported</option>
                        <option value="recycling_rates">Recycling Rates</option>
                    </select>
                </div>
                <div class="container2">
                    <label class="label" for="date_range">Date Range:&nbsp;</label>
                    <input type="date" name="start_date">
                    <input type="date" name="end_date">
                </div>
                <div class="container2">
                    <label class="label" for="area">Area (Optional):&nbsp;</label>
                    <select name="area" id="area">
                        <option value="">All Areas</option>
                        <option value="Jalan A">Jalan A</option>
                        <option value="TestCom">TestCom</option>
                    </select>
                </div>
                <div class="container3">
                    <button type="submit">Generate Report</button>
                </div>
            </form>
        </div>
        <div class="container">
            <div class="container1">
                <canvas id="chart"></canvas>
            </div>
        </div>
    </div>
    <script src="../Javascript/generate-reports.js"></script>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $report_type = $_POST['report_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $area = $_POST['area'];

    $sql = "";

    if ($report_type == 'select'){
        echo "<script>alert('Please select a report type before generating the report.');</script>";
        exit;
    } elseif ($report_type == 'pickup_statistics') {
        $sql = "SELECT us.waste_type, SUM(us.sch_quantity) AS total_quantity
                FROM users_schedule us
                JOIN schedule s ON us.Sch_Id = s.Sch_Id";
        
        $conditions = [];
        if (!empty($start_date) && !empty($end_date)) {
            $conditions[] = "s.`sch-date` BETWEEN '$start_date' AND '$end_date'";
        }
        if (!empty($area)) {
            $conditions[] = "s.Com_Id IN (SELECT Com_Id FROM community WHERE Area = '$area')";
        }
    
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
    
        $sql .= " GROUP BY us.waste_type";
    } elseif ($report_type == 'issues_reported') {
        $sql = "SELECT i.issue_type, COUNT(*) AS issue_count
                FROM issue i";
    
        $conditions = [];
        if (!empty($start_date) && !empty($end_date)) {
            $conditions[] = "i.issue_Date BETWEEN '$start_date' AND '$end_date'";
        }
        if (!empty($area)) {
            $conditions[] = "i.user_id IN (SELECT User_ID FROM users WHERE com_id IN (SELECT Com_Id FROM community WHERE Area = '$area'))";
        }
    
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
    
        $sql .= " GROUP BY i.issue_type";
    } elseif ($report_type == 'recycling_rates') {
        $sql = "SELECT us.waste_type, SUM(us.sch_quantity) AS total_quantity
                FROM users_schedule us
                JOIN schedule s ON us.Sch_Id = s.Sch_Id
                JOIN waste w ON us.waste_type = w.waste_type
                WHERE w.waste_type = 'Recycling'";
        
        $conditions = [];
        if (!empty($start_date) && !empty($end_date)) {
            $conditions[] = "s.`sch-date` BETWEEN '$start_date' AND '$end_date'";
        }
        if (!empty($area)) {
            $conditions[] = "s.Com_Id IN (SELECT Com_Id FROM community WHERE Area = '$area')";
        }
    
        if (!empty($conditions)) {
            $sql .= " AND " . implode(" AND ", $conditions);
        }
    
        $sql .= " GROUP BY us.waste_type";
    }
    

    if (!empty($sql)) {
        $result = $conn->query($sql);
        $response = array();

        if ($result->num_rows > 0) {
            $labels = [];
            $values = [];

            while ($row = $result->fetch_assoc()) {
                if ($report_type == "issues_reported") {
                    $labels[] = $row['issue_type'];
                    } else {
                    $labels[] = $row['waste_type'];
                }
                $values[] = $row['total_quantity'] ?? $row['issue_count'];
            }

            $response['success'] = true;
            if (($values[0]) == "0") {
                echo "<script>alert('There is no data.');</script>";
            }
            $response['chartData'] = [
                'labels' => $labels,
                'values' => $values
            ];
        } else {
            $response['success'] = false;
            echo "<script>alert('There is no data.');</script>";
        }

        echo json_encode($response);
    }
}
?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const chartData = {
        labels: <?php echo json_encode($labels ?? []); ?>,
        datasets: [{
            label: 'Quantity',
            data: <?php echo json_encode($values ?? []); ?>,
            backgroundColor: '#36A2EB',
            borderColor: '#2a7bbd',
            borderWidth: 1
        }]
    };

    const ctx = document.getElementById('chart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: chartData,
        options: {
            responsive: true,
            plugins: {
                tooltip: { enabled: true },
                legend: { display: false }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Type'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total Quantity'
                    }
                }
            }
        }
    });
});
</script>