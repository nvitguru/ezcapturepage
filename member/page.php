<?php
/**
 * page.php
 *
 * Responsibilities:
 * - Authenticated page builder for editing an existing capture page
 * - Loads base page record (pages) and content record (content) by pageID
 * - Renders configuration UI and a live preview pane
 */

session_start();

$pageTitle = "Edit Capture Page";
$tab = "page";

include_once '../includes/session.php';
include_once '../includes/settings.php';

/**
 * Auth gate.
 */
if (!isset($_SESSION['user'])) {
    header('location: index.php');
    exit;
}

/**
 * Validate and normalize pageID.
 */
$pageID = filter_input(INPUT_GET, 'pageID', FILTER_VALIDATE_INT);
if (!$pageID) {
    die('Invalid pageID.');
}

/**
 * Open DB connection once for the page.
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
 * Fetch a single row by pageID from a whitelisted table.
 * This avoids introducing table-name injection risks.
 */
function fetchByPageId(PDO $conn, string $table, int $pageId): array
{
    $allowed = ['content', 'pages'];
    if (!in_array($table, $allowed, true)) {
        return [];
    }

    $stmt = $conn->prepare("SELECT * FROM `$table` WHERE `pageID` = :id LIMIT 1");
    $stmt->execute(['id' => $pageId]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: [];
}

/**
 * Load records required by this page.
 */
$content = fetchByPageId($conn, 'content', $pageID);
$pages   = fetchByPageId($conn, 'pages', $pageID);
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

    <style>
        .form-control-lg {
            font-size: 1.75rem;
        }
        .input-group-lg>.input-group-text {
            font-size: 2rem;
            padding: 1rem;
        }
        .input-group-lg>.input-group-text {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }
    </style>
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
                                    <h3>
                                        <?php echo e($pageTitle); ?>

                                        <!-- Delete Capture Page Button Start -->
                                        <span class="float-end">
                                            <?php if (!empty($pages['active'])) { ?>
                                                <a class="btn btn-dark btn-sm"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="bottom"
                                                   title="Attention! This page is currently PUBLISHED. To delete, please deactivate the page first."
                                                   disabled>
                                                  <i class="fa fa-trash"></i> Delete
                                                </a>
                                            <?php } else { ?>
                                                <a href="#deleteModal" data-bs-toggle="modal" class="btn btn-dark btn-sm">
                                                  <i class="fa fa-trash"></i> Delete
                                                </a>
                                            <?php } ?>
                                        </span>
                                        <!-- Delete Capture Page Button End -->
                                    </h3>
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

                    <!-- Create Page Form Start -->
                    <div class='col-lg-6 box-col-4'>
                        <div class='card total-earning'>
                            <div class='card-body'>
                                <form class="theme-form" method="post" action="editPage.php" enctype="multipart/form-data">
                                    <input type="hidden" name="pageID" value="<?php echo e($pageID); ?>">
                                    <div class='row'>
                                        <div class="col-12 mb-2">
                                            <h3>Build Page</h3>
                                        </div>
                                        <div class='col-12'>
                                            <div class="accordion dark-accordion" id="contentAccordion">

                                                <!-- General Information Start -->
                                                <div class='accordion-item'>
                                                    <h2 class='accordion-header' id='heading00'>
                                                        <button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#accordInfo' aria-expanded='true' aria-controls='accordInfo'>General Information<i class='fa fa-chevron-down svg-color'></i></button>
                                                    </h2>
                                                    <div class='accordion-collapse collapse show' id='accordInfo' aria-labelledby='heading00' data-bs-parent='#contentAccordion'>
                                                        <div class='accordion-body'>
                                                            <div class='row'>
                                                                <div class="col-lg-6 mb-2">
                                                                    <label>Page Name <i class="fa fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="auto" title="Give your page a name only you will see to remember what the page is for."></i></label>
                                                                    <input type="text" class="form-control form-control-lg" name="name" value="<?php echo e($pages['name'] ?? ''); ?>">
                                                                </div>
                                                                <div class="col-lg-6 mb-2">
                                                                    <label>Tab Title <i class="fa fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="auto" title="This name will appear in the tab of your browser."></i></label>
                                                                    <input type="text" class="form-control form-control-lg" name="tabTitle" value="<?php echo e($content['tabTitle'] ?? ''); ?>">
                                                                </div>
                                                                <div class="col-lg-6 mb-2">
                                                                    <label>Google Analytics (optional) <i class="fa fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="auto" title="Add your G-tag for Google Analytic tracking of your capture page."></i></label>
                                                                    <input type="text" class="form-control form-control-lg" name="google" placeholder="G-XXXXXX" value="<?php echo e($pages['google'] ?? ''); ?>">
                                                                </div>
                                                                <div class="col-lg-6 mb-2">
                                                                    <label>Facebook Pixel (optional) <i class="fa fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="auto" title="Add your Facebook Pixel for your Facebook Marketing campaign for this capture page."></i></label>
                                                                    <input type="text" class="form-control form-control-lg" name="pixel" placeholder="XXXXXXXXXXXX" value="<?php echo e($pages['pixel'] ?? ''); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <hr>
                                                                </div>
                                                                <div class="col text-end">
                                                                    <button class='btn btn-primary btn-lg accordionBtn' type='button' data-bs-toggle='collapse' data-bs-target='#accordBackground' aria-expanded='true' aria-controls='accordInfo'>NEXT STEP <i class='fa fa-angle-double-right'></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- General Information End -->

                                                <!-- Color Select Start -->
                                                <div class='accordion-item'>
                                                    <h2 class='accordion-header' id='heading01'>
                                                        <button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#accordColor' aria-expanded='true' aria-controls='accordColor'>Color Pallete<i class='fa fa-chevron-down svg-color'></i></button>
                                                    </h2>
                                                    <div class='accordion-collapse collapse' id='accordColor' aria-labelledby='heading01' data-bs-parent='#contentAccordion'>
                                                        <div class='accordion-body'>
                                                            <div class='row'>
                                                                <div class="col-12">
                                                                    <?php $color = $content['color'] ?? ''; ?>
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio" name="color" id="inlineRadio1" value="red" <?php echo ($color === "red") ? "checked" : "" ?>>
                                                                        <label class="form-check-label" for="inlineRadio1">RED</label>
                                                                    </div>
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio" name="color" id="inlineRadio2" value="blue" <?php echo ($color === "blue") ? "checked" : "" ?>>
                                                                        <label class="form-check-label" for="inlineRadio2">BLUE</label>
                                                                    </div>
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio" name="color" id="inlineRadio3" value="green" <?php echo ($color === "green") ? "checked" : "" ?>>
                                                                        <label class="form-check-label" for="inlineRadio3">GREEN</label>
                                                                    </div>
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio" name="color" id="inlineRadio4" value="orange" <?php echo ($color === "orange") ? "checked" : "" ?>>
                                                                        <label class="form-check-label" for="inlineRadio4">ORANGE</label>
                                                                    </div>
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio" name="color" id="inlineRadio5" value="purple" <?php echo ($color === "purple") ? "checked" : "" ?>>
                                                                        <label class="form-check-label" for="inlineRadio5">PURPLE</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <hr>
                                                                </div>
                                                                <div class="col">
                                                                    <button class='btn btn-secondary btn-lg accordionBtn' type='button' data-bs-toggle='collapse' data-bs-target='#accordInfo' aria-expanded='true' aria-controls='accordBackground'><i class='fa fa-angle-double-left'></i> PREVIOUS STEP</button>
                                                                </div>
                                                                <div class="col text-end">
                                                                    <button class='btn btn-primary btn-lg accordionBtn' type='button' data-bs-toggle='collapse' data-bs-target='#accordBackground' aria-expanded='true' aria-controls='accordBackground'>NEXT STEP <i class='fa fa-angle-double-right'></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Color Select End -->

                                                <!-- Background Image Start -->
                                                <div class='accordion-item'>
                                                    <h2 class='accordion-header' id='heading02'>
                                                        <button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#accordBackground' aria-expanded='true' aria-controls='accordBackground'>Background Image<i class='fa fa-chevron-down svg-color'></i></button>
                                                    </h2>
                                                    <div class='accordion-collapse collapse' id='accordBackground' aria-labelledby='heading02' data-bs-parent='#contentAccordion'>
                                                        <div class='accordion-body'>
                                                            <div class='row'>
                                                                <div class="form-check col-12 mb-3 ps-5">
                                                                    <input id="noBackground" class="form-check-input" type="radio" value="99" name="background"  <?php echo (($content['background'] ?? null) == 99) ? "checked" : "" ?>>
                                                                    <label class="form-check-label mt-2 ms-2" for="99">NO BACKGROUND IMAGE</label>
                                                                </div>
                                                                <?php
                                                                try {
                                                                    $backgroundStmt = $conn->prepare("SELECT * FROM `background` WHERE `active` = TRUE");
                                                                    $backgroundStmt->execute();
                                                                    $background = $backgroundStmt->fetchAll(PDO::FETCH_ASSOC);

                                                                    foreach ($background as $bg) {
                                                                        $currentBg    = $content['background'] ?? '';
                                                                        $bgChecked    = ($bg['name'] == $currentBg) ? "checked" : "";
                                                                        $divSelected  = ($bg['name'] == $currentBg) ? "piece-selected" : "";
                                                                        $iSelected    = ($bg['name'] == $currentBg) ? "" : "d-none";

                                                                        echo "
                                                                            <div id='selectionDiv' class='form-check col-md-6 position-relative dark-bg pt-2 position-relative $divSelected'>
                                                                                <i id='selectionIcon' class='fa fa-check-circle selected-check $iSelected'></i>
                                                                                <input class='form-check-input position-absolute piece-selection d-none' type='radio' value='". e($bg['name']) ."' id='". e($bg['name']) ."' name='background' $bgChecked>
                                                                                <label class='form-check-label' for='". e($bg['name']) ."'>
                                                                                    <img src='../images/background/". e($bg['name']) .".jpg' class='img-fluid'>
                                                                                </label>
                                                                            </div>";
                                                                    }
                                                                } catch (PDOException $e) {
                                                                    echo e($e->getMessage());
                                                                }
                                                                ?>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <hr>
                                                                </div>
                                                                <div class="col">
                                                                    <button class='btn btn-secondary btn-lg accordionBtn' type='button' data-bs-toggle='collapse' data-bs-target='#accordColor' aria-expanded='true' aria-controls='accordBackground'><i class='fa fa-angle-double-left'></i> PREVIOUS STEP</button>
                                                                </div>
                                                                <div class="col text-end">
                                                                    <button class='btn btn-primary btn-lg accordionBtn' type='button' data-bs-toggle='collapse' data-bs-target='#accordHeader' aria-expanded='true' aria-controls='accordBackground'>NEXT STEP <i class='fa fa-angle-double-right'></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Background Image End -->

                                                <!-- Header Image Start -->
                                                <div class='accordion-item'>
                                                    <h2 class='accordion-header' id='heading03'>
                                                        <button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#accordHeader' aria-expanded='true' aria-controls='accordHeader'>Header Image<i class='fa fa-chevron-down svg-color'></i></button>
                                                    </h2>
                                                    <div class='accordion-collapse collapse' id='accordHeader' aria-labelledby='heading03' data-bs-parent='#contentAccordion'>
                                                        <div class='accordion-body'>
                                                            <div class='row'>
                                                                <div class="form-check col-12 mb-3 ps-5">
                                                                    <input id="noHeader" class="form-check-input" type="radio" value="99" name="header" <?php echo (($content['header'] ?? null) == 99) ? "checked" : "" ?>>
                                                                    <label class="form-check-label mt-2 ms-2" for="99">NO HEADER IMAGE</label>
                                                                </div>
                                                                <?php
                                                                try {
                                                                    $headerStmt = $conn->prepare("SELECT * FROM `header` WHERE `active` = TRUE");
                                                                    $headerStmt->execute();
                                                                    $header = $headerStmt->fetchAll(PDO::FETCH_ASSOC);

                                                                    $currentHeader = $content['header'] ?? '';
                                                                    $currentColor  = $content['color'] ?? '';

                                                                    foreach ($header as $head) {
                                                                        $headChecked  = ($head['name'] == $currentHeader) ? "checked" : "";
                                                                        $divSelected  = ($head['name'] == $currentHeader) ? "piece-selected" : "";
                                                                        $iSelected    = ($head['name'] == $currentHeader) ? "" : "d-none";

                                                                        echo "
                                                                        <div id='selectionDiv' class='form-check col-md-6 position-relative dark-bg pt-2 position-relative $divSelected'>
                                                                            <i id='selectionIcon' class='fa fa-check-circle selected-check $iSelected'></i>
                                                                            <input class='form-check-input header-radio piece-selection d-none' type='radio' value='". e($head['name']) ."' id='". e($head['name']) ."' name='header' $headChecked>
                                                                            <label class='form-check-label' for='". e($head['name']) ."'>
                                                                                <img src='../images/header/". e($currentColor) ."/". e($head['name']) .".png' class='img-fluid'>
                                                                            </label>
                                                                        </div>";
                                                                    }
                                                                } catch (PDOException $e) {
                                                                    echo e($e->getMessage());
                                                                }
                                                                ?>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <hr>
                                                                </div>
                                                                <div class="col">
                                                                    <button class='btn btn-secondary btn-lg accordionBtn' type='button' data-bs-toggle='collapse' data-bs-target='#accordBackground' aria-expanded='true' aria-controls='accordBackground'><i class='fa fa-angle-double-left'></i> PREVIOUS STEP</button>
                                                                </div>
                                                                <div class="col text-end">
                                                                    <button class='btn btn-primary btn-lg accordionBtn' type='button' data-bs-toggle='collapse' data-bs-target='#accordSubheader' aria-expanded='true' aria-controls='accordBackground'>NEXT STEP <i class='fa fa-angle-double-right'></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Header Image End -->

                                                <!-- Subheader Image Start -->
                                                <div class='accordion-item'>
                                                    <h2 class='accordion-header' id='heading04'>
                                                        <button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#accordSubheader' aria-expanded='true' aria-controls='accordSubheader'>Subheader Image<i class='fa fa-chevron-down svg-color'></i></button>
                                                    </h2>
                                                    <div class='accordion-collapse collapse' id='accordSubheader' aria-labelledby='heading04' data-bs-parent='#contentAccordion'>
                                                        <div class='accordion-body'>
                                                            <div class='row'>
                                                                <div class="form-check col-12 mb-3 ps-5">
                                                                    <input id="noSubheader" class="form-check-input" type="radio" value="99" name="subheader" <?php echo (($content['subheader'] ?? null) == 99) ? "checked" : "" ?>>
                                                                    <label class="form-check-label mt-2 ms-2" for="99">NO SUBHEADER IMAGE</label>
                                                                </div>
                                                                <?php
                                                                try {
                                                                    $subheaderStmt = $conn->prepare("SELECT * FROM `subheader` WHERE `active` = TRUE");
                                                                    $subheaderStmt->execute();
                                                                    $subheader = $subheaderStmt->fetchAll(PDO::FETCH_ASSOC);

                                                                    $currentSub = $content['subheader'] ?? '';

                                                                    foreach ($subheader as $sub) {
                                                                        $subChecked  = ($sub['name'] == $currentSub) ? "checked" : "";
                                                                        $divSelected = ($sub['name'] == $currentSub) ? "piece-selected" : "";
                                                                        $iSelected   = ($sub['name'] == $currentSub) ? "" : "d-none";

                                                                        echo "
                                                                            <div id='subDiv' class='form-check col-md-6 position-relative dark-bg pt-2 position-relative $divSelected'>
                                                                                <i class='fa fa-check-circle selected-check $iSelected'></i>
                                                                                <input class='form-check-input subheader-radio piece-selection d-none' type='radio' value='". e($sub['name']) ."' id='". e($sub['name']) ."' name='subheader' $subChecked>
                                                                                <label class='form-check-label' for='". e($sub['name']) ."'>
                                                                                    <img src='../images/subheader/". e($sub['name']) .".png' class='img-fluid'>
                                                                                </label>
                                                                            </div>";
                                                                    }
                                                                } catch (PDOException $e) {
                                                                    echo e($e->getMessage());
                                                                }
                                                                ?>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <hr>
                                                                </div>
                                                                <div class="col">
                                                                    <button class='btn btn-secondary btn-lg accordionBtn' type='button' data-bs-toggle='collapse' data-bs-target='#accordHeader' aria-expanded='true' aria-controls='accordBackground'><i class='fa fa-angle-double-left'></i> PREVIOUS STEP</button>
                                                                </div>
                                                                <div class="col text-end">
                                                                    <button class='btn btn-primary btn-lg accordionBtn' type='button' data-bs-toggle='collapse' data-bs-target='#accordVideo' aria-expanded='true' aria-controls='accordBackground'>NEXT STEP <i class='fa fa-angle-double-right'></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Subheader Image End -->

                                                <!-- Video Embed Start -->
                                                <div class='accordion-item'>
                                                    <h2 class='accordion-header' id='heading05'>
                                                        <button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#accordVideo' aria-expanded='true' aria-controls='accordVideo'>Video Embed<i class='fa fa-chevron-down svg-color'></i></button>
                                                    </h2>
                                                    <div class='accordion-collapse collapse' id='accordVideo' aria-labelledby='heading05' data-bs-parent='#contentAccordion'>
                                                        <div class='accordion-body'>
                                                            <div class="row">
                                                                <div class="form-check col-12 mb-3 ps-5">
                                                                    <input id="noVideo" class="form-check-input" type="checkbox" name="noVideo"
                                                                        <?php echo (($content['video'] ?? null) == 99) ? "checked" : "" ?>>
                                                                    <label class="form-check-label mt-2 ms-2" for="noVideo">NO VIDEO EMBED</label>
                                                                </div>

                                                                <div class="col-12 form-group">
                                                                    <label id="form-title" for="videoEmbed">Video Embed Code</label>
                                                                    <input class="form-control form-control-lg" type="text" name="video" id="videoEmbed"
                                                                           value="<?php echo (isset($content['video']) && $content['video'] != 99) ? e($content['video']) : '' ?>"
                                                                        <?php echo (($content['video'] ?? null) == 99) ? "disabled" : "" ?> />
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <hr>
                                                                </div>
                                                                <div class="col">
                                                                    <button class='btn btn-secondary btn-lg accordionBtn' type='button' data-bs-toggle='collapse' data-bs-target='#accordSubheader' aria-expanded='true' aria-controls='accordBackground'><i class='fa fa-angle-double-left'></i> PREVIOUS STEP</button>
                                                                </div>
                                                                <div class="col text-end">
                                                                    <button class='btn btn-primary btn-lg accordionBtn' type='button' data-bs-toggle='collapse' data-bs-target='#accordForm' aria-expanded='true' aria-controls='accordBackground'>NEXT STEP <i class='fa fa-angle-double-right'></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Video Embed End -->

                                                <!-- Form Selection Start -->
                                                <div class='accordion-item'>
                                                    <h2 class='accordion-header' id='heading06'>
                                                        <button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#accordForm' aria-expanded='true' aria-controls='accordForm'>Form Selector<i class='fa fa-chevron-down svg-color'></i></button>
                                                    </h2>
                                                    <div class='accordion-collapse collapse' id='accordForm' aria-labelledby='heading06' data-bs-parent='#contentAccordion'>
                                                        <div class='accordion-body'>
                                                            <div id="formRadios" class='row'>
                                                                <?php
                                                                try {
                                                                    $formStmt = $conn->prepare("SELECT * FROM `form` WHERE `active` = TRUE AND `userID` = :userID");
                                                                    $formStmt->execute(['userID' => $user['userID']]);
                                                                    $submitForm = $formStmt->fetchAll(PDO::FETCH_ASSOC);

                                                                    $currentFormId = $content['formID'] ?? null;

                                                                    foreach ($submitForm as $forms) {
                                                                        $formsChecked = ((int)$forms['formID'] === (int)$currentFormId) ? "checked" : "";
                                                                        echo "
                                                                        <div class='form-check col-12 mb-3 ps-5'>
                                                                            <input class='form-check-input' type='radio' value='". e($forms['formID']) ."' id='". e($forms['formID']) ."' name='form' $formsChecked>
                                                                            <label class='form-check-label mt-2 ms-2' for='". e($forms['name']) ."'>
                                                                                ". e($forms['name']) ."
                                                                            </label>
                                                                        </div>";
                                                                    }
                                                                } catch (PDOException $e) {
                                                                    echo e($e->getMessage());
                                                                }
                                                                ?>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <hr>
                                                                </div>
                                                                <div class="col">
                                                                    <button class='btn btn-secondary btn-lg accordionBtn' type='button' data-bs-toggle='collapse' data-bs-target='#accordVideo' aria-expanded='true' aria-controls='accordBackground'><i class='fa fa-angle-double-left'></i> PREVIOUS STEP</button>
                                                                </div>
                                                                <div class="col text-end">
                                                                    <button class='btn btn-primary btn-lg accordionBtn' type='button' data-bs-toggle='collapse' data-bs-target='#accordButton' aria-expanded='true' aria-controls='accordBackground'>NEXT STEP <i class='fa fa-angle-double-right'></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Form Selection End -->

                                                <!-- Button Selection Start -->
                                                <div class='accordion-item'>
                                                    <h2 class='accordion-header' id='heading07'>
                                                        <button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#accordButton' aria-expanded='true' aria-controls='accordButton'>Form Button<i class='fa fa-chevron-down svg-color'></i></button>
                                                    </h2>
                                                    <div class='accordion-collapse collapse' id='accordButton' aria-labelledby='heading07' data-bs-parent='#contentAccordion'>
                                                        <div class='accordion-body'>
                                                            <div class='row'>
                                                                <?php
                                                                try {
                                                                    $buttonStmt = $conn->prepare("SELECT * FROM `button` WHERE `active` = TRUE");
                                                                    $buttonStmt->execute();
                                                                    $button = $buttonStmt->fetchAll(PDO::FETCH_ASSOC);

                                                                    $currentBtn  = $content['button'] ?? '';
                                                                    $currentColor = $content['color'] ?? '';

                                                                    foreach ($button as $btn) {
                                                                        $subChecked  = ($btn['name'] == $currentBtn) ? "checked" : "";
                                                                        $divSelected = ($btn['name'] == $currentBtn) ? "piece-selected" : "";
                                                                        $iSelected   = ($btn['name'] == $currentBtn) ? "" : "d-none";

                                                                        echo "
                                                                            <div id='buttonDiv' class='form-check col-md-6 position-relative dark-bg pt-2 position-relative $divSelected'>
                                                                                <i class='fa fa-check-circle selected-check $iSelected'></i>
                                                                                <input class='form-check-input button-radio piece-selection d-none' type='radio' value='". e($btn['name']) ."' id='". e($btn['name']) ."' name='button' $subChecked>
                                                                                <label class='form-check-label' for='". e($btn['name']) ."'>
                                                                                    <img src='../images/button/". e($currentColor) ."/". e($btn['name']) .".png' class='img-fluid'>
                                                                                </label>
                                                                            </div>";
                                                                    }
                                                                } catch (PDOException $e) {
                                                                    echo e($e->getMessage());
                                                                }
                                                                ?>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <hr>
                                                                </div>
                                                                <div class="col">
                                                                    <button class='btn btn-secondary btn-lg accordionBtn' type='button' data-bs-toggle='collapse' data-bs-target='#accordForm' aria-expanded='true' aria-controls='accordBackground'><i class='fa fa-angle-double-left'></i> PREVIOUS STEP</button>
                                                                </div>
                                                                <div class="col text-end">
                                                                    <button class='btn btn-primary btn-lg accordionBtn' type='button' data-bs-toggle='collapse' data-bs-target='#accordDisclaimer' aria-expanded='true' aria-controls='accordBackground'>NEXT STEP <i class='fa fa-angle-double-right'></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Button Selection End -->

                                                <!-- Disclaimer Selection Start -->
                                                <div class='accordion-item'>
                                                    <h2 class='accordion-header' id='heading08'>
                                                        <button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#accordDisclaimer' aria-expanded='true' aria-controls='accordDisclaimer'>Page Disclaimer<i class='fa fa-chevron-down svg-color'></i></button>
                                                    </h2>
                                                    <div class='accordion-collapse collapse' id='accordDisclaimer' aria-labelledby='heading08' data-bs-parent='#contentAccordion'>
                                                        <div class='accordion-body'>
                                                            <div class='row'>
                                                                <?php
                                                                $disclaimer = ['disclaimer' => ''];

                                                                try {
                                                                    $disclaimerStmt = $conn->prepare("SELECT disclaimer FROM `content` WHERE `pageID` = :pageID LIMIT 1");
                                                                    $disclaimerStmt->execute(['pageID' => $pageID]);
                                                                    $row = $disclaimerStmt->fetch(PDO::FETCH_ASSOC);
                                                                    if ($row) {
                                                                        $disclaimer = $row;
                                                                    }
                                                                } catch (PDOException $e) {
                                                                    echo e($e->getMessage());
                                                                }
                                                                ?>
                                                                <div class="form-group col-12 mb-3">
                                                                    <label>Page Disclaimer</label>
                                                                    <textarea id="disclaimer" class="form-control form-control-lg" rows="3" name="disclaimer"><?php echo e($disclaimer['disclaimer'] ?? ''); ?></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <hr>
                                                                </div>
                                                                <div class="col">
                                                                    <button class='btn btn-secondary btn-lg accordionBtn' type='button' data-bs-toggle='collapse' data-bs-target='#accordButton' aria-expanded='true' aria-controls='accordBackground'><i class='fa fa-angle-double-left'></i> PREVIOUS STEP</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Disclaimer Selection End -->

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col-lg-6 col-md-8 col-sm-10 mt-3 d-grid">
                                            <button type="submit" class="btn btn-lg btn-primary" name="editPage"><i class="fa fa-thumbs-up"></i> SAVE CHANGES</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Create Page Form End -->

                    <!-- Live Preview Start -->
                    <div class='col-lg-6 box-col-4'>
                        <div class="row justify-content-center">
                            <div class="col-lg-10 col-md-11 page-content">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 mb-3 capture-header">
                                                <img id="page-header-img" src="images/template/header.png" class="img-fluid">
                                            </div>

                                            <div class="col-12 mb-3 capture-subheader">
                                                <img id="page-subheader-img" src="images/template/subheader.png" class="img-fluid">
                                            </div>

                                            <div class="col-12 mb-3 capture-video">
                                                <img id="page-video-img" src="images/template/video.png" class="img-fluid">
                                                <div id="videoDiv" class="embed-responsive embed-responsive-16by9 d-none">
                                                    <iframe width="100%" height="400" id="videoSource" class="embed-responsive-item" src=""></iframe>
                                                </div>
                                            </div>

                                            <div id="formTemplate" class="col-12 mb-3 capture-form">
                                                <img src="images/template/form.png" class="img-fluid">
                                            </div>
                                            <div id="selectedForm" class="col-12 mb-3 d-none">
                                                <div class="row justify-content-center">
                                                    <div class="col-lg-9">
                                                        <form>
                                                            <div class="row">
                                                                <div id="fullNameDiv" class="col-12 mb-2 d-none">
                                                                    <div class="row">
                                                                        <div class='col-12 input-group'>
                                                                            <div class='input-group-prepend input-group-lg'>
                                                                                <span class='input-group-text'><i class='fa fa-user'></i></span>
                                                                            </div>
                                                                            <input type='text' class='form-control form-control-lg' placeholder='Full Name'>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div id="splitNameDiv" class="col-12 d-none">
                                                                    <div class="row">
                                                                        <div class='col-12 input-group mb-2'>
                                                                            <div class='input-group-prepend input-group-lg'>
                                                                                <span class='input-group-text'><i class='fa fa-user'></i></span>
                                                                            </div>
                                                                            <input type='text' class='form-control form-control-lg' placeholder='First Name'>
                                                                        </div>
                                                                        <div class='col-12 input-group mb-3'>
                                                                            <div class='input-group-prepend input-group-lg'>
                                                                                <span class='input-group-text'><i class='fa fa-user'></i></span>
                                                                            </div>
                                                                            <input type='text' class='form-control form-control-lg' placeholder='Last Name'>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div id="email" class="col-12 mb-2">
                                                                    <div class="row">
                                                                        <div class='col-12 input-group'>
                                                                            <div class='input-group-prepend input-group-lg'>
                                                                                <span class='input-group-text'><i class='fa fa-envelope'></i></span>
                                                                            </div>
                                                                            <input type='text' class='form-control form-control-lg' placeholder='Email Address'>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div id="phoneDiv" class="col-12 mb-2 d-none">
                                                                    <div class="row">
                                                                        <div class='col-12 input-group'>
                                                                            <div class='input-group-prepend input-group-lg'>
                                                                                <span class='input-group-text'><i class='fa fa-phone'></i></span>
                                                                            </div>
                                                                            <input type='text' class='form-control form-control-lg' placeholder='Phone Number'>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div id="humanDiv" class="col-12 mb-2 d-none">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <p class="text-light mb-0">Are You Human? - Vs9o4DKp</p>
                                                                        </div>
                                                                        <div class='col-12 input-group'>
                                                                            <div class='input-group-prepend input-group-lg'>
                                                                                <span class='input-group-text'><i class='fa fa-shield'></i></span>
                                                                            </div>
                                                                            <input type='text' class='form-control form-control-lg' placeholder='Enter Code Above'>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row justify-content-center">
                                            <div class="col-lg-7 mb-3 capture-button">
                                                <img id="page-button-img" src="images/template/button.png" class="img-fluid">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12 mb-3 capture-disclaimer text-center">
                                                <img id="page-disclaimer-img" src="images/template/disclaimer.png" class="img-fluid">
                                                <p id="disclaimerP" class="d-none text-light"><small id="disclaimerText"></small></p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Live Preview End -->

                </div>
            </div>
        </div>

        <?php include_once 'includes/member-footer.php' ?>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 text-center mb-3">
                        <h1 style="color:#CC0001; font-size: 4rem;"><i class="fa fa-exclamation-triangle"></i> ATTENTION <i class="fa fa-exclamation-triangle"></i></h1>
                    </div>
                    <div class="col-12 text-center mb-3">
                        <h4>You are about to DELETE a capture page! Once deleted, this process can not be undone.</h4>
                    </div>
                    <div class="col-12 text-center mb-5">
                        <h3>Do you wish to proceed?</h3>
                    </div>
                    <div class="col-12 d-grid">
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <button type="button" class="btn btn-dark btn-lg close" data-bs-dismiss="modal" aria-label="Close"><i class='fa fa-times-circle'></i> CANCEL</button>
                            <a href="deletePage.php?pageID=<?php echo e($pageID); ?>" class="btn btn-primary btn-lg accordionBtn"> <i class="fa fa-trash"></i> DELETE PAGE</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
/**
 * Close DB connection at end of page lifecycle.
 */
$pdo->close();
?>

<?php include_once 'includes/js.php' ?>
<?php include_once 'includes/js-plugins.php' ?>
<?php include_once 'includes/js-custom.php' ?>
<?php include_once 'includes/live-chat.php' ?>
<script src="js/page-builder.js"></script>

</body>
</html>
