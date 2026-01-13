<?php
/**
 * Plan Subscription Checkout
 *
 * Responsibilities:
 * - Enforce authenticated access
 * - Read requested levelID
 * - Render the checkout summary (plan name + cost) and require cancellation policy agreement
 *
 * Notes:
 * - This file relies on includes that hydrate globals/constants (SYSTEM_NAME, SYSTEM_URL, etc.)
 */

session_start();

// -----------------------------------------------------------------------------
// Page metadata (used by layout/template includes)
// -----------------------------------------------------------------------------
$pageTitle = "Plan Subscription Checkout";

// -----------------------------------------------------------------------------
// Bootstrap / shared includes
// -----------------------------------------------------------------------------
include '../includes/session.php';
include '../includes/settings.php';

// -----------------------------------------------------------------------------
// Auth gate (consistent with other member pages)
// -----------------------------------------------------------------------------
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// -----------------------------------------------------------------------------
// Input: levelID (sanitize + default)
// -----------------------------------------------------------------------------
$levelID = filter_input(INPUT_GET, 'levelID', FILTER_VALIDATE_INT);
$levelID = $levelID !== null && $levelID !== false ? (int)$levelID : 1;

// Optional clamp: only allow known levels (1,2,3). Default to 1 if anything else.
if (!in_array($levelID, [1, 2, 3], true)) {
    $levelID = 1;
}

// -----------------------------------------------------------------------------
// Output escape helper
// -----------------------------------------------------------------------------
$e = static function ($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};

// -----------------------------------------------------------------------------
// Plan mapping (fixes the original = vs == bug)
// -----------------------------------------------------------------------------
if ($levelID === 2) {
    $newLevelName = "Standard Plan";
    $newLevelCost = "$14.99";
} elseif ($levelID === 3) {
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
    <title><?php echo $e($pageTitle); ?> | <?php echo $e(SYSTEM_NAME); ?></title>

    <?php include_once 'includes/css-fonts.php'; ?>
    <?php include_once 'includes/css-plugins.php'; ?>
    <?php include_once 'includes/css.php'; ?>
</head>

<body>
<?php include_once 'includes/loader.php'; ?>
<!-- tap on tap ends-->
<!-- page-wrapper Start-->
<div class="page-wrapper" id="pageWrapper">
    <!-- Page Header Start-->
    <div class="page-header">
        <?php include_once 'includes/member-header.php'; ?>
    </div>
    <!-- Page Header Ends-->
    <!-- Page Body Start-->
    <div class="page-body-wrapper">
        <!-- Page Sidebar Start-->
        <?php include_once 'includes/member-sidebar.php'; ?>
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
                                    <h3><?php echo $e($pageTitle); ?></h3>
                                </div>
                                <div class="col-sm-6 pe-0">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="dashboard.php">
                                                <i class="fa fa-home stroke-icon"></i>
                                            </a></li>
                                        <li class="breadcrumb-item active"><?php echo $e($pageTitle); ?></li>
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
                                        <img src="../images/ez-capture-page-logo.png" class="img-fluid" alt="<?php echo $e(SYSTEM_NAME); ?>">
                                    </div>
                                </div>

                                <!-- NOTE: action intentionally left blank (posts to same URL), preserved from original -->
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
                                                        <li id="product"><?php echo $e($newLevelName); ?> <span id="cost"><?php echo $e($newLevelCost); ?></span></li>
                                                    </ul>
                                                    <ul class="sub-total total">
                                                        <li>Total <span id="total" class="count"><?php echo $e($newLevelCost); ?></span></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" id="refund" type="checkbox" name="refund" required>
                                                <label class="form-check-label" for="refund">I have read & agree to the <?php echo $e(SYSTEM_NAME); ?> <a href="legal" target="_blank">Cancellation Policy</a>.</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col-lg-10 d-grid mb-2">
                                            <button type="submit" class="btn btn-lg btn-primary" name="">
                                                <span id="payBtn"><i class="fa fa-credit-card"></i> GO TO CHECKOUT</span>
                                            </button>
                                        </div>
                                        <div class="col-xl-6 col-lg-8 col-md-10">
                                            <img src="../images/stripe.png" class="img-fluid" alt="Stripe">
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

        <?php include_once 'includes/member-footer.php'; ?>

    </div>
</div>

<?php include_once 'includes/js.php'; ?>
<?php include_once 'includes/js-plugins.php'; ?>
<?php include_once 'includes/js-custom.php'; ?>
<?php include_once 'includes/live-chat.php'; ?>

<script>new WOW().init();</script>

</body>

</html>
