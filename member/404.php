<?php
/** @var Database $pdo */
session_start();

include 'includes/session.php';
include '../includes/settings.php';

if (!isset($_SESSION['user'])) {
    header('location: index.php');
}

$pageTitle = "404 Error";
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
<body id="captureLogin">
<!-- tap on top starts-->
<div class="tap-top"><i data-feather="chevrons-up"></i></div>
<!-- tap on tap ends-->
<!-- page-wrapper Start-->
<div class="page-wrapper" id="pageWrapper">
    <!-- error-404 start-->
    <div class="error-wrapper">
        <div class="container">
            <div class="error-heading">
                <h2 class="headline font-danger">404</h2>
            </div>
            <div class="col-md-8 offset-md-2">
                <p class="sub-content">The page you are attempting to reach is currently not available. This may be
                    because the page does not exist or has been moved.</p>
            </div>
            <div><a class="btn btn-danger-gradien btn-lg" href="dashboard">BACK TO DASHBOARD</a></div>
        </div>
    </div>
    <!-- error-404 end      -->
</div>

<?php include_once 'includes/js.php' ?>
<?php include_once 'includes/js-plugins.php' ?>
<?php include_once 'includes/js-custom.php' ?>

</body>

</html>