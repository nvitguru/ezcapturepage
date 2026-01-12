<?php
/** @var Database $pdo */
session_start();

$pageTitle = "Member Profile";
$tab = "profile";

include '../includes/session.php';
include '../includes/settings.php';

if (!isset($_SESSION['user'])) {
    header('location: index.php');
}

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

                    <!-- Profile Form Start -->
                    <div class='col-lg-5'>
                        <form class="theme-form" method="post" action="editProfile">
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
                                                    <input id="fname" type="text" class="form-control form-control-lg" value="<?php echo $user['fname'] ?>" name="fname">
                                                </div>
                                                <div class="col-lg-6 form-group mb-3">
                                                    <label>Last Name</label>
                                                    <input id="lname" type="text" class="form-control form-control-lg" value="<?php echo $user['lname'] ?>" name="lname">
                                                </div>
                                                <div class="col-lg-12 form-group mb-3">
                                                    <label>Email Address</label>
                                                    <input id="email" type="text" class="form-control form-control-lg" value="<?php echo $user['email'] ?>" name="email">
                                                </div>
                                                <div class="col-lg-8 d-grid">
                                                    <button type="submit" name="editProfile" class="btn btn-lg btn-primary"><i class="fa fa-user-plus"></i> Save Profile</button>
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
                                                            <input id="pincode" type="password" class="form-control form-control-lg" value="<?php echo $user['pincode'] ?>" name="pincode">
                                                            <i id="showPin" class="fa fa-eye" onclick="togglePinVisibility()"></i>
                                                        </div>
                                                        <div class="col-lg-10 d-grid">
                                                            <button type="button" data-bs-toggle="modal" data-bs-target="#pinModal" class="btn btn-lg btn-primary"><i class="fa fa-user-plus"></i> Save Security Pin</button>
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
                                                            <button type="submit" name="editSecurity" class="btn btn-lg btn-primary"><i class="fa fa-user-plus"></i> Save Security Pin</button>
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
            <!-- Container-fluid Ends-->
        </div>

        <?php include_once 'includes/member-footer.php' ?>

    </div>
</div>

<?php include_once 'includes/js.php' ?>
<?php include_once 'includes/js-plugins.php' ?>
<?php include_once 'includes/js-custom.php' ?>
<?php include_once 'includes/live-chat.php' ?>

<script>
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