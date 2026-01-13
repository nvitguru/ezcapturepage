<?php
/**
 * legal.php
 *
 * Responsibilities:
 * - Authenticated system page for viewing legal documents
 * - Displays Terms & Conditions, Privacy Policy, and Cancellation Policy
 * - Content is modularized into include files for maintainability
 */

session_start();

$pageTitle = "System Legal";
$tab = "legal";

include_once '../includes/session.php';
include_once '../includes/settings.php';

/**
 * Auth gate: restrict access to authenticated users only.
 */
if (!isset($_SESSION['user'])) {
    header('location: index.php');
    exit;
}

/**
 * Open database connection.
 * (Not currently used on this page, but kept consistent with other member pages.)
 */
$conn = $pdo->open();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?> | <?php echo htmlspecialchars(SYSTEM_NAME, ENT_QUOTES, 'UTF-8'); ?></title>

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

                    <!-- Page Title -->
                    <div class="col-12">
                        <div class="page-title mt-2">
                            <div class="row">
                                <div class="col-sm-6 ps-0">
                                    <h3><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h3>
                                </div>
                                <div class="col-sm-6 pe-0">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="dashboard.php">
                                                <i class="fa fa-home stroke-icon"></i>
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item active">
                                            <?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php include_once 'includes/alerts.php' ?>

                    <!-- Navigation Buttons -->
                    <div class="col-lg-3">
                        <div class="row">
                            <div class="col-12 mb-3 d-grid">
                                <button id="btn-terms" class="btn btn-lg btn-primary">
                                    Terms & Conditions
                                </button>
                            </div>
                            <div class="col-12 mb-3 d-grid">
                                <button id="btn-privacy" class="btn btn-lg btn-primary">
                                    Privacy Policy
                                </button>
                            </div>
                            <div class="col-12 mb-3 d-grid">
                                <button id="btn-cancel" class="btn btn-lg btn-primary">
                                    Cancellation Policy
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Legal Content -->
                    <div class="col-lg-9">
                        <div class="row justify-content-center">
                            <div class="col-12 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">

                                            <!-- Terms -->
                                            <span id="content-terms">
                                                <?php include_once 'forms/terms.php'; ?>
                                            </span>

                                            <!-- Privacy -->
                                            <span id="content-privacy" class="d-none">
                                                <?php include_once 'forms/privacy.php'; ?>
                                            </span>

                                            <!-- Cancellation -->
                                            <span id="content-cancel" class="d-none">
                                                <?php include_once 'forms/cancel.php'; ?>
                                            </span>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <?php include_once 'includes/member-footer.php' ?>
    </div>
</div>

<?php
/**
 * Close database connection after page rendering is complete.
 */
$pdo->close();
?>

<?php include_once 'includes/js.php' ?>
<?php include_once 'includes/js-plugins.php' ?>
<?php include_once 'includes/js-custom.php' ?>
<?php include_once 'includes/live-chat.php' ?>

<script>
    document.addEventListener("DOMContentLoaded", function () {

        const buttons = {
            terms: document.getElementById("btn-terms"),
            privacy: document.getElementById("btn-privacy"),
            cancel: document.getElementById("btn-cancel")
        };

        const content = {
            terms: document.getElementById("content-terms"),
            privacy: document.getElementById("content-privacy"),
            cancel: document.getElementById("content-cancel")
        };

        /**
         * Hide all content sections, then show the requested one.
         */
        function showSection(section) {
            Object.values(content).forEach(el => el.classList.add("d-none"));
            content[section].classList.remove("d-none");
        }

        buttons.terms.addEventListener("click", () => showSection("terms"));
        buttons.privacy.addEventListener("click", () => showSection("privacy"));
        buttons.cancel.addEventListener("click", () => showSection("cancel"));
    });
</script>

<script>new WOW().init();</script>
</body>
</html>
