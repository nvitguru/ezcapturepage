<?php
/** @var Database $pdo */
session_start();

$pageTitle = "Capture Page CRM";
$tab = "crm";

include '../includes/session.php';
include '../includes/settings.php';

if (!isset($_SESSION['user'])) {
    header('location: index');
}

$conn = $pdo->open();

$crmStmt = $conn->prepare("SELECT * FROM `crm` WHERE `crmID` = :crmID");
$crmStmt->execute(['crmID' => $_GET['crmID']]);
$crm = $crmStmt->fetch();

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
                                    <h3>
                                        <?php echo $pageTitle ?>
                                        <span class="float-end">
                                            <?php if($crm['active']){ ?>
                                                <a class="btn btn-dark btn-sm" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Attention! This CRM is currently Active. To delete, please deactivate the CRM first." disabled>
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
                                        <li class="breadcrumb-item active"><?php echo $pageTitle ?></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php include_once 'includes/alerts.php' ?>

                    <form class="theme-form" method="post" action="editCRM.php">
                        <input type="hidden" name="crmID" value="<?php echo $crm['crmID'] ?>">
                        <div class="row">
                            <!-- CRM Selection Start -->
                            <div class='col-lg-3 box-col-4'>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row justify-content-center">
                                            <div class="form-check col-12 mb-3 position-relative position-relative">
                                                <input class="form-check-input d-none" type="radio" value="0" id="systemRadio" name="crmType" <?php echo $crm['crmType'] == 0 ? "checked" : "" ?>>
                                                <label class="form-check-label crm-label" for="systemRadio">
                                                    <img id="systemImg" src="images/system-btn.png" class="img-fluid">
                                                </label>
                                            </div>
                                            <div class="form-check col-12 mb-3 position-relative position-relative">
                                                <input class="form-check-input d-none" type="radio" value="1" id="aweberRadio" name="crmType" <?php echo $crm['crmType'] == 1 ? "checked" : "" ?>>
                                                <label class="form-check-label crm-label" for="aweberRadio">
                                                    <img id="aweberImg" src="images/aweber-btn.png" class="img-fluid">
                                                </label>
                                            </div>
                                            <div class="form-check col-12 position-relative position-relative">
                                                <input class="form-check-input d-none" type="radio" value="2" id="responseRadio" name="crmType" <?php echo $crm['crmType'] == 2 ? "checked" : "" ?>>
                                                <label class="form-check-label crm-label" for="responseRadio">
                                                    <img id="responseImg" src="images/response-btn.png" class="img-fluid">
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
                                                        <input id="crmName" type="text" class="form-control form-control-lg" value="<?php echo $crm['name'] ?>" name="name">
                                                    </div>
                                                    <div class="col-12 form-group mb-3">
                                                        <label id="crmCodeLabel">CRM Code</label>
                                                        <input id="crmCode" type="text" class="form-control form-control-lg" value="<?php echo $crm['crmCode'] ?>" name="crmCode">
                                                    </div>
                                                    <div class="col-12 form-group">
                                                        <label>Submission Redirect</label>
                                                        <input id="crmRedirect" type="text" class="form-control form-control-lg" value="<?php echo $crm['crmRedirect'] ?>" name="crmRedirect">
                                                    </div>
                                                    <div class="col-12 my-3">
                                                        <hr>
                                                    </div>
                                                </div>
                                                <div class="row justify-content-center">
                                                    <div class="form-check col-lg-10 d-grid">
                                                        <button id="crmSubmit" class="btn btn-lg btn-primary" type="submit" name="editSystemCRM"><i class="fa fa-thumbs-up"></i> Save CRM</button>
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

        <?php include_once 'includes/member-footer.php' ?>

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
                                <a href="deleteCRM.php?crmID=<?php echo $crm['crmID'] ?>" class="btn btn-primary btn-lg"> <i class="fa fa-trash"></i> DELETE CRM</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/js.php' ?>
<?php include_once 'includes/js-plugins.php' ?>
<?php include_once 'includes/js-custom.php' ?>
<?php include_once 'includes/live-chat.php' ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to remove crmActive from all images
        function clearActiveClasses() {
            document.querySelectorAll('.crm-label img').forEach(function(img) {
                img.classList.remove('crmActive');
            });
        }

        // Function to clear the values of specific elements
        function clearFormValues() {
            document.getElementById('crmName').value = '';
            document.getElementById('crmCode').value = '';
            document.getElementById('crmRedirect').value = '';
        }

        // Function to handle the visibility of the form and setting the crmCodeLabel
        function handleCrmTypeChange(selectedType) {
            const crmFormDiv = document.getElementById('crmFormDiv');
            const submitButton = document.getElementById('crmSubmit');
            const crmCodeLabel = document.getElementById('crmCodeLabel');

            crmFormDiv.classList.add('d-none');

            if (selectedType === 'system') {
                crmCodeLabel.textContent = "CRM Code";
                submitButton.name = 'editSystemCRM';
            } else {
                crmFormDiv.classList.remove('d-none');
                submitButton.classList.remove('d-none');
                if (selectedType === 'aweber') {
                    crmCodeLabel.textContent = "Aweber List Code";
                    submitButton.name = 'editAweberCRM';
                } else if (selectedType === 'response') {
                    crmCodeLabel.textContent = "Campaign List Token";
                    submitButton.name = 'editResponseCRM';
                }
            }
        }

        // Event listeners for radio buttons
        document.querySelectorAll('input[name="crmType"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                clearActiveClasses();
                document.getElementById(radio.id.replace('Radio', 'Img')).classList.add('crmActive');
                handleCrmTypeChange(radio.id.replace('Radio', ''));
                clearFormValues();
            });
        });

        // When the page loads, add crmActive to the current selected radio button's image
        const currentCrmType = <?php echo json_encode($crm['crmType']); ?>;
        if (currentCrmType !== null) {
            const crmTypeMap = ['system', 'aweber', 'response'];
            document.getElementById(crmTypeMap[currentCrmType] + 'Img').classList.add('crmActive');
            document.getElementById(crmTypeMap[currentCrmType] + 'Radio').checked = true;
            handleCrmTypeChange(crmTypeMap[currentCrmType]);
        }
    });

</script>

<script>new WOW().init();</script>

</body>

</html>
