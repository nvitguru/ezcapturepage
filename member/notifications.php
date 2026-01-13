<?php
/**
 * notifications.php
 *
 * Responsibilities:
 * - Authenticated page that lists active system notifications
 * - Provides a link to view a single notification record
 */

session_start();

$pageTitle = "System Notifications";
$tab = "notify";

include_once '../includes/session.php';
include_once '../includes/settings.php';

/**
 * Auth gate: only authenticated users may access this page.
 */
if (!isset($_SESSION['user'])) {
    header('location: index.php');
    exit;
}

/**
 * Open database connection once for the page.
 */
$conn = $pdo->open();

/**
 * Helper for safe HTML output.
 */
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Load active notifications.
 */
$note = [];
$loadError = null;

try {
    $stmt = $conn->prepare("SELECT * FROM `notifications` WHERE `active` = TRUE");
    $stmt->execute();
    $note = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Preserve visible error behavior, but escape for safe HTML output.
    $loadError = e($e->getMessage());
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

                    <!-- Notifications Table -->
                    <div class="col-lg-9">
                        <div class="card">
                            <div class="card-body">

                                <?php if ($loadError): ?>
                                    <div class="alert alert-danger"><?php echo $loadError; ?></div>
                                <?php endif; ?>

                                <div class="table-responsive theme-scrollbar">
                                    <table class="display" id="basic-1">
                                        <thead>
                                        <tr>
                                            <th class="d-none">id</th>
                                            <th>Date</th>
                                            <th>Title</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($note as $row): ?>
                                            <tr>
                                                <td class="d-none"><?php echo e($row['id']); ?></td>
                                                <td>
                                                    <h5><?php echo e(date('M d, Y', strtotime($row['created']))); ?></h5>
                                                </td>
                                                <td>
                                                    <h5><?php echo e($row['title']); ?></h5>
                                                </td>
                                                <td>
                                                    <a href="notification?id=<?php echo e($row['id']); ?>" class="btn btn-outline-primary">
                                                        <i class="fa fa-search"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- /Notifications Table -->

                </div>
            </div>
        </div>

        <?php include_once 'includes/member-footer.php' ?>
    </div>
</div>

<?php
/**
 * Close database connection after page work is complete.
 */
$pdo->close();
?>

<?php include_once 'includes/js.php' ?>
<?php include_once 'includes/js-plugins.php' ?>
<?php include_once 'includes/js-custom.php' ?>
<?php include_once 'includes/live-chat.php' ?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        /**
         * Initialize DataTables for the notifications list.
         * The table ID must match the HTML table element.
         */
        if (typeof $ !== 'undefined' && $('#basic-1').length) {
            $('#basic-1').DataTable({
                order: [[0, 'desc']],
                responsive: true
            });
        }
    });
</script>

<script>new WOW().init();</script>
</body>
</html>
