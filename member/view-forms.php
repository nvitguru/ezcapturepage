<?php
/**
 * view-forms.php
 *
 * Responsibilities:
 * - Require an authenticated session
 * - Query and display all capture forms for the logged-in user
 * - Show CRM association, status, and actions (toggle/edit/delete)
 */

/** @var Database $pdo */
session_start();

$pageTitle = "View All Page Forms";
$tab = "form";

include '../includes/session.php';
include '../includes/settings.php';

/**
 * Auth guard: only allow logged-in members.
 */
if (!isset($_SESSION['user'])) {
    header('location: index.php');
    exit;
}

/**
 * Open database connection.
 */
$conn = $pdo->open();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?> | <?php echo SYSTEM_NAME ?></title>

    <?php include_once 'includes/css-fonts.php' ?>
    <?php include_once 'includes/css-plugins.php' ?>
    <?php include_once 'includes/css.php' ?>
</head>
<body>
<?php include_once 'includes/loader.php' ?>
<!-- tap on tap ends-->
<!-- page-wrapper Start-->
<div class="page-wrapper" id="pageWrapper">
    <!-- Page Header Start-->
    <div class="page-header">
        <?php include_once 'includes/member-header.php' ?>
    </div>
    <!-- Page Header Ends-->
    <!-- Page Body Start-->
    <div class="page-body-wrapper">
        <!-- Page Sidebar Start-->
        <?php include_once 'includes/member-sidebar.php' ?>
        <!-- Page Sidebar Ends-->
        <div class="page-body">
            <div class="container-fluid"></div>
            <!-- Container-fluid starts-->
            <div class="container-fluid dashboard_default">
                <div class="row widget-grid">
                    <div class="col-12">
                        <div class="page-title mt-2">
                            <div class="row">
                                <div class="col-sm-6 ps-0">
                                    <h3><?php echo $pageTitle ?></h3>
                                </div>
                                <div class="col-sm-6 pe-0">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="dashboard.php">
                                                <i class="fa fa-home stroke-icon"></i>
                                            </a></li>
                                        <li class="breadcrumb-item active"><?php echo $pageTitle ?></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php include_once 'includes/alerts.php' ?>

                    <!-- View All Forms Start -->
                    <div class='col-lg-8'>
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive theme-scrollbar">
                                    <table class="display" id="dataTable">
                                        <thead>
                                        <tr>
                                            <th class="d-none">id</th>
                                            <th>Form Name</th>
                                            <th>CRM</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        /**
                                         * Fetch forms scoped to the logged-in user.
                                         * Also fetch the CRM for each form (per current schema: form.crm = crmID).
                                         */
                                        try {
                                            $formStmt = $conn->prepare("SELECT * FROM `form` WHERE `userID` = :userID");
                                            $crmStmt  = $conn->prepare("SELECT * FROM `crm` WHERE `crmID` = :crmID");

                                            $formStmt->execute(['userID'=> $user['userID']]);
                                            $forms = $formStmt->fetchAll();

                                            foreach ($forms as $row) {

                                                // Lookup CRM (may be null/missing if deleted or not assigned)
                                                $crmName = '—';
                                                if (!empty($row['crm'])) {
                                                    $crmStmt->execute(['crmID'=> $row['crm']]);
                                                    $crm = $crmStmt->fetch();
                                                    if ($crm && isset($crm['name'])) {
                                                        $crmName = $crm['name'];
                                                    }
                                                }

                                                /**
                                                 * UI helpers based on active status.
                                                 */
                                                $isActive = !empty($row['active']);
                                                $formStatus = $isActive
                                                    ? "<span class='badge rounded-pill badge-success'>ACTIVE</span>"
                                                    : "<span class='badge rounded-pill badge-warning'>INACTIVE</span>";

                                                if ($isActive) {
                                                    $statusColor  = "warning";
                                                    $statusIcon   = "fa-pause-circle";
                                                    $toggleAction = "Deactivate"; // informational (not displayed)
                                                    $deleteBtn    = "<li><a href='javascript:void(0)' data-bs-toggle='tooltip' data-bs-placement='auto' title='Attention! This form is currently ACTIVE. To delete, please deactivate the form first.' disabled><i class='fa fa-trash fa-2x text-dark'></i></a></li>";
                                                } else {
                                                    $statusColor  = "success";
                                                    $statusIcon   = "fa-play-circle";
                                                    $toggleAction = "Publish"; // informational (not displayed)
                                                    $deleteBtn    = "<li><a href='#deleteModal' data-bs-toggle='modal' data-id='". $row['formID'] ."'><i class='fa fa-trash fa-2x text-dark'></i></a></li>";
                                                }

                                                /**
                                                 * Note: hidden ID column is used for sorting.
                                                 * Use formID (matches toggle/edit/delete identifiers).
                                                 */
                                                echo "
                                                <tr>
                                                    <td class='d-none'>". $row['formID'] ."</td>
                                                    <td><h5>". $row['name'] ."</h5></td>
                                                    <td><h5>". $crmName ."</h5></td>
                                                    <td><h5>$formStatus</h5></td>
                                                    <td>
                                                        <ul class='action'>
                                                            <li class='text-success me-4'>
                                                                <form method='post' action='toggleForm.php' class='d-inline'>
                                                                    <input type='hidden' name='formID' value='". $row['formID'] ."'>
                                                                    <button type='submit' class='text-". $statusColor ."' name='toggleForm' style='background: none; border: none; padding: 0; cursor: pointer;'>
                                                                        <i class='fa ". $statusIcon ." fa-2x'></i>
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li class='me-4'>
                                                                <a href='form.php?formID=". $row['formID'] ."'>
                                                                    <i class='fa fa-edit fa-2x'></i>
                                                                </a>
                                                            </li>
                                                            ". $deleteBtn ."
                                                        </ul>
                                                    </td>
                                                </tr>";
                                            }
                                        } catch (PDOException $e) {
                                            echo $e->getMessage();
                                        }

                                        /**
                                         * Close DB connection via wrapper.
                                         */
                                        $pdo->close();
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- View All Forms End -->

                </div>
            </div>
            <!-- Container-fluid Ends-->
        </div>

        <?php include_once 'includes/member-footer.php' ?>

    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 text-center mb-3">
                        <h1 style="color:#CC0001; font-size: 4rem;"><i class="fa fa-exclamation-triangle"></i> ATTENTION <i class="fa fa-exclamation-triangle"></i></h1>
                    </div>
                    <div class="col-12 text-center mb-3">
                        <h4>You are about to DELETE a capture form! Once deleted, this process can not be undone.</h4>
                    </div>
                    <div class="col-12 text-center mb-5">
                        <h3>Do you wish to proceed?</h3>
                    </div>
                    <div class="col-12 d-grid">
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <button type="button" class="btn btn-dark btn-lg close" data-bs-dismiss="modal" aria-label="Close">
                                <i class='fa fa-times-circle'></i> CANCEL
                            </button>
                            <a href="deleteForm.php?formID=" class="btn btn-primary btn-lg">
                                <i class="fa fa-trash"></i> DELETE FORM
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/js.php' ?>
<?php include_once 'includes/js-plugins.php' ?>
<?php include_once 'includes/js-custom.php' ?>
<?php include_once 'includes/live-chat.php' ?>

<script>
    $(document).ready(function () {
        // Initialize DataTable
        $("#dataTable").DataTable({
            order: [[0, 'desc']],
            responsive: true
        });

        // Inject selected formID into delete URL when modal opens
        $('#deleteModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var formID = button.data('id');
            var modal = $(this);
            var deleteUrl = "deleteForm.php?formID=" + formID;
            modal.find('.btn-primary').attr('href', deleteUrl);
        });
    });
</script>

<script>new WOW().init();</script>

</body>

</html>
