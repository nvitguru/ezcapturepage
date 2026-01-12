<?php
/** @var Database $pdo */
session_start();

$pageTitle = "System Legal";
$tab = "legal";

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

                    <div class="col-lg-3">
                        <div class="row">
                            <div class='col-12 mb-3 d-grid'>
                                <button id="terms" class="btn btn-lg btn-primary">
                                    Terms & Conditions
                                </button>
                            </div>
                            <div class='col-12 mb-3 d-grid'>
                                <button id="privacy" class="btn btn-lg btn-primary">
                                    Privacy Policy
                                </button>
                            </div>
                            <div class='col-12 mb-3 d-grid'>
                                <button id="cancel" class="btn btn-lg btn-primary">
                                    Cancellation Policy
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class='col-lg-9'>
                        <div class="row justify-content-center">
                            <div class="col-12 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <span id="terms" class="">
                                                <?php include "forms/terms.php" ?>
                                            </span>
                                            <span id="privacy" class="d-none">
                                                <?php include "forms/privacy.php" ?>
                                            </span>
                                            <span id="cancel" class="d-none">
                                                <?php include "forms/cancel.php" ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Get all buttons
        const termsButton = document.getElementById("terms");
        const privacyButton = document.getElementById("privacy");
        const cancelButton = document.getElementById("cancel");

        // Get all content spans
        const termsContent = document.querySelector("span#terms");
        const privacyContent = document.querySelector("span#privacy");
        const cancelContent = document.querySelector("span#cancel");

        // Function to show the selected content and hide others
        function showContent(contentToShow) {
            // Hide all content
            termsContent.classList.add("d-none");
            privacyContent.classList.add("d-none");
            cancelContent.classList.add("d-none");

            // Show the selected content
            contentToShow.classList.remove("d-none");
        }

        // Add event listeners to buttons
        termsButton.addEventListener("click", function() {
            showContent(termsContent);
        });

        privacyButton.addEventListener("click", function() {
            showContent(privacyContent);
        });

        cancelButton.addEventListener("click", function() {
            showContent(cancelContent);
        });
    });
</script>

<script>new WOW().init();</script>

</body>

</html>