<?php
/**
 * view-pages.php
 *
 * Responsibilities:
 * - Require an authenticated session
 * - Query and display all capture pages for the logged-in user
 * - Show associated form (via content.formID), status, and actions (toggle/edit/delete)
 */

/** @var Database $pdo */
session_start();

$pageTitle = "View All Pages";
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

                    <!-- View All Pages Start -->
                    <div class='col-lg-8'>
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive theme-scrollbar">
                                    <table class="display" id="dataTable">
                                        <thead>
                                        <tr>
                                            <th class="d-none">id</th>
                                            <th>Page Name</th>
                                            <th>Form Name</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        /**
                                         * Fetch pages for the logged-in user.
                                         * Determine associated form via:
                                         * pages.pageID -> content.pageID -> content.formID -> form.formID
                                         */
                                        try {
                                            $pagesStmt   = $conn->prepare("SELECT * FROM `pages` WHERE `userID` = :userID");
                                            $contentStmt = $conn->prepare("SELECT * FROM `content` WHERE `pageID` = :pageID");
                                            $formStmt    = $conn->prepare("SELECT * FROM `form` WHERE `formID` = :formID");

                                            $pagesStmt->execute(['userID' => $user['userID']]);
                                            $pages = $pagesStmt->fetchAll();

                                            foreach ($pages as $row) {

                                                // Default form name if content/form not found
                                                $formName = '—';

                                                // Lookup content by pageID
                                                $contentStmt->execute(['pageID' => $row['pageID']]);
                                                $content = $contentStmt->fetch();

                                                // Lookup form by content.formID (if present)
                                                if ($content && !empty($content['formID'])) {
                                                    $formStmt->execute(['formID' => $content['formID']]);
                                                    $form = $formStmt->fetch();
                                                    if ($form && isset($form['name'])) {
                                                        $formName = $form['name'];
                                                    }
                                                }

                                                /**
                                                 * UI helpers based on active status.
                                                 */
                                                $isActive = !empty($row['active']);
                                                $pagesStatus = $isActive
                                                    ? "<span class='badge rounded-pill badge-success'>ACTIVE</span>"
                                                    : "<span class='badge rounded-pill badge-warning'>INACTIVE</span>";

                                                if ($isActive) {
                                                    $statusColor  = "warning";
                                                    $statusIcon   = "fa-pause-circle";
                                                    $toggleAction = "Deactivate";
                                                    $deleteBtn    = "<li><a href='javascript:void(0)' data-bs-toggle='tooltip' data-bs-placement='auto' title='Attention! This page is currently PUBLISHED. To delete, please deactivate the page first.' disabled><i class='fa fa-trash fa-2x text-dark'></i></a></li>";
                                                } else {
                                                    $statusColor  = "success";
                                                    $statusIcon   = "fa-play-circle";
                                                    $toggleAction = "Publish";
                                                    $deleteBtn    = "<li><a href='#deleteModal' data-bs-toggle='modal' data-id='". $row['pageID'] ."'><i class='fa fa-trash fa-2x text-dark'></i></a></li>";
                                                }

                                                /**
                                                 * Note: hidden ID column is used for sorting.
                                                 * Use pageID (matches toggle/edit/delete identifiers).
                                                 */
                                                echo "
                                                <tr>
                                                    <td class='d-none'>". $row['pageID'] ."</td>
                                                    <td><h5>". $row['name'] ."</h5></td>
                                                    <td><h5>". $formName ."</h5></td>
                                                    <td><h5>$pagesStatus</h5></td>
                                                    <td>
                                                        <ul class='action'>
                                                            <li class='text-success me-4'>
                                                                <form method='post' action='togglePage.php' class='d-inline'>
                                                                    <input type='hidden' name='pageID' value='". $row['pageID'] ."'>
                                                                    <button
                                                                        type='submit'
                                                                        class='text-". $statusColor ."'
                                                                        name='togglePage'
                                                                        style='background: none; border: none; padding: 0; cursor: pointer;'
                                                                        data-bs-toggle='tooltip'
                                                                        data-bs-placement='auto'
                                                                        title='". $toggleAction ." Page'
                                                                    >
                                                                        <i class='fa ". $statusIcon ." fa-2x'></i>
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li class='me-4'>
                                                                <a href='page.php?pageID=". $row['pageID'] ."' data-bs-toggle='tooltip' data-bs-placement='auto' title='Edit Page'>
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
                    <!-- View All Pages End -->

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
                        <h4>You are about to DELETE a capture page! Once deleted, this process can not be undone.</h4>
                    </div>
                    <div class="col-12 text-center mb-5">
                        <h3>Do you wish to proceed?</h3>
                    </div>
                    <div class="col-12 d-grid">
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <button type="button" class="btn btn-dark btn-lg close" data-bs-dismiss="modal" aria-label="Close">
                                <i class='fa fa-times-circle'></i> CANCEL
                            </button>
                            <a href="deletePage.php?pageID=" class="btn btn-primary btn-lg">
                                <i class="fa fa-trash"></i> DELETE PAGE
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

        // Inject selected pageID into delete URL when modal opens
        $('#deleteModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var pageID = button.data('id');
            var modal = $(this);
            var deleteUrl = "deletePage.php?pageID=" + pageID;
            modal.find('.btn-primary').attr('href', deleteUrl);
        });
    });
</script>
<script>new WOW().init();</script>

</body>

</html>
