<?php
session_start();

$levelID = $_GET['levelID'];

$pageTitle = "Plan Subscription Checkout";

include '../includes/session.php';
include '../includes/settings.php';

if($levelID == 2){
    $newLevelName = "Standard Plan";
    $newLevelCost = "$14.99";
} elseif($levelID = 3) {
    $newLevelName = "Pro Plan";
    $newLevelCost = "$19.99";
} else {
    $newLevelName = "Start-Up Plan";
    $newLevelCost = "$9.99";
}

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

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="row justify-content-center">
                                    <div class="col-lg-7 col-10 mb-5">
                                        <img src="../images/ez-capture-page-logo.png" class="img-fluid">
                                    </div>
                                </div>
                                <form class="theme-form" method="post" action="">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="checkout-details">
                                                <div class="order-box">
                                                    <div class="title-box">
                                                        <div class="checkbox-title">
                                                            <h4>Product </h4><span>Total</span>
                                                        </div>
                                                    </div>
                                                    <ul class="qty sub-total total">
                                                        <li id="product"><?php echo $newLevelName ?> <span id="cost"><?php echo $newLevelCost ?></span></li>
                                                    </ul>
                                                    <ul class="sub-total total">
                                                        <li>Total <span id="total" class="count"><?php echo $newLevelCost ?></span></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" id="refund" type="checkbox" name="refund" required>
                                                <label class="form-check-label" for="refund">I have read & agree to the <?php echo SYSTEM_NAME ?> <a href="legal" target="_blank">Cancellation Policy</a>.</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col-lg-10 d-grid mb-2">
                                            <button type="submit" class="btn btn-lg btn-primary" name=""><span id="payBtn"><i class="fa fa-credit-card"></i> GO TO CHECKOUT</span></button>
                                        </div>
                                        <div class="col-xl-6 col-lg-8 col-md-10">
                                            <img src="../images/stripe.png" class="img-fluid">
                                        </div>
                                    </div>
                                </form>
                            </div>
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

<script>new WOW().init();</script>

</body>

</html>