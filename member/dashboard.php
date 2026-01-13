<?php
/**
 * Member Dashboard
 *
 * Responsibilities:
 * - Enforce authenticated access
 * - Load member pages + metrics
 * - Render dashboard widgets and capture page quick-view cards
 *
 * Notes:
 * - This file relies on globals/variables hydrated by included files (ex: $pdo, $user, $activePages, etc.).
 * - The capture page cards render per-page copy-link functions and toggle behaviors.
 */

/** @var Database $pdo */
session_start();

// -----------------------------------------------------------------------------
// Page metadata (used by layout/template includes)
// -----------------------------------------------------------------------------
$pageTitle = "Member Dashboard";
$tab = "dashboard";

// -----------------------------------------------------------------------------
// Bootstrap / shared includes
// -----------------------------------------------------------------------------
include '../includes/session.php';
include '../includes/settings.php';

// -----------------------------------------------------------------------------
// Auth gate: dashboard requires an authenticated session
// -----------------------------------------------------------------------------
if (!isset($_SESSION['user'])) {
    header('Location: index');
    exit;
}

// -----------------------------------------------------------------------------
// DB connection + basic runtime values
// -----------------------------------------------------------------------------
$conn = $pdo->open();
$now  = date('Y-m-d'); // kept (even if unused) to avoid behavior surprises

// -----------------------------------------------------------------------------
// Client IP address detection (best-effort)
// NOTE: Forwarded headers can be spoofed unless you only trust known proxies.
// This implementation validates IPs and prefers the first valid IP in XFF.
// -----------------------------------------------------------------------------
$ipaddress = 'UNKNOWN';

$serverVars = [
    'HTTP_CLIENT_IP',
    'HTTP_X_FORWARDED_FOR',
    'HTTP_X_FORWARDED',
    'HTTP_X_CLUSTER_CLIENT_IP',
    'HTTP_FORWARDED_FOR',
    'HTTP_FORWARDED',
    'REMOTE_ADDR',
];

foreach ($serverVars as $key) {
    if (empty($_SERVER[$key])) {
        continue;
    }

    $value = trim((string)$_SERVER[$key]);

    // X-Forwarded-For can contain a list: client, proxy1, proxy2...
    if ($key === 'HTTP_X_FORWARDED_FOR') {
        $parts = array_map('trim', explode(',', $value));
        foreach ($parts as $candidate) {
            if (filter_var($candidate, FILTER_VALIDATE_IP)) {
                $ipaddress = $candidate;
                break 2;
            }
        }
        continue;
    }

    if (filter_var($value, FILTER_VALIDATE_IP)) {
        $ipaddress = $value;
        break;
    }
}

// Helpful tiny escape helper for HTML output
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
                         Server-side alerts/flash messages
                         ----------------------------------------------------------------- -->
                    <?php include_once 'includes/alerts.php'; ?>

                    <!-- -----------------------------------------------------------------
                         Right column: account status + quick view widgets
                         ----------------------------------------------------------------- -->
                    <div class="col-lg-4 order-md-1 order-lg-2">
                        <div class="row">

                            <!-- Account Status -->
                            <div class="col-12 mb-3">
                                <div class="card quick-view">
                                    <?php $isSuspended = !empty($user['suspend']); ?>
                                    <div class="card-body pt-2 <?php echo $isSuspended ? 'bg-warning' : 'bg-success'; ?>">
                                        <div class="row">
                                            <div class="col-12 text-center">
                                                <h2 class="<?php echo $isSuspended ? 'txt-warning' : 'txt-success'; ?>">
                                                    <?php echo $isSuspended ? 'ACCOUNT SUSPENDED' : 'ACCOUNT ACTIVE'; ?>
                                                </h2>
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
                                                <h2><?php echo $e($activeCRMs) . " out of " . $e($pageCount); ?></h2>
                                            </div>
                                            <div class="col-12 d-grid">
                                                <form method="post" action="createCRM.php">
                                                    <div class="row">
                                                        <div class="col-12 d-grid">
                                                            <button type="submit" name="createCRM" class="btn btn-sm btn-success">
                                                                <i class="fa fa-plus-circle"></i> CREATE CRM
                                                            </button>
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
                                                <h2><?php echo $e($activeForms) . " out of " . $e($pageCount); ?></h2>
                                            </div>
                                            <div class="col-12">
                                                <form method="post" action="createForm.php">
                                                    <div class="row">
                                                        <div class="col-12 d-grid">
                                                            <button type="submit" name="createForm" class="btn btn-sm btn-success">
                                                                <i class="fa fa-plus-circle"></i> CREATE FORM
                                                            </button>
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
                                                <h2><?php echo $e($activePages) . " out of " . $e($pageCount); ?></h2>
                                            </div>
                                            <div class="col-12 d-grid">
                                                <form method="post" action="createPage.php">
                                                    <div class="row">
                                                        <div class="col-12 d-grid">
                                                            <button type="submit" name="createPage" class="btn btn-sm btn-success">
                                                                <i class="fa fa-plus-circle"></i> CREATE PAGE
                                                            </button>
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

                    <!-- -----------------------------------------------------------------
                         Left column: capture pages list + performance metrics
                         ----------------------------------------------------------------- -->
                    <div class="col-lg-8" style="border-right: 1px solid #ccc;">
                        <div class="row">

                            <!-- Capture Page Quick View Start -->
                            <?php
                            try {
                                // Single-query load: pages + metrics (prevents N+1 query pattern)
                                // Assumes metrics.pageID is unique per pageID (as implied by original code).
                                $stmt = $conn->prepare("
                                    SELECT
                                        p.*,
                                        COALESCE(m.view, 0)   AS metric_view,
                                        COALESCE(m.submit, 0) AS metric_submit
                                    FROM pages p
                                    LEFT JOIN metrics m ON m.pageID = p.pageID
                                    WHERE p.userID = :userID
                                    ORDER BY p.pageID DESC
                                ");
                                $stmt->execute(['userID' => $user['userID']]);
                                $pages = $stmt->fetchAll();

                                foreach ($pages as $row) {
                                    $pageId  = (int)$row['pageID'];
                                    $views   = (int)$row['metric_view'];
                                    $submits = (int)$row['metric_submit'];
                                    $conversion = $views > 0 ? (int)round(($submits / $views) * 100) : 0;

                                    $isActive = !empty($row['active']);

                                    $pageActiveHtml = $isActive
                                        ? "<span class='text-success'><strong>ACTIVE</strong></span>"
                                        : "<span class='text-warning'><strong>INACTIVE</strong></span>";

                                    $statusColor = $isActive ? "btn-warning" : "btn-success";
                                    $statusIcon  = $isActive ? "<i class='fa fa-pause-circle'></i>" : "<i class='fa fa-play-circle'></i>";
                                    $tooltipText = $isActive ? "Suspend" : "Publish";

                                    // Build the capture page URL exactly as before (same pattern)
                                    $pageUrl = "https://page." . SYSTEM_URL . "/{$pageId}";

                                    echo "<div class='col-lg-6 col-sm-12 box-col-4 mb-3'>
                                            <div class='card total-earning'>
                                                <div class='card-body'>
                                                    <div class='row'>
                                                        <div class='col-12'>
                                                            <div class='d-flex'>
                                                                <div class='badge bg-light-primary badge-rounded font-primary me-2'><i class='fa fa-globe'></i></div>
                                                                <div class='flex-grow-1'>
                                                                    <p class='my-0'><small>{$pageActiveHtml}</small></p>
                                                                    <h3>" . $e($row['name']) . "</h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class='col text-center'>
                                                            <h5 class='mb-1'>" . $e($views) . "</h5>
                                                            <p class='mt-0'>Visitors</p>
                                                        </div>
                                                        <div class='col text-center'>
                                                            <h5 class='mb-1'>" . $e($submits) . "</h5>
                                                            <p class='mt-0'>Submissions</p>
                                                        </div>
                                                        <div class='col text-center'>
                                                            <h5 class='mb-1'>" . $e($conversion) . "%</h5>
                                                            <p class='mt-0'>Conversions</p>
                                                        </div>

                                                        <div id='pageLink{$pageId}' class='input-group input-group-lg col-12 mt-3 theme-form d-none'>
                                                            <input class='form-control' id='link{$pageId}' value='" . $e($pageUrl) . "' readonly>
                                                            <button
                                                                class='btn btn-secondary js-copy-link'
                                                                type='button'
                                                                data-page-id='{$pageId}'
                                                                data-bs-toggle='tooltip'
                                                                data-bs-placement='top'
                                                                title='Copy Link'
                                                            ><i class='fa fa-copy'></i></button>
                                                        </div>

                                                        <div class='col-12'><hr></div>

                                                        <div class='col-12 d-grid'>
                                                            <div class='btn-group' role='group' aria-label='Basic example'>
                                                                <a href='page?pageID={$pageId}' class='btn btn-primary btn-lg' type='button' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit Capture Page'><i class='fa fa-edit'></i></a>
                                                                <a href='example?pageID={$pageId}' target='_blank' class='btn btn-secondary btn-lg' type='button' data-bs-toggle='tooltip' data-bs-placement='top' title='View Capture Page'><i class='fa fa-search'></i></a>
                                                                <button
                                                                    type='button'
                                                                    class='btn btn-info btn-lg js-toggle-link'
                                                                    data-page-id='{$pageId}'
                                                                    data-bs-toggle='tooltip'
                                                                    data-bs-placement='top'
                                                                    title='Get Capture Page Link'
                                                                ><i class='fa fa-link'></i></button>
                                                                <a href='toggleDashPage.php?pageID={$pageId}' class='btn {$statusColor} btn-lg' type='button' data-bs-toggle='tooltip' data-bs-placement='top' title='{$tooltipText} Capture Page'>{$statusIcon}</a>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>";
                                }
                            } catch (PDOException $eX) {
                                // Professional: log details, show generic message
                                error_log("Dashboard load error: " . $eX->getMessage());
                                echo "<div class='col-12'><div class='alert alert-danger'>Unable to load dashboard data right now.</div></div>";
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

        <!-- ---------------------------------------------------------------------
             Footer include
             --------------------------------------------------------------------- -->
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
    // Delegated UI handlers:
    // - Toggle visibility of link input groups
    // - Copy link to clipboard (modern API with legacy fallback)
    // -----------------------------------------------------------------------------
    document.addEventListener('click', async function (e) {
        const toggleBtn = e.target.closest('.js-toggle-link');
        if (toggleBtn) {
            const pageId = toggleBtn.getAttribute('data-page-id');
            if (!pageId) return;

            const inputGroup = document.getElementById('pageLink' + pageId);
            if (inputGroup) {
                inputGroup.classList.toggle('d-none');
            }
            return;
        }

        const copyBtn = e.target.closest('.js-copy-link');
        if (copyBtn) {
            const pageId = copyBtn.getAttribute('data-page-id');
            if (!pageId) return;

            const input = document.getElementById('link' + pageId);
            if (!input) return;

            const textToCopy = input.value || '';

            // Try modern clipboard API first
            try {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    await navigator.clipboard.writeText(textToCopy);
                } else {
                    // Legacy fallback (keeps old behavior compatibility)
                    input.focus();
                    input.select();
                    input.setSelectionRange(0, 99999);
                    document.execCommand('copy');
                }

                // Preserve original UX behavior (alert) to avoid changing behavior
                alert('Copied the link: ' + textToCopy);
            } catch (err) {
                // If clipboard fails, attempt legacy once more
                try {
                    input.focus();
                    input.select();
                    input.setSelectionRange(0, 99999);
                    document.execCommand('copy');
                    alert('Copied the link: ' + textToCopy);
                } catch (err2) {
                    alert('Unable to copy link. Please copy it manually.');
                }
            }
        }
    });
</script>

<!-- Animation init (kept as-is) -->
<script>new WOW().init();</script>
</body>
</html>
