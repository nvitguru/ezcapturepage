<?php
/**
 * profile.php
 *
 * Responsibilities:
 * - Authenticated member profile page
 * - Allows member to update basic profile fields (fname/lname/email)
 * - Allows member to update support security pin (requires password confirm via modal)
 *
 * Notes:
 * - Profile update handler: editProfile.php
 * - Security pin update handler: editSecurity.php
 */

/** @var Database $pdo */
session_start();

$pageTitle = "Member Profile";
$tab = "profile";

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
 * Open DB connection to match site pattern (even if not used directly on this page).
 * $user is assumed to be provided by includes/session.php.
 */
$conn = $pdo->open();

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
                    <div class='col-lg-5'>
                        <!--
                            Updates member profile fields.
                            NOTE: action was "editProfile" (no extension). Using "editProfile.php" for consistency with other pages.
                        -->
                        <form class="theme-form" method="post" action="editProfile.php">
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
                                                    <input id="fname" type="text" class="form-control form-control-lg"
                                                           value="<?php echo e($user['fname'] ?? ''); ?>" name="fname">
                                                </div>

                                                <div class="col-lg-6 form-group mb-3">
                                                    <label>Last Name</label>
                                                    <input id="lname" type="text" class="form-control form-control-lg"
                                                           value="<?php echo e($user['lname'] ?? ''); ?>" name="lname">
                                                </div>

                                                <div class="col-lg-12 form-group mb-3">
                                                    <label>Email Address</label>
                                                    <input id="email" type="text" class="form-control form-control-lg"
                                                           value="<?php echo e($user['email'] ?? ''); ?>" name="email">
                                                </div>

                                                <div class="col-lg-8 d-grid">
                                                    <button type="submit" name="editProfile" class="btn btn-lg btn-primary">
                                                        <i class="fa fa-user-plus"></i> Save Profile
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

                    <div class="col-lg-3">
                        <div class="row">

                            <!-- Call-in Pin Form Start -->
                            <div class='col-12'>
                                <!--
                                    Updates the support security pin.
                                    Requires password confirmation via modal before submit.
                                -->
                                <form class="theme-form" method="post" action="editSecurity.php">
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="row justify-content-center">
                                                        <div class="col-12 mb-3">
                                                            <h3>Support Security Pin</h3>
                                                        </div>

                                                        <div class="col-12 form-group mb-3">
                                                            <label>Security Pin (6 - 10 digits)</label>
                                                            <input id="pincode" type="password" class="form-control form-control-lg"
                                                                   value="<?php echo e($user['pincode'] ?? ''); ?>" name="pincode">
                                                            <i id="showPin" class="fa fa-eye" onclick="togglePinVisibility()"></i>
                                                        </div>

                                                        <!-- Opens modal to confirm password -->
                                                        <div class="col-lg-10 d-grid">
                                                            <button type="button" data-bs-toggle="modal" data-bs-target="#pinModal"
                                                                    class="btn btn-lg btn-primary">
                                                                <i class="fa fa-user-plus"></i> Save Security Pin
                                                            </button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pin Modal -->
                                    <div class="modal fade" id="pinModal" tabindex="-1" role="dialog" aria-labelledby="pinModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered zoomIn" role="document">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <div class="row justify-content-center">
                                                        <div class="col-lg-9 mb-5">
                                                            <img src="images/ez-capture-page-logo.png" class="img-fluid">
                                                        </div>

                                                        <div class="col-12 mb-3">
                                                            <h2>Confirm your password to continue.</h2>
                                                        </div>

                                                        <div class="col-12 form-group position-relative mb-3">
                                                            <label>Password</label>
                                                            <input id="password" type="password" class="form-control form-control-lg" name="password">
                                                            <i id="showPassword" class="fa fa-eye" onclick="togglePasswordVisibility()"></i>
                                                        </div>

                                                        <div class="col-lg-10 d-grid">
                                                            <button type="submit" name="editSecurity" class="btn btn-lg btn-primary">
                                                                <i class="fa fa-user-plus"></i> Save Security Pin
                                                            </button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- Call-in Pin Form End -->

                        </div>
                    </div>
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
     * togglePinVisibility()
     * Shows/hides the support pin field.
     */
    function togglePinVisibility() {
        var pincodeInput = document.getElementById('pincode');
        var showPinIcon = document.getElementById('showPin');

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

    /**
     * togglePasswordVisibility()
     * Shows/hides the password confirmation field inside the pin modal.
     */
    function togglePasswordVisibility() {
        var pincodeInput = document.getElementById('password');
        var showPinIcon = document.getElementById('showPassword');

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
