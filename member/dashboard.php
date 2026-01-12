<?php
/** @var Database $pdo */
session_start();

$pageTitle = "Member Dashboard";
$tab = "dashboard";

include '../includes/session.php';
include '../includes/settings.php';

if (!isset($_SESSION['user'])) {
    header('location: index');
}

$conn = $pdo->open();

$now = date('Y-m-d');

if (isset($_SERVER['HTTP_CLIENT_IP']))
    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
else if (isset($_SERVER['HTTP_X_FORWARDED']))
    $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
else if (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
    $ipaddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
    $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
else if (isset($_SERVER['HTTP_FORWARDED']))
    $ipaddress = $_SERVER['HTTP_FORWARDED'];
else if (isset($_SERVER['REMOTE_ADDR']))
    $ipaddress = $_SERVER['REMOTE_ADDR'];
else
    $ipaddress = 'UNKNOWN';


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

                    <div class="col-lg-4 order-md-1 order-lg-2">
                        <div class="row">

                            <!-- Account Status -->
                            <div class="col-12 mb-3">
                                <div class="card quick-view">
                                    <div class="card-body pt-2 <?php echo $user['suspend'] ? "bg-warning" : "bg-success" ?>">
                                        <div class="row">
                                            <div class="col-12 text-center">
                                                <h2 class="<?php echo $user['suspend'] ? "txt-warning" : "txt-success" ?>"><?php echo $user['suspend'] ? "ACCOUNT SUSPENDED" : "ACCOUNT ACTIVE" ?></h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Available CRMs -->
                            <div class="col-lg-6 mb-3">
                                <div class="card quick-view">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <h5>Form CRM's</h5>
                                            </div>
                                            <div class="col-12 mb-2 text-center">
                                                <h2><?php echo $activeCRMs . " out of " . $pageCount ?></h2>
                                            </div>
                                            <div class="col-12 d-grid">
                                                <form method="post" action="createCRM.php">
                                                    <div class="row">
                                                        <div class="col-12 d-grid">
                                                            <button type="submit" name="createCRM" class="btn btn-sm btn-success"><i class="fa fa-plus-circle"></i> CREATE CRM</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Available Forms -->
                            <div class="col-lg-6 mb-3">
                                <div class="card quick-view">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <h5>Capture Forms</h5>
                                            </div>
                                            <div class="col-12 mb-2 text-center">
                                                <h2><?php echo $activeForms . " out of " . $pageCount ?></h2>
                                            </div>
                                            <div class="col-12">
                                                <form method="post" action="createForm.php">
                                                    <div class="row">
                                                        <div class="col-12 d-grid">
                                                            <button type="submit" name="createForm" class="btn btn-sm btn-success"><i class="fa fa-plus-circle"></i> CREATE FORM</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Available Pages -->
                            <div class="col-lg-6 mb-3">
                                <div class="card quick-view">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <h5>Capture Pages</h5>
                                            </div>
                                            <div class="col-12 mb-2 text-center">
                                                <h2><?php echo $activePages . " out of " . $pageCount ?></h2>
                                            </div>
                                            <div class="col-12 d-grid">
                                                <form method="post" action="createPage.php">
                                                    <div class="row">
                                                        <div class="col-12 d-grid">
                                                            <button type="submit" name="createPage" class="btn btn-sm btn-success"><i class="fa fa-plus-circle"></i> CREATE PAGE</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-lg-8" style="border-right: 1px solid #ccc;">
                        <div class="row">

                            <!-- Capture Page Quick View Start -->
                            <?php
                            $conn = $pdo->open();

                            try {
                                $pageStmt = $conn->prepare("SELECT * FROM `pages` WHERE `userID` = :userID");
                                $metricStmt = $conn->prepare("SELECT * FROM `metrics` WHERE `pageID` = :pageID");
                                $pageStmt->execute(['userID' => $user['userID']]);
                                $pages = $pageStmt->fetchAll();

                                foreach ($pages as $row) {
                                    $metricStmt->execute(['pageID' => $row['pageID']]);
                                    $metric = $metricStmt->fetch();

                                    // Ensure metrics exist and prevent division by zero
                                    $views = isset($metric['view']) ? $metric['view'] : 0;
                                    $submits = isset($metric['submit']) ? $metric['submit'] : 0;
                                    $conversion = $views > 0 ? round(($submits / $views) * 100) : 0;

                                    $pageActive = $row['active'] ? "<span class='text-success'><strong>ACTIVE</strong></span>" : "<span class='text-warning'><strong>INACTIVE</strong></span>";
                                    $statusColor = $row['active'] ? "btn-warning" : "btn-success";
                                    $statusText = $row['active'] ? "<i class='fa fa-pause-circle'></i>" : "<i class='fa fa-play-circle'></i>";
                                    $tooltipText = $row['active'] ? "Suspend" : "Publish";

                                    echo "<div class='col-lg-6 col-sm-12 box-col-4 mb-3'>
                                            <div class='card total-earning'>
                                                <div class='card-body'>
                                                    <div class='row'>
                                                        <div class='col-12'>
                                                            <div class='d-flex'>
                                                                <div class='badge bg-light-primary badge-rounded font-primary me-2'><i class='fa fa-globe'></i></div>
                                                                <div class='flex-grow-1'>
                                                                    <p class='my-0'><small>" . $pageActive . "</small></p>
                                                                    <h3>" . htmlspecialchars($row['name']) . "</h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class='col text-center'>
                                                            <h5 class='mb-1'>" . htmlspecialchars($views) . "</h5>
                                                            <p class='mt-0'>Visitors</p>
                                                        </div>
                                                        <div class='col text-center'>
                                                            <h5 class='mb-1'>" . htmlspecialchars($submits) . "</h5>
                                                            <p class='mt-0'>Submissions</p>
                                                        </div>
                                                        <div class='col text-center'>
                                                            <h5 class='mb-1'>" . htmlspecialchars($conversion) . "%</h5>
                                                            <p class='mt-0'>Conversions</p>
                                                        </div>
                                                        <div id='pageLink" . $row['pageID'] . "' class='input-group input-group-lg col-12 mt-3 theme-form d-none'>
                                                            <input class='form-control' id='link" . $row['pageID'] . "' value='https://page." . SYSTEM_URL . "/" . $row['pageID'] . "' readonly>
                                                            <button class='btn btn-secondary' type='button'
                                                                    id='button-addon" . $row['pageID'] . "' data-toggle='tooltip'
                                                                    data-placement='top' title='Copy Link' onclick='myFunction" . $row['pageID'] . "()'><i class='fa fa-copy'></i></button>
                                                        </div>
                                                        <div class='col-12'><hr></div>
                                                        <div class='col-12 d-grid'>
                                                            <div class='btn-group' role='group' aria-label='Basic example'>
                                                              <a href='page?pageID=" . $row['pageID'] . "' class='btn btn-primary btn-lg' type='button' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit Capture Page'><i class='fa fa-edit'></i></a>
                                                              <a href='example?pageID=" . $row['pageID'] . "' target='_blank' class='btn btn-secondary btn-lg' type='button' data-bs-toggle='tooltip' data-bs-placement='top' title='View Capture Page'><i class='fa fa-search'></i></a>
                                                              <button id='showLink" . $row['pageID'] . "' type='button' class='btn btn-info btn-lg' data-bs-toggle='tooltip' data-bs-placement='top' title='Get Capture Page Link'><i class='fa fa-link'></i></button>
                                                              <a href='toggleDashPage.php?pageID=". $row['pageID'] ."' class='btn " . $statusColor . " btn-lg' type='button' data-bs-toggle='tooltip' data-bs-placement='top' title='". $tooltipText ." Capture Page'>" . $statusText . "</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>";

                                    echo "<script>
                                            function myFunction" . $row['pageID'] . "() {
                                                var copyText = document.getElementById('link" . $row['pageID'] . "');
                                                copyText.select();
                                                copyText.setSelectionRange(0, 99999); /* For mobile devices */
                                                document.execCommand('copy');
                                                alert('Copied the link: ' + copyText.value);
                                            }
                                            </script>";
                                }
                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage();
                            }

                            $pdo->close();
                            ?>



                            <!-- Capture Page Quick View End -->
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
    document.addEventListener('DOMContentLoaded', function () {
        const showLinkButtons = document.querySelectorAll('[id^="showLink"]');

        showLinkButtons.forEach(button => {
            button.addEventListener('click', function () {
                const card = button.closest('.card');

                if (card) {
                    const pageLink = card.querySelector('.input-group');

                    if (pageLink) {
                        pageLink.classList.toggle('d-none');
                    }
                }
            });
        });
    });
</script>

<script>new WOW().init();</script>
</body>

</html>