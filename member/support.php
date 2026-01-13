<?php
/**
 * support.php
 *
 * Responsibilities:
 * - Display the member support form for authenticated users
 * - Prefill member contact fields from the current session user record
 */

session_start();

$pageTitle = "Member Support";
$tab = "support";

include '../includes/session.php';
include '../includes/settings.php';

/**
 * Auth gate: only authenticated users can access this page.
 */
if (!isset($_SESSION['user'])) {
    header('location: index.php');
    exit;
}

/**
 * Helper for safe HTML output.
 */
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle); ?> | <?php echo e(SYSTEM_NAME); ?></title>

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
                                    <h3><?php echo e($pageTitle); ?></h3>
                                </div>
                                <div class="col-sm-6 pe-0">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="dashboard.php">
                                                <i class="fa fa-home stroke-icon"></i>
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item active"><?php echo e($pageTitle); ?></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php include_once 'includes/alerts.php' ?>

                    <!-- Support Form Start -->
                    <div class='col-lg-5'>
                        <form class="theme-form" method="post" action="sendSupport.php">
                            <div class="row justify-content-center">
                                <div class="col-12 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row justify-content-center">
                                                <div class="col-12 mb-3">
                                                    <h3>Member Information</h3>
                                                </div>

                                                <div class="col-lg-6 form-group mb-3">
                                                    <label>First Name</label>
                                                    <input id="fname"
                                                           type="text"
                                                           class="form-control form-control-lg"
                                                           value="<?php echo e($user['fname'] ?? ''); ?>"
                                                           name="fname">
                                                </div>

                                                <div class="col-lg-6 form-group mb-3">
                                                    <label>Last Name</label>
                                                    <input id="lname"
                                                           type="text"
                                                           class="form-control form-control-lg"
                                                           value="<?php echo e($user['lname'] ?? ''); ?>"
                                                           name="lname">
                                                </div>

                                                <div class="col-lg-12 form-group mb-3">
                                                    <label>Email Address</label>
                                                    <input id="email"
                                                           type="text"
                                                           class="form-control form-control-lg"
                                                           value="<?php echo e($user['email'] ?? ''); ?>"
                                                           name="email">
                                                </div>

                                                <div class="col-lg-12 form-group mb-3">
                                                    <label>Message</label>
                                                    <textarea class="form-control form-control-lg" name="question" rows="7"></textarea>
                                                </div>

                                                <div class="col-lg-8 d-grid">
                                                    <button type="submit" name="sendSupport" class="btn btn-lg btn-primary">
                                                        <i class="fa fa-envelope"></i> Submit Support Email
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- Support Form End -->

                </div>
            </div>
            <!-- Container-fluid Ends-->
        </div>

        <?php include_once 'includes/member-footer.php' ?>

    </div>
</div>

<?php include_once 'includes/js.php' ?>
<?php include_once 'includes/js-plugins.php' ?>
<?php include_once 'includes/js-custom.php' ?>
<?php include_once 'includes/live-chat.php' ?>

<script>new WOW().init();</script>

</body>
</html>
