<?php
/** @var Database $pdo */
session_start();

$pageTitle = "Manage Account";
$tab = "account";

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

                    <!-- View All Forms Start -->
                    <div class='col-lg-7'>
                        <div class="row">
                            <?php include_once '../includes/alerts-static.php' ?>
                            <div class='col-12'>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive theme-scrollbar">
                                            <table class="display" id="dataTable">
                                                <thead>
                                                <tr>
                                                    <th class="d-none">id</th>
                                                    <th>Date</th>
                                                    <th>Amount</th>
                                                    <th>Type</th>
                                                    <th class="text-center">Status</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                $conn = $pdo->open();

                                                try {
                                                    $paymentStmt = $conn->prepare("SELECT * FROM `payments` WHERE `userID` = :userID");
                                                    $paymentStmt->execute(['userID'=> $user['userID']]);
                                                    $pay = $paymentStmt->fetchAll();
                                                    foreach ($pay as $row) {
                                                        $payStatus = $row['payStatus'] ? "<span class='badge rounded-pill badge-success'>APPROVED</span>" : "<span class='badge rounded-pill badge-warning'>DECLINED</span>";
                                                        echo "
                                                <tr>
                                                    <td class='d-none'>". $row['id'] ."</td>
                                                    <td><h5>" . date('M d, Y', strtotime($row['payDate'])) . "</h5></td>
                                                    <td><h5>". $row['payAmount'] ."</h5></td>
                                                    <td><h5>". $row['payType'] ."</h5></td>
                                                    <td class='text-center'><h5>$payStatus</h5></td>
                                                </tr>";
                                                    }
                                                } catch (PDOException $e) {
                                                    echo $e->getMessage();
                                                }

                                                $pdo->close();
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- View All Forms End -->
                    <div class="col-5"  style="border-left: 1px solid #ccc;">
                        <div class="row justify-content-center">
                            <div class="col-lg-9">
                                <div class="card">
                                    <div class="card-header pb-0 border-t-danger ribbon-wrapper">
                                        <div class="ribbon ribbon-secondary ribbon-clip">CURRENT PLAN</div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 text-center">
                                                <h1><?php echo $levelName ?></h1>
                                            </div>
                                            <div class="col-12 text-center mt-2 mb-4">
                                                <h1><?php echo $levelCost ?><small>/mo</small></h1>
                                            </div>
                                            <div class="col-12 text-center">
                                                <h4><?php echo $pageCount ?> Capture Pages</h4>
                                                <hr>
                                                <h4><?php echo $pageCount ?> Submit Forms</h4>
                                                <hr>
                                                <h4><?php echo $pageCount ?> Form CRM's</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <hr>
                            </div>
                            <div class="col-12 mb-3">
                                <h3>Change Subscription Plan</h3>
                            </div>
                            <div class="col-lg-6 <?php echo $user['level'] == 1 ? "d-none" : "" ?>">
                                <div class="card border-t-danger">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 text-center">
                                                <h1>Start-Up Plan</h1>
                                            </div>
                                            <div class="col-12 text-center mt-2 mb-4">
                                                <h1>$9.99<small>/mo</small></h1>
                                            </div>
                                            <div class="col-12 text-center">
                                                <h4>3 Capture Pages</h4>
                                                <hr>
                                                <h4>3 Submit Forms</h4>
                                                <hr>
                                                <h4>3 Form CRM's</h4>
                                                <hr>
                                            </div>
                                            <div class="col-12 d-grid">
                                                <a href="<?php echo $user['level'] > 1 ? "downgrade?levelID=1" : "checkout?levelID=1" ?>" type="button" class="btn btn-lg btn-primary">CHANGE PLAN</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 <?php echo $user['level'] == 2 ? "d-none" : "" ?>">
                                <div class="card border-t-danger">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 text-center">
                                                <h1>Standard Plan</h1>
                                            </div>
                                            <div class="col-12 text-center mt-2 mb-4">
                                                <h1>$14.99<small>/mo</small></h1>
                                            </div>
                                            <div class="col-12 text-center">
                                                <h4>6 Capture Pages</h4>
                                                <hr>
                                                <h4>6 Submit Forms</h4>
                                                <hr>
                                                <h4>6 Form CRM's</h4>
                                                <hr>
                                            </div>
                                            <div class="col-12 d-grid">
                                                <a href="<?php echo $user['level'] > 2 ? "downgrade?levelID=2" : "checkout?levelID=2" ?>" type="button" class="btn btn-lg btn-primary">CHANGE PLAN</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 <?php echo $user['level'] == 3 ? "d-none" : "" ?>">
                                <div class="card border-t-danger">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 text-center">
                                                <h1>Pro Plan</h1>
                                            </div>
                                            <div class="col-12 text-center mt-2 mb-4">
                                                <h1>$19.99<small>/mo</small></h1>
                                            </div>
                                            <div class="col-12 text-center">
                                                <h4>10 Capture Pages</h4>
                                                <hr>
                                                <h4>10 Submit Forms</h4>
                                                <hr>
                                                <h4>10 Form CRM's</h4>
                                                <hr>
                                            </div>
                                            <div class="col-12 d-grid">
                                                <a href="checkout?levelID=3" type="button" class="btn btn-lg btn-primary">CHANGE PLAN</a>
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

        <?php include_once 'includes/member-footer.php' ?>

    </div>
</div>

<?php include_once 'includes/js.php' ?>
<?php include_once 'includes/js-plugins.php' ?>
<?php include_once 'includes/js-custom.php' ?>
<?php include_once 'includes/live-chat.php' ?>

<script>
    $(document).ready(function () {
        $("#dataTable").DataTable({
            order: [[ 0, 'desc' ]],
            responsive: true
        });
    });
</script>

<script>new WOW().init();</script>

</body>

</html>