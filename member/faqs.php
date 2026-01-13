<?php
/**
 * faqs.php
 *
 * Responsibilities:
 * - Auth-gated member FAQ page
 * - Displays two FAQ categories:
 *   1) General Questions (category = 1)
 *   2) Page Builder Questions (category = 2)
 *
 */

session_start();

$pageTitle = "Frequently Asked Questions";
$tab = "faq";

include_once '../includes/session.php';
include_once '../includes/settings.php';

/**
 * Auth gate: member pages require a logged-in session.
 */
if (!isset($_SESSION['user'])) {
    header('location: index');
    exit;
}

/**
 * Keep compatibility with existing routing/query patterns.
 * This file previously assumed pageID existed; now we safely parse it.
 * (Not used in rendering currently, but still captured.)
 */
$pageID = filter_input(INPUT_GET, 'pageID', FILTER_VALIDATE_INT);
if ($pageID === false) {
    $pageID = null;
}

/**
 * Render an FAQ accordion for a given category.
 *
 * - Uses prepared statements to avoid injection.
 * - Escapes output to prevent XSS, since FAQ content is stored in DB.
 * - Preserves the same HTML structure + Bootstrap accordion behavior.
 *
 * @param PDO    $conn
 * @param int    $category
 * @param string $accordionId
 */
function renderFaqAccordion(PDO $conn, int $category, string $accordionId): void
{
    try {
        $stmt = $conn->prepare("
            SELECT `id`, `question`, `answer`
            FROM `faqs`
            WHERE `category` = :category
            ORDER BY `id` ASC
        ");
        $stmt->execute(['category' => $category]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $id = (int)$row['id'];

            // Escape output for safety (DB content should never be blindly trusted)
            $question = htmlspecialchars((string)$row['question'], ENT_QUOTES, 'UTF-8');
            $answer   = nl2br(htmlspecialchars((string)$row['answer'], ENT_QUOTES, 'UTF-8'));

            echo "
                <div class='accordion-item'>
                    <h2 class='accordion-header' id='heading{$id}'>
                        <button class='accordion-button collapsed' type='button'
                            data-bs-toggle='collapse'
                            data-bs-target='#accord{$id}'
                            aria-expanded='false'
                            aria-controls='accord{$id}'>
                            {$question}<i class='fa fa-chevron-down svg-color'></i>
                        </button>
                    </h2>
                    <div class='accordion-collapse collapse' id='accord{$id}'
                        aria-labelledby='heading{$id}'
                        data-bs-parent='#{$accordionId}'>
                        <div class='accordion-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    <p style='font-size: 18px'>{$answer}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            ";
        }
    } catch (PDOException $e) {
        /**
         * Maintain the previous "show error" behavior,
         * but make it safe for HTML output.
         */
        echo htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Open DB connection once for the whole page.
 */
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
                                    <h3><?php echo $pageTitle ?></h3>
                                </div>
                                <div class="col-sm-6 pe-0">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="dashboard.php">
                                                <i class="fa fa-home stroke-icon"></i>
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item active"><?php echo $pageTitle ?></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php include_once 'includes/alerts.php' ?>

                    <div class="col-lg-8 box-col-4">

                        <!-- General Questions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card total-earning">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 mb-2">
                                                <h3>General Questions</h3>
                                            </div>
                                            <div class="col-12">
                                                <div class="accordion dark-accordion" id="generalAccordion">
                                                    <?php renderFaqAccordion($conn, 1, 'generalAccordion'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Page Builder Questions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card total-earning">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 mb-2">
                                                <h3>Page Builder Questions</h3>
                                            </div>
                                            <div class="col-12">
                                                <div class="accordion dark-accordion" id="builderAccordion">
                                                    <?php renderFaqAccordion($conn, 2, 'builderAccordion'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div><!-- /col -->

                </div><!-- /row -->
            </div><!-- /container -->
        </div><!-- /page-body -->

        <?php include_once '../includes/member-footer.php' ?>
    </div><!-- /page-body-wrapper -->
</div><!-- /page-wrapper -->

<?php
/**
 * Close DB connection after page render work is complete.
 */
$pdo->close();
?>

<?php include_once 'includes/js.php' ?>
<?php include_once 'includes/js-plugins.php' ?>
<?php include_once 'includes/js-custom.php' ?>
<?php include_once 'includes/live-chat.php' ?>

<script>new WOW().init();</script>
</body>
</html>
