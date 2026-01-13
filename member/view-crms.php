<?php
/**
 * view-crms.php
 *
 * Responsibilities:
 * - Require an authenticated session
 * - Query and display all CRMs for the logged-in user
 * - Provide actions to toggle active status, edit, and delete (when inactive)
 */

/** @var Database $pdo */
session_start();

$pageTitle = "View All CRMs";
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

                    <!-- View All CRMs Start -->
                    <div class='col-lg-8'>
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive theme-scrollbar">
                                    <table class="display" id="dataTable">
                                        <thead>
                                        <tr>
                                            <th class="d-none">id</th>
                                            <th>CRM Name</th>
                                            <th>CRM Type</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        /**
                                         * Fetch CRMs scoped to the logged-in user.
                                         */
                                        try {
                                            $crmStmt = $conn->prepare("SELECT * FROM `crm` WHERE `userID` = :userID");
                                            $crmStmt->execute(['userID' => $user['userID']]);
                                            $crm = $crmStmt->fetchAll();

                                            foreach ($crm as $row) {
                                                /**
                                                 * CRM Type mapping:
                                                 * - 1 = Aweber
                                                 * - 2 = GetResponse
                                                 * - else = Internal CRM
                                                 */
                                                if ((int)$row['crmType'] === 1) {
                                                    $crmType = "Aweber";
                                                } elseif ((int)$row['crmType'] === 2) {
                                                    $crmType = "GetResponse";
                                                } else {
                                                    $crmType = "Internal CRM";
                                                }

                                                /**
                                                 * UI helpers based on active status.
                                                 */
                                                $isActive = !empty($row['active']);
                                                $crmStatus = $isActive
                                                    ? "<span class='badge rounded-pill badge-success'>ACTIVE</span>"
                                                    : "<span class='badge rounded-pill badge-warning'>INACTIVE</span>";

                                                if ($isActive) {
                                                    $statusColor  = "warning";
                                                    $statusIcon   = "fa-pause-circle";
                                                    $toggleAction = "Deactivate"; // informational (not displayed)
                                                    $deleteBtn    = "<li><a href='javascript:void(0)' data-bs-toggle='tooltip' data-bs-placement='auto' title='Attention! This CRM is currently ACTIVE. To delete, please deactivate the CRM first.' disabled><i class='fa fa-trash fa-2x text-dark'></i></a></li>";
                                                } else {
                                                    $statusColor  = "success";
                                                    $statusIcon   = "fa-play-circle";
                                                    $toggleAction = "Publish"; // informational (not displayed)
                                                    $deleteBtn    = "<li><a href='#deleteModal' data-bs-toggle='modal' data-id='". $row['crmID'] ."'><i class='fa fa-trash fa-2x text-dark'></i></a></li>";
                                                }

                                                /**
                                                 * Note: the hidden ID column is used for sorting in DataTables.
                                                 * We use crmID to match the rest of this module’s identifiers.
                                                 */
                                                echo "
                                                <tr>
                                                    <td class='d-none'>" . $row['crmID'] . "</td>
                                                    <td><h5>" . $row['name'] . "</h5></td>
                                                    <td><h5>" . $crmType . "</h5></td>
                                                    <td><h5>$crmStatus</h5></td>
                                                    <td>
                                                        <ul class='action'>
                                                            <li class='text-success me-4'>
                                                                <form method='post' action='toggleCRM.php' class='d-inline'>
                                                                    <input type='hidden' name='crmID' value='" . $row['crmID'] . "'>
                                                                    <button type='submit' name='toggleCRM' class='text-" . $statusColor . "' style='background: none; border: none; padding: 0; cursor: pointer;'>
                                                                        <i class='fa " . $statusIcon . " fa-2x'></i>
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li class='me-4'>
                                                                <a href='crm.php?crmID=" . $row['crmID'] . "'>
                                                                    <i class='fa fa-edit fa-2x'></i>
                                                                </a>
                                                            </li>
                                                            " . $deleteBtn . "
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
                    <!-- View All CRMs End -->

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
                        <h4>You are about to DELETE a form crm! Once deleted, this process can not be undone.</h4>
                    </div>
                    <div class="col-12 text-center mb-5">
                        <h3>Do you wish to proceed?</h3>
                    </div>
                    <div class="col-12 d-grid">
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <button type="button" class="btn btn-dark btn-lg close" data-bs-dismiss="modal" aria-label="Close">
                                <i class='fa fa-times-circle'></i> CANCEL
                            </button>
                            <a href="deleteCRM.php?crmID=" class="btn btn-primary btn-lg">
                                <i class="fa fa-trash"></i> DELETE CRM
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
        /**
         * Initialize DataTable.
         * The hidden first column is used for ordering.
         */
        $("#dataTable").DataTable({
            order: [[0, 'desc']],
            responsive: true
        });

        /**
         * Delete modal: inject the selected crmID into the delete URL.
         */
        $('#deleteModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var crmID = button.data('id');
            var modal = $(this);
            var deleteUrl = "deleteCRM.php?crmID=" + crmID;
            modal.find('.btn-primary').attr('href', deleteUrl);
        });
    });
</script>
<script>new WOW().init();</script>

</body>

</html>
