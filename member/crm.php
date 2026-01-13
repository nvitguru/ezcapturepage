<?php
/**
 * crm.php
 *
 * Responsibilities:
 * - Enforce authenticated access
 * - Load CRM record by crmID (scoped to current user)
 * - Render CRM editor UI + delete modal
 *
 * Notes:
 * - Relies on includes to hydrate: $pdo, $user, SYSTEM_NAME, etc.
 */

/** @var Database $pdo */
session_start();

$pageTitle = "Capture Page CRM";
$tab = "crm";

include '../includes/session.php';
include '../includes/settings.php';

// -----------------------------------------------------------------------------
// Auth gate
// -----------------------------------------------------------------------------
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// -----------------------------------------------------------------------------
// Output escaping helper
// -----------------------------------------------------------------------------
$e = static function ($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};

// -----------------------------------------------------------------------------
// Input: crmID (sanitized)
// -----------------------------------------------------------------------------
$crmID = filter_input(INPUT_GET, 'crmID', FILTER_SANITIZE_STRING);
$crmID = is_string($crmID) ? trim($crmID) : '';

if ($crmID === '') {
    $_SESSION['error'] = 'Missing CRM ID.';
    header('Location: dashboard');
    exit;
}

// -----------------------------------------------------------------------------
// DB: load CRM scoped to the logged-in user (prevents IDOR)
// -----------------------------------------------------------------------------
try {
    $conn = $pdo->open();

    // IMPORTANT: Scope by userID to prevent unauthorized access by guessing crmID
    $crmStmt = $conn->prepare("
        SELECT *
        FROM `crm`
        WHERE `crmID` = :crmID
          AND `userID` = :userID
        LIMIT 1
    ");
    $crmStmt->execute([
        'crmID'  => $crmID,
        'userID' => (int)$user['userID'],
    ]);

    $crm = $crmStmt->fetch();

    if (!$crm) {
        $_SESSION['error'] = 'CRM not found.';
        $pdo->close();
        header('Location: dashboard');
        exit;
    }
} catch (PDOException $ex) {
    error_log('crm.php load error (crmID=' . $crmID . '): ' . $ex->getMessage());
    $_SESSION['error'] = 'Unable to load CRM right now.';
    $pdo->close();
    header('Location: dashboard');
    exit;
}

$pdo->close();
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
<!-- tap on tap ends-->
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
                    <div class="col-12">
                        <div class="page-title mt-2">
                            <div class="row">
                                <div class="col-sm-6 ps-0">
                                    <h3>
                                        <?php echo $e($pageTitle); ?>
                                        <span class="float-end">
                                            <?php if (!empty($crm['active'])) { ?>
                                                <a class="btn btn-dark btn-sm"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="bottom"
                                                   title="Attention! This CRM is currently Active. To delete, please deactivate the CRM first."
                                                   disabled>
                                                    <i class="fa fa-trash"></i> Delete
                                                </a>
                                            <?php } else { ?>
                                                <a href="#deleteModal" data-bs-toggle="modal" class="btn btn-dark btn-sm">
                                                    <i class="fa fa-trash"></i> Delete
                                                </a>
                                            <?php } ?>
                                        </span>
                                    </h3>
                                </div>
                                <div class="col-sm-6 pe-0">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="dashboard.php">
                                                <i class="fa fa-home stroke-icon"></i>
                                            </a></li>
                                        <li class="breadcrumb-item active"><?php echo $e($pageTitle); ?></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php include_once 'includes/alerts.php'; ?>

                    <form class="theme-form" method="post" action="editCRM.php">
                        <input type="hidden" name="crmID" value="<?php echo $e($crm['crmID']); ?>">
                        <div class="row">
                            <!-- CRM Selection Start -->
                            <div class='col-lg-3 box-col-4'>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row justify-content-center">
                                            <div class="form-check col-12 mb-3 position-relative position-relative">
                                                <input class="form-check-input d-none"
                                                       type="radio"
                                                       value="0"
                                                       id="systemRadio"
                                                       name="crmType"
                                                    <?php echo ((int)$crm['crmType'] === 0) ? "checked" : ""; ?>>
                                                <label class="form-check-label crm-label" for="systemRadio">
                                                    <img id="systemImg" src="images/system-btn.png" class="img-fluid" alt="System CRM">
                                                </label>
                                            </div>
                                            <div class="form-check col-12 mb-3 position-relative position-relative">
                                                <input class="form-check-input d-none"
                                                       type="radio"
                                                       value="1"
                                                       id="aweberRadio"
                                                       name="crmType"
                                                    <?php echo ((int)$crm['crmType'] === 1) ? "checked" : ""; ?>>
                                                <label class="form-check-label crm-label" for="aweberRadio">
                                                    <img id="aweberImg" src="images/aweber-btn.png" class="img-fluid" alt="Aweber CRM">
                                                </label>
                                            </div>
                                            <div class="form-check col-12 position-relative position-relative">
                                                <input class="form-check-input d-none"
                                                       type="radio"
                                                       value="2"
                                                       id="responseRadio"
                                                       name="crmType"
                                                    <?php echo ((int)$crm['crmType'] === 2) ? "checked" : ""; ?>>
                                                <label class="form-check-label crm-label" for="responseRadio">
                                                    <img id="responseImg" src="images/response-btn.png" class="img-fluid" alt="GetResponse CRM">
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- CRM Selection End -->

                            <!-- CRM Form Start -->
                            <div class='col-lg-4'>
                                <div class="row justify-content-center">

                                    <div class="col-12 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <div id="crmFormDiv" class="row d-none">
                                                    <div class="col-12 mb-3">
                                                        <h3>CRM Information</h3>
                                                    </div>
                                                    <div class="col-12 form-group mb-3">
                                                        <label>CRM Name</label>
                                                        <input id="crmName" type="text" class="form-control form-control-lg"
                                                               value="<?php echo $e($crm['name']); ?>" name="name">
                                                    </div>
                                                    <div class="col-12 form-group mb-3">
                                                        <label id="crmCodeLabel">CRM Code</label>
                                                        <input id="crmCode" type="text" class="form-control form-control-lg"
                                                               value="<?php echo $e($crm['crmCode']); ?>" name="crmCode">
                                                    </div>
                                                    <div class="col-12 form-group">
                                                        <label>Submission Redirect</label>
                                                        <input id="crmRedirect" type="text" class="form-control form-control-lg"
                                                               value="<?php echo $e($crm['crmRedirect']); ?>" name="crmRedirect">
                                                    </div>
                                                    <div class="col-12 my-3">
                                                        <hr>
                                                    </div>
                                                </div>
                                                <div class="row justify-content-center">
                                                    <div class="form-check col-lg-10 d-grid">
                                                        <button id="crmSubmit" class="btn btn-lg btn-primary" type="submit" name="editSystemCRM">
                                                            <i class="fa fa-thumbs-up"></i> Save CRM
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- CRM End -->

                        </div>
                    </form>

                </div>
            </div>
            <!-- Container-fluid Ends-->
        </div>

        <?php include_once 'includes/member-footer.php'; ?>

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
                            <h4>You are about to DELETE a CRM! Once deleted, this process can not be undone.</h4>
                        </div>
                        <div class="col-12 text-center mb-5">
                            <h3>Do you wish to proceed?</h3>
                        </div>
                        <div class="col-12 d-grid">
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <button type="button" class="btn btn-dark btn-lg close" data-bs-dismiss="modal" aria-label="Close"><i class='fa fa-times-circle'></i> CANCEL</button>
                                <a href="deleteCRM.php?crmID=<?php echo rawurlencode((string)$crm['crmID']); ?>" class="btn btn-primary btn-lg">
                                    <i class="fa fa-trash"></i> DELETE CRM
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/js.php'; ?>
<?php include_once 'includes/js-plugins.php'; ?>
<?php include_once 'includes/js-custom.php'; ?>
<?php include_once 'includes/live-chat.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function clearActiveClasses() {
            document.querySelectorAll('.crm-label img').forEach(function(img) {
                img.classList.remove('crmActive');
            });
        }

        function clearFormValues() {
            const crmName = document.getElementById('crmName');
            const crmCode = document.getElementById('crmCode');
            const crmRedirect = document.getElementById('crmRedirect');

            if (crmName) crmName.value = '';
            if (crmCode) crmCode.value = '';
            if (crmRedirect) crmRedirect.value = '';
        }

        function handleCrmTypeChange(selectedType) {
            const crmFormDiv = document.getElementById('crmFormDiv');
            const submitButton = document.getElementById('crmSubmit');
            const crmCodeLabel = document.getElementById('crmCodeLabel');

            if (!crmFormDiv || !submitButton || !crmCodeLabel) return;

            crmFormDiv.classList.add('d-none');

            if (selectedType === 'system') {
                crmCodeLabel.textContent = "CRM Code";
                submitButton.name = 'editSystemCRM';
            } else {
                crmFormDiv.classList.remove('d-none');

                if (selectedType === 'aweber') {
                    crmCodeLabel.textContent = "Aweber List Code";
                    submitButton.name = 'editAweberCRM';
                } else if (selectedType === 'response') {
                    crmCodeLabel.textContent = "Campaign List Token";
                    submitButton.name = 'editResponseCRM';
                }
            }
        }

        document.querySelectorAll('input[name="crmType"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                clearActiveClasses();

                const imgId = radio.id.replace('Radio', 'Img');
                const img = document.getElementById(imgId);
                if (img) img.classList.add('crmActive');

                handleCrmTypeChange(radio.id.replace('Radio', ''));
                clearFormValues();
            });
        });

        // Initialize current selection on load
        const currentCrmType = <?php echo json_encode((int)$crm['crmType']); ?>;
        const crmTypeMap = ['system', 'aweber', 'response'];

        if (currentCrmType >= 0 && currentCrmType < crmTypeMap.length) {
            const key = crmTypeMap[currentCrmType];

            const img = document.getElementById(key + 'Img');
            const radio = document.getElementById(key + 'Radio');

            if (img) img.classList.add('crmActive');
            if (radio) radio.checked = true;

            handleCrmTypeChange(key);
        }
    });
</script>

<script>new WOW().init();</script>

</body>
</html>
