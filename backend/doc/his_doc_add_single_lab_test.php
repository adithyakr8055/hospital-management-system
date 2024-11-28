<?php
session_start();
include('assets/inc/config.php');

if (isset($_POST['add_patient_lab_test'])) {
    // Retrieve form data
    $lab_pat_name = $_POST['lab_pat_name'];
    $lab_pat_ailment = $_POST['lab_pat_ailment'];
    $lab_pat_number  = $_POST['lab_pat_number'];
    $lab_pat_tests = $_POST['lab_pat_tests'];
    $lab_number  = $_POST['lab_number'];

    // Call the stored procedure to insert lab test details
    try {
        // Prepare the SQL call to the stored procedure
        $query = "CALL AddPatientLabTest(?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query);

        if ($stmt === false) {
            throw new Exception("Error preparing statement: " . $mysqli->error);
        }

        // Bind parameters
        $stmt->bind_param('sssss', $lab_pat_name, $lab_pat_ailment, $lab_pat_number, $lab_pat_tests, $lab_number);

        // Execute the stored procedure
        if ($stmt->execute()) {
            $success = "Patient Laboratory Tests Added Successfully";
        } else {
            throw new Exception("Error executing statement: " . $stmt->error);
        }

    } catch (Exception $e) {
        // Handle specific MySQL trigger errors
        if ($mysqli->errno == 1644) {
            $err = "Trigger Error: " . $e->getMessage(); // Specific trigger error handling
        } else {
            $err = "Database Error: " . $e->getMessage(); // General error handling
        }
    }

    // Close the statement
    $stmt->close();
}
?>
<!-- End of Server-Side Code -->

<!DOCTYPE html>
<html lang="en">
    <?php include('assets/inc/head.php'); ?>
    <body>
        <div id="wrapper">
            <?php include("assets/inc/nav.php"); ?>
            <?php include("assets/inc/sidebar.php"); ?>

            <?php
                // Fetch patient details
                $pat_number = $_GET['pat_number'];
                $ret = "SELECT * FROM his_patients WHERE pat_number = ?";
                $stmt = $mysqli->prepare($ret);
                $stmt->bind_param('s', $pat_number);
                $stmt->execute();
                $res = $stmt->get_result();

                while ($row = $res->fetch_object()) {
            ?>
            <div class="content-page">
                <div class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="his_admin_dashboard.php">Dashboard</a></li>
                                            <li class="breadcrumb-item"><a href="javascript:void(0);">Laboratory</a></li>
                                            <li class="breadcrumb-item active">Add Lab Test</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Add Lab Test</h4>
                                </div>
                            </div>
                        </div>     

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="header-title">Fill all fields</h4>
                                        <?php if (isset($success)) { echo "<div class='alert alert-success'>$success</div>"; } ?>
                                        <?php if (isset($err)) { echo "<div class='alert alert-danger'>$err</div>"; } ?>
                                        <form method="post">
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="inputEmail4" class="col-form-label">Patient Name</label>
                                                    <input type="text" required="required" readonly name="lab_pat_name" 
                                                           value="<?php echo $row->pat_fname . ' ' . $row->pat_lname; ?>" 
                                                           class="form-control">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="inputPassword4" class="col-form-label">Patient Ailment</label>
                                                    <input required="required" type="text" readonly name="lab_pat_ailment" 
                                                           value="<?php echo $row->pat_ailment; ?>" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-12">
                                                    <label for="inputEmail4" class="col-form-label">Patient Number</label>
                                                    <input type="text" required="required" readonly name="lab_pat_number" 
                                                           value="<?php echo $row->pat_number; ?>" class="form-control">
                                                </div>
                                            </div>
                                            
                                            <hr>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <?php 
                                                        // Generate a random lab test number
                                                        $length = 5;    
                                                        $lab_number = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, $length);
                                                    ?>
                                                    <label for="inputZip" class="col-form-label">Lab Test Number</label>
                                                    <input type="text" name="lab_number" value="<?php echo $lab_number; ?>" 
                                                           class="form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputAddress" class="col-form-label">Laboratory Tests</label>
                                                <textarea required="required" type="text" class="form-control" 
                                                          name="lab_pat_tests" id="editor"></textarea>
                                            </div>
                                            <button type="submit" name="add_patient_lab_test" 
                                                    class="ladda-button btn btn-success" data-style="expand-right">
                                                Add Laboratory Test
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include('assets/inc/footer.php'); ?>
            </div>
            <?php } ?>
        </div>

        <div class="rightbar-overlay"></div>
        <script src="//cdn.ckeditor.com/4.6.2/basic/ckeditor.js"></script>
        <script type="text/javascript">
        CKEDITOR.replace('editor');
        </script>
        <script src="assets/js/vendor.min.js"></script>
        <script src="assets/js/app.min.js"></script>
        <script src="assets/libs/ladda/spin.js"></script>
        <script src="assets/libs/ladda/ladda.js"></script>
        <script src="assets/js/pages/loading-btn.init.js"></script>
    </body>
</html>
