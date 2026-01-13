<?php
/**
 * Manage Account
 *
 * Responsibilities:
 * - Enforce authenticated access
 * - Display payment history (DataTable)
 * - Display current subscription plan and available plan changes
 *
 * Notes:
 * - Relies on variables hydrated by included files (ex: $pdo, $user, $levelName, $levelCost, $pageCount).
 */

/** @var Database $pdo */
session_start();

// -----------------------------------------------------------------------------
// Page metadata (used by layout/template includes)
// -----------------------------------------------------------------------------
$pageTitle = "Manage Account";
$tab = "account";

// -----------------------------------------------------------------------------
// Bootstrap / shared includes
// -----------------------------------------------------------------------------
include '../includes/session.php';
include '../includes/settings.php';

// -----------------------------------------------------------------------------
// Auth gate: account management requires an authenticated session
// -----------------------------------------------------------------------------
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// -----------------------------------------------------------------------------
// DB connection
// -----------------------------------------------------------------------------
$conn = $pdo->open();

// -----------------------------------------------------------------------------
// Small HTML escape helper (consistent output encoding)
// -----------------------------------------------------------------------------
$e = static function ($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};
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

                    <!-- -----------------------------------------------------------------
                         Page title + breadcrumbs
                         ----------------------------------------------------------------- -->
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
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item active"><?php echo $e($pageTitle); ?></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- -----------------------------------------------------------------
                         Left column: payment history
                         ----------------------------------------------------------------- -->
                    <div class="col-lg-7">
                        <div class="row">

                            <!-- Static alerts (kept as-is) -->
                            <?php include_once '../includes/alerts-static.php'; ?>

                            <div class="col-12">
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
                                                try {
                                                    $paymentStmt = $conn->prepare("
                                                        SELECT *
                                                        FROM `payments`
                                                        WHERE `userID` = :userID
                                                        ORDER BY `id` DESC
                                                    ");
                                                    $paymentStmt->execute(['userID' => $user['userID']]);

                                                    $payments = $paymentStmt->fetchAll();

                                                    foreach ($payments as $row) {
                                                        $id = (int)$row['id'];

                                                        // payStatus appears to be 0/1. Normalize to boolean.
                                                        $isApproved = !empty($row['payStatus']);

                                                        $payStatusHtml = $isApproved
                                                            ? "<span class='badge rounded-pill badge-success'>APPROVED</span>"
                                                            : "<span class='badge rounded-pill badge-warning'>DECLINED</span>";

                                                        // Keep display formatting consistent with the original output
                                                        $dateLabel = '';
                                                        if (!empty($row['payDate'])) {
                                                            $ts = strtotime((string)$row['payDate']);
                                                            $dateLabel = $ts ? date('M d, Y', $ts) : (string)$row['payDate'];
                                                        }

                                                        echo "
                                                            <tr>
                                                                <td class='d-none'>" . $e($id) . "</td>
                                                                <td><h5>" . $e($dateLabel) . "</h5></td>
                                                                <td><h5>" . $e($row['payAmount']) . "</h5></td>
                                                                <td><h5>" . $e($row['payType']) . "</h5></td>
                                                                <td class='text-center'><h5>{$payStatusHtml}</h5></td>
                                                            </tr>
                                                        ";
                                                    }
                                                } catch (PDOException $ex) {
                                                    // Don't leak DB details to the UI
                                                    error_log("Account payments load error (userID=" . ($user['userID'] ?? 'UNKNOWN') . "): " . $ex->getMessage());
                                                    echo "<tr><td colspan='5'><div class='alert alert-danger mb-0'>Unable to load payment history right now.</div></td></tr>";
                                                }

                                                // Close after page logic completes (kept consistent with the rest of the system)
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

                    <!-- -----------------------------------------------------------------
                         Right column: current plan + available plan changes
                         ----------------------------------------------------------------- -->
                    <div class="col-5" style="border-left: 1px solid #ccc;">
                        <div class="row justify-content-center">

                            <!-- Current plan summary -->
                            <div class="col-lg-9">
                                <div class="card">
                                    <div class="card-header pb-0 border-t-danger ribbon-wrapper">
                                        <div class="ribbon ribbon-secondary ribbon-clip">CURRENT PLAN</div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 text-center">
                                                <h1><?php echo $e($levelName); ?></h1>
                                            </div>
                                            <div class="col-12 text-center mt-2 mb-4">
                                                <h1><?php echo $e($levelCost); ?><small>/mo</small></h1>
                                            </div>
                                            <div class="col-12 text-center">
                                                <h4><?php echo $e($pageCount); ?> Capture Pages</h4>
                                                <hr>
                                                <h4><?php echo $e($pageCount); ?> Submit Forms</h4>
                                                <hr>
                                                <h4><?php echo $e($pageCount); ?> Form CRM's</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <hr>
                            </div>

                            <!-- Plan change CTA section -->
                            <div class="col-12 mb-3">
                                <h3>Change Subscription Plan</h3>
                            </div>

                            <?php
                            // Normalize level to int for comparisons (defensive; keeps same behavior)
                            $currentLevel = isset($user['level']) ? (int)$user['level'] : 0;
                            ?>

                            <!-- Start-Up Plan (hidden if already on level 1) -->
                            <div class="col-lg-6 <?php echo $currentLevel === 1 ? 'd-none' : ''; ?>">
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
                                                <a
                                                        href="<?php echo $currentLevel > 1 ? 'downgrade?levelID=1' : 'checkout?levelID=1'; ?>"
                                                        type="button"
                                                        class="btn btn-lg btn-primary"
                                                >CHANGE PLAN</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Standard Plan (hidden if already on level 2) -->
                            <div class="col-lg-6 <?php echo $currentLevel === 2 ? 'd-none' : ''; ?>">
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
                                                <a
                                                        href="<?php echo $currentLevel > 2 ? 'downgrade?levelID=2' : 'checkout?levelID=2'; ?>"
                                                        type="button"
                                                        class="btn btn-lg btn-primary"
                                                >CHANGE PLAN</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pro Plan (hidden if already on level 3) -->
                            <div class="col-lg-6 <?php echo $currentLevel === 3 ? 'd-none' : ''; ?>">
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

        <?php include_once 'includes/member-footer.php'; ?>

    </div>
</div>

<!-- -----------------------------------------------------------------------------
     Scripts (vendor + plugins + custom)
     ----------------------------------------------------------------------------- -->
<?php include_once 'includes/js.php'; ?>
<?php include_once 'includes/js-plugins.php'; ?>
<?php include_once 'includes/js-custom.php'; ?>
<?php include_once 'includes/live-chat.php'; ?>

<script>
    // -----------------------------------------------------------------------------
    // Payments DataTable initialization (kept as-is)
    // -----------------------------------------------------------------------------
    $(document).ready(function () {
        $("#dataTable").DataTable({
            order: [[0, 'desc']],
            responsive: true
        });
    });
</script>

<!-- Animation init (kept as-is) -->
<script>new WOW().init();</script>

</body>
</html>
