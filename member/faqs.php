<?php
/** @var Database $pdo */
session_start();

$pageID = $_GET['pageID'];

$pageTitle = "Frequently Asked Questions";
$tab = "faq";

include '../includes/session.php';
include '../includes/settings.php';

if (!isset($_SESSION['user'])) {
    header('location: index');
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

                    <!-- Create Page Form Start -->
                    <div class='col-lg-8 box-col-4'>

                        <div class="row">
                            <div class="col-12">
                                <div class='card total-earning'>
                                    <div class='card-body'>
                                        <div class='row'>
                                            <div class="col-12 mb-2">
                                                <h3>General Questions</h3>
                                            </div>
                                            <div class='col-12'>
                                                <div class="accordion dark-accordion" id="generalAccordion">

                                                    <!-- General Information Start -->
                                                    <?php
                                                    $conn = $pdo->open();

                                                    try {
                                                        $generalStmt = $conn->prepare("SELECT * FROM `faqs` WHERE `category` = 1");
                                                        $generalStmt->execute();
                                                        $general = $generalStmt->fetchAll();
                                                        foreach ($general as $gen) {
                                                            echo "
                                                                <div class='accordion-item'>
                                                                    <h2 class='accordion-header' id='heading". $gen['id'] ."'>
                                                                        <button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#accord". $gen['id'] ."' aria-expanded='true' aria-controls='accord". $gen['id'] ."'>". $gen['question'] ."<i class='fa fa-chevron-down svg-color'></i></button>
                                                                    </h2>
                                                                    <div class='accordion-collapse collapse' id='accord". $gen['id'] ."' aria-labelledby='heading". $gen['id'] ."' data-bs-parent='#generalAccordion'>
                                                                        <div class='accordion-body'>
                                                                            <div class='row'>
                                                                                <div class='col-12'>
                                                                                    <p style='font-size: 18px'>". $gen['answer'] ."</p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                ";
                                                            }
                                                        } catch (PDOException $e) {
                                                            echo $e->getMessage();
                                                        }

                                                        $pdo->close();
                                                        ?>
                                                    <!-- General Information End -->

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class='card total-earning'>
                                    <div class='card-body'>
                                        <div class='row'>
                                            <div class="col-12 mb-2">
                                                <h3>Page Builder Questions</h3>
                                            </div>
                                            <div class='col-12'>
                                                <div class="accordion dark-accordion" id="builderAccordion">

                                                    <!-- General Information Start -->
                                                    <?php
                                                    $conn = $pdo->open();

                                                    try {
                                                        $builderStmt = $conn->prepare("SELECT * FROM `faqs` WHERE `category` = 2");
                                                        $builderStmt->execute();
                                                        $builder = $builderStmt->fetchAll();
                                                        foreach ($builder as $build) {
                                                            echo "
                                                                <div class='accordion-item'>
                                                                    <h2 class='accordion-header' id='heading". $build['id'] ."'>
                                                                        <button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#accord". $build['id'] ."' aria-expanded='true' aria-controls='accord". $build['id'] ."'>". $build['question'] ."<i class='fa fa-chevron-down svg-color'></i></button>
                                                                    </h2>
                                                                    <div class='accordion-collapse collapse' id='accord". $build['id'] ."' aria-labelledby='heading". $build['id'] ."' data-bs-parent='#builderAccordion'>
                                                                        <div class='accordion-body'>
                                                                            <div class='row'>
                                                                                <div class='col-12'>
                                                                                    <p style='font-size: 18px'>". $build['answer'] ."</p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                ";
                                                        }
                                                    } catch (PDOException $e) {
                                                        echo $e->getMessage();
                                                    }

                                                    $pdo->close();
                                                    ?>
                                                    <!-- General Information End -->

                                                </div>
                                            </div>
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

        <?php include_once '../includes/member-footer.php' ?>

    </div>
</div>

<?php include_once 'includes/js.php' ?>
<?php include_once 'includes/js-plugins.php' ?>
<?php include_once 'includes/js-custom.php' ?>
<?php include_once 'includes/live-chat.php' ?>

<script>new WOW().init();</script>

</body>

</html>