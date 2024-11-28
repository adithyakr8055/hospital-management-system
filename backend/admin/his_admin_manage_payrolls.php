<?php
session_start();
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();
$aid = $_SESSION['ad_id'];

$totals = [];
$salary_ranges = [];

// Query for total payroll amount
$query = "SELECT SUM(pay_emp_salary) AS total_payroll_amount FROM his_payrolls";
$stmt = $mysqli->prepare($query);
$stmt->execute();
$stmt->bind_result($totals['total_payroll_amount']);
$stmt->fetch();
$stmt->close();

// Query for average salary
$query = "SELECT AVG(pay_emp_salary) AS average_salary FROM his_payrolls";
$stmt = $mysqli->prepare($query);
$stmt->execute();
$stmt->bind_result($totals['average_salary']);
$stmt->fetch();
$stmt->close();

// Query for total employees count
$query = "SELECT COUNT(DISTINCT pay_doc_number) AS total_employees FROM his_payrolls";
$stmt = $mysqli->prepare($query);
$stmt->execute();
$stmt->bind_result($totals['total_employees']);
$stmt->fetch();
$stmt->close();

// Query for highest and lowest salary
$query = "SELECT MAX(pay_emp_salary) AS highest_salary, MIN(pay_emp_salary) AS lowest_salary FROM his_payrolls";
$stmt = $mysqli->prepare($query);
$stmt->execute();
$stmt->bind_result($totals['highest_salary'], $totals['lowest_salary']);
$stmt->fetch();
$stmt->close();

// Query for salary distribution by range
$query = "
    SELECT 
        CASE 
            WHEN pay_emp_salary < 30000 THEN 'Below 30K'
            WHEN pay_emp_salary BETWEEN 30000 AND 50000 THEN '30K-50K'
            WHEN pay_emp_salary BETWEEN 50000 AND 70000 THEN '50K-70K'
            ELSE 'Above 70K'
        END AS salary_range,
        COUNT(*) AS number_of_employees
    FROM his_payrolls
    GROUP BY salary_range
";
$stmt = $mysqli->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $salary_ranges[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<?php include('assets/inc/head.php');?>

<body>
    <!-- Begin page -->
    <div id="wrapper">
        <?php include('assets/inc/nav.php');?>
        <?php include("assets/inc/sidebar.php");?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="page-title">Payroll Aggregate Summary</h4>
                            </div>
                        </div>
                    </div>     

                    <div class="row">
                        <div class="col-12">
                            <div class="card-box">
                                <h4 class="header-title">Payroll Overview</h4>

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Total Payroll Amount</th>
                                            <th>Average Salary</th>
                                            <th>Total Employees</th>
                                            <th>Highest Salary</th>
                                            <th>Lowest Salary</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Rs <?php echo $totals['total_payroll_amount']; ?></td>
                                            <td>Rs <?php echo $totals['average_salary']; ?></td>
                                            <td><?php echo $totals['total_employees']; ?></td>
                                            <td>Rs <?php echo $totals['lowest_salary']; ?></td>
                                            <td>Rs <?php echo $totals['highest_salary']; ?></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <h4 class="header-title">Salary Distribution</h4>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Salary Range</th>
                                            <th>Number of Employees</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($salary_ranges as $range): ?>
                                        <tr>
                                            <td><?php echo $range['salary_range']; ?></td>
                                            <td><?php echo $range['number_of_employees']; ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div> <!-- end card-box -->
                        </div> <!-- end col -->
                    </div>
                </div> <!-- container -->
            </div> <!-- content -->
            <?php include('assets/inc/footer.php');?>
        </div>
    </div>

    <div class="rightbar-overlay"></div>
    <script src="assets/js/vendor.min.js"></script>
    <script src="assets/js/app.min.js"></script>
</body>
</html>
