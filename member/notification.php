<?php
/**
 * notifications.php
 *
 * Responsibilities:
 * - Display a single system notification
 * - Restrict access to authenticated users
 * - Load notification content by ID
 */

session_start();

$pageTitle = "System Notification";
$tab = "notify";

include_once '../includes/session.php';
include_once '../includes/settings.php';

/**
 * Auth gate: only authenticated users may view notifications.
 */
if (!isset($_SESSION['user'])) {
    header('location: index.php');
    exit;
}

/**
 * Validate and normalize notification ID.
 */
$noteID = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$noteID) {
    die('Invalid notification ID.');
}

/**
 * Open database connection.
 */
$conn = $pdo->open();

/**
 * Fetch notification record.
 */
$stmt = $conn->prepare(
    "SELECT * FROM `notifications` WHERE `id` = :noteID LIMIT 1"
);
$stmt->execute(['noteID' => $noteID]);
$notify = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$notify) {
    $pdo->close();
    die('Notification not found.');
}

/**
 * Helper for safe plain-text output.
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
                <div class="row widget-grid mb-5">

                    <!-- Page Title -->
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
                                        <li class="breadcrumb-item active">
                                            <?php echo e($pageTitle); ?>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php include_once 'includes/alerts.php' ?>

                    <!-- Notification Content -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">

                                    <!-- Timestamp -->
                                    <div class="col-12 text-end mb-4">
                                        <p>
                                            <?php echo date('M d, Y', strtotime($notify['created'])); ?>
                                        </p>
                                    </div>

                                    <!-- Title -->
                                    <div class="col-12">
                                        <h2>
                                            <?php
                                            /**
                                             * Title content is decoded intentionally
                                             * to allow stored HTML formatting.
                                             */
                                            echo htmlspecialchars_decode($notify['title']);
                                            ?>
                                        </h2>
                                        <hr>
                                    </div>

                                    <!-- Body -->
                                    <div class="col-12 notification-body">
                                        <?php
                                        /**
                                         * Body content is decoded intentionally
                                         * to allow rich text formatting.
                                         */
                                        echo htmlspecialchars_decode($notify['body']);
                                        ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Notification Content -->

                </div>
            </div>
        </div>

        <?php include_once 'includes/member-footer.php' ?>
    </div>
</div>

<?php
/**
 * Close DB connection after page render.
 */
$pdo->close();
?>

<?php include_once 'includes/js.php' ?>
<?php include_once 'includes/js-plugins.php' ?>
<?php include_once 'includes/js-custom.php' ?>
<?php include_once 'includes/live-chat.php' ?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (typeof $ !== 'undefined' && $('#dataTable').length) {
            $('#dataTable').DataTable({
                order: [[0, 'desc']],
                responsive: true
            });
        }
    });
</script>

<script>new WOW().init();</script>
</body>
</html>
