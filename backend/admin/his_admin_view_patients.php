<?php
session_start();
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();
$aid = $_SESSION['ad_id'];
?>

<!DOCTYPE html>
<html lang="en">
    
<?php include('assets/inc/head.php'); ?>

<body>

    <!-- Begin page -->
    <div id="wrapper">

        <!-- Topbar Start -->
        <?php include('assets/inc/nav.php'); ?>
        <!-- end Topbar -->

        <!-- ========== Left Sidebar Start ========== -->
        <?php include("assets/inc/sidebar.php"); ?>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">
                    
                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Patients</a></li>
                                        <li class="breadcrumb-item active">View Patients</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Patient Details</h4>
                            </div>
                        </div>
                    </div>     
                    <!-- end page title --> 

                    <div class="row">
                        <div class="col-12">
                            <div class="card-box">
                                <h4 class="header-title"></h4>
                                <div class="mb-2">
                                    <div class="row">
                                        <div class="col-12 text-sm-center form-inline" >
                                            <div class="form-group mr-2" style="display:none">
                                                <select id="demo-foo-filter-status" class="custom-select custom-select-sm">
                                                    <option value="">Show all</option>
                                                    <option value="Discharged">Discharged</option>
                                                    <option value="OutPatients">OutPatients</option>
                                                    <option value="InPatients">InPatients</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <input id="demo-foo-search" type="text" placeholder="Search" class="form-control form-control-sm" autocomplete="on">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="table-responsive">
                                    <table id="demo-foo-filtering" class="table table-bordered toggle-circle mb-0" data-page-size="7">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th data-toggle="true">Name</th>
                                            <th data-hide="phone">Number</th>
                                            <th data-hide="phone">Address</th>
                                            <th data-hide="phone">Phone</th>
                                            <th data-hide="phone">Age</th>
                                            <th data-hide="phone">Category</th>
                                            <th data-hide="phone">Recent Vitals</th>
                                            <th data-hide="phone">Last Prescription Date</th>
                                            <th data-hide="phone">Medical Record Date</th>
                                            <th data-hide="phone">Action</th>
                                        </tr>
                                        </thead>
                                        <?php
                                            $ret = "
                                                SELECT 
                                                    p.pat_id, p.pat_fname, p.pat_lname, p.pat_number, 
                                                    p.pat_addr, p.pat_phone, p.pat_age, p.pat_type,
                                                    v.vit_bodytemp AS recent_body_temp, 
                                                    v.vit_heartpulse AS recent_heart_pulse, 
                                                    v.vit_resprate AS recent_resp_rate, 
                                                    v.vit_bloodpress AS recent_blood_pressure,
                                                    pr.pres_date AS last_prescription_date,
                                                    mdr.mdr_date_rec AS medical_record_date
                                                FROM 
                                                    his_patients AS p
                                                LEFT JOIN 
                                                    his_vitals AS v ON p.pat_number = v.vit_pat_number
                                                LEFT JOIN 
                                                    his_prescriptions AS pr ON p.pat_number = pr.pres_pat_number
                                                LEFT JOIN 
                                                    his_medical_records AS mdr ON p.pat_number = mdr.mdr_pat_number
                                                ORDER BY 
                                                    p.pat_fname, p.pat_lname
                                            ";
                                            $stmt = $mysqli->prepare($ret);
                                            $stmt->execute();
                                            $res = $stmt->get_result();
                                            $cnt = 1;
                                            while($row = $res->fetch_object()) {
                                        ?>

                                        <tbody>
                                        <tr>
                                            <td><?php echo $cnt; ?></td>
                                            <td><?php echo $row->pat_fname . " " . $row->pat_lname; ?></td>
                                            <td><?php echo $row->pat_number; ?></td>
                                            <td><?php echo $row->pat_addr; ?></td>
                                            <td><?php echo $row->pat_phone; ?></td>
                                            <td><?php echo $row->pat_age; ?> Years</td>
                                            <td><?php echo $row->pat_type; ?></td>
                                            <td>
                                                Temp: <?php echo $row->recent_body_temp; ?> °C, 
                                                Pulse: <?php echo $row->recent_heart_pulse; ?> bpm, 
                                                Resp: <?php echo $row->recent_resp_rate; ?> bpm, 
                                                BP: <?php echo $row->recent_blood_pressure; ?>
                                            </td>
                                            <td><?php echo $row->last_prescription_date; ?></td>
                                            <td><?php echo $row->medical_record_date; ?></td>
                                            
                                            <td>
                                                <a href="his_admin_view_single_patient.php?pat_id=<?php echo $row->pat_id; ?>&&pat_number=<?php echo $row->pat_number; ?>" class="badge badge-success">
                                                    <i class="mdi mdi-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                        </tbody>
                                        <?php $cnt = $cnt + 1; } ?>
                                        <tfoot>
                                        <tr class="active">
                                            <td colspan="11">
                                                <div class="text-right">
                                                    <ul class="pagination pagination-rounded justify-content-end footable-pagination m-t-10 mb-0"></ul>
                                                </div>
                                            </td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div> <!-- end .table-responsive-->
                            </div> <!-- end card-box -->
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->

                </div> <!-- container -->

            </div> <!-- content -->

            <!-- Footer Start -->
             <?php include('assets/inc/footer.php'); ?>
            <!-- end Footer -->

        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- Vendor js -->
    <script src="assets/js/vendor.min.js"></script>

    <!-- Footable js -->
    <script src="assets/libs/footable/footable.all.min.js"></script>

    <!-- Init js -->
    <script src="assets/js/pages/foo-tables.init.js"></script>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>
    
</body>

</html>
