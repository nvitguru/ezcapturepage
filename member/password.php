<?php
/**
 * password.php
 *
 * Responsibilities:
 * - Authenticated "Manage Password" page
 * - Presents form to change password (current/new/confirm)
 * - Uses client-side show/hide toggles for password fields
 *
 * Notes:
 * - Actual password update logic lives in editPassword.php
 */

/** @var Database $pdo */
session_start();

$pageTitle = "Manage Password";
$tab = "password";

include '../includes/session.php';
include '../includes/settings.php';

/**
 * Auth gate.
 */
if (!isset($_SESSION['user'])) {
    header('location: index.php');
    exit;
}

/**
 * Open DB connection to match site pattern (even if not used here directly).
 */
$conn = $pdo->open();

/**
 * Helper for safe HTML output (defensive / consistent).
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

<div class="page-wrapper" id="pageWrapper">
    <div class="page-header">
        <?php include_once 'includes/member-header.php' ?>
    </div>

    <div class="page-body-wrapper">
        <?php include_once 'includes/member-sidebar.php' ?>

        <div class="page-body">
            <div class="container-fluid"></div>

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

                    <!-- Profile Form Start -->
                    <div class='col-lg-4'>
                        <form class="theme-form" method="post" action="editPassword.php">
                            <div class="row justify-content-center">
                                <div class="col-12 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row justify-content-center">
                                                <div class="col-12 mb-3">
                                                    <h3>Change Password</h3>
                                                </div>

                                                <!-- Current Password -->
                                                <div class="col-12 form-group mb-3 position-relative">
                                                    <label>Current Password</label>
                                                    <input id="currentPass" type="password" class="form-control form-control-lg" name="currentPass">
                                                    <i id="showCurrentPass" class="fa fa-eye" onclick="togglePasswordVisibility('currentPass', 'showCurrentPass')"></i>
                                                </div>

                                                <!-- New Password -->
                                                <div class="col-12 form-group mb-3 position-relative">
                                                    <label>New Password</label>
                                                    <input id="newPass" type="password" class="form-control form-control-lg" name="newPass">
                                                    <i id="showNewPass" class="fa fa-eye" onclick="togglePasswordVisibility('newPass', 'showNewPass')"></i>
                                                </div>

                                                <!-- Confirm Password -->
                                                <div class="col-lg-12 form-group mb-3 position-relative">
                                                    <label>Confirm Password</label>
                                                    <input id="confirmPass" type="password" class="form-control form-control-lg" name="confirmPass">
                                                    <i id="showConfirmPass" class="fa fa-eye" onclick="togglePasswordVisibility('confirmPass', 'showConfirmPass')"></i>
                                                </div>

                                                <div class="col-lg-8 d-grid">
                                                    <button type="submit" name="editPassword" class="btn btn-lg btn-primary">
                                                        <i class="fa fa-shield"></i> Change Password
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                    <!-- Profile Form End -->

                </div>
            </div>
        </div>

        <?php include_once 'includes/member-footer.php' ?>
    </div>
</div>

<?php
/**
 * Close DB connection at end of page lifecycle.
 */
$pdo->close();
?>

<?php include_once 'includes/js.php' ?>
<?php include_once 'includes/js-plugins.php' ?>
<?php include_once 'includes/js-custom.php' ?>
<?php include_once 'includes/live-chat.php' ?>

<script>
    /**
     * togglePasswordVisibility()
     * Simple show/hide toggle for password inputs using FontAwesome icon state.
     */
    function togglePasswordVisibility(inputId, iconId) {
        var pincodeInput = document.getElementById(inputId);
        var showPinIcon = document.getElementById(iconId);

        if (pincodeInput.type === 'password') {
            pincodeInput.type = 'text';
            showPinIcon.classList.remove('fa-eye');
            showPinIcon.classList.add('fa-eye-slash');
        } else {
            pincodeInput.type = 'password';
            showPinIcon.classList.remove('fa-eye-slash');
            showPinIcon.classList.add('fa-eye');
        }
    }
</script>

<script>new WOW().init();</script>

</body>
</html>
