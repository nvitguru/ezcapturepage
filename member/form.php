<?php
/** @var Database $pdo */
session_start();

$pageTitle = "Capture Page Form";
$tab = "form";

include '../includes/session.php';
include '../includes/settings.php';

if (!isset($_SESSION['user'])) {
    header('location: index.php');
}

$conn = $pdo->open();

$formStmt = $conn->prepare("SELECT * FROM `form` WHERE `formID` = :formID");
$formStmt->execute(['formID' => $_GET['formID']]);
$form = $formStmt->fetch();

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
                                            <?php if($form['active']){ ?>
                                                <a class="btn btn-dark btn-sm" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Attention! This form is currently Active. To delete, please deactivate the form first." disabled>
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

                    <!-- Create Page Form Start -->
                    <div class='col-lg-5 box-col-4'>
                        <form class="theme-form" method="post" action="editForm.php">
                            <input type="hidden" name="formID" value="<?php echo $form['formID'] ?>">
                            <div class="row justify-content-center">

                                <div class="col-12 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <h3>General Information</h3>
                                                </div>
                                                <div class="col-lg-6 form-group mb-3">
                                                    <label>Form Name</label>
                                                    <input id="formName" type="text" class="form-control form-control-lg" value="<?php echo $form['name'] ?>" name="name">
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>Form CRM</label>
                                                    <select class="form-select form-select-lg" name="crm">
                                                        <option value="0"> - Internal CRM - </option>
                                                        <?php
                                                        $conn = $pdo->open();

                                                        try {
                                                            $crmStmt = $conn->prepare("SELECT * FROM `crm` WHERE `active` = TRUE AND `userID` = :userID");
                                                            $crmStmt->execute(['userID'=> $user['userID']]);
                                                            $formCRM = $crmStmt->fetchAll();
                                                            foreach ($formCRM as $row) {
                                                                $selected = $row['crmID'] == $form['crm'] ? "selected" : "";
                                                                echo "
                                                                    <option value='". $row['crmID'] ."' ". $selected ."> ". $row['name'] ." </option>";
                                                            }
                                                        } catch (PDOException $e) {
                                                            echo $e->getMessage();
                                                        }

                                                        $pdo->close();
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="formBuilder" class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <h3>Form Layout</h3>
                                                    <p class="mx-0"><small>Email Address is required for forms and defaulted on.</small></p>
                                                    <hr>
                                                </div>

                                                <!-- Form Name Selection -->
                                                <div class="col-12">
                                                    <label>Form Name Field</label>
                                                </div>
                                                <div class="form-check col-12 mb-0 ps-5">
                                                    <input id="noName" class="form-check-input" type="radio" value="0" name="formName" <?php echo $form['formName'] == 0 ? "checked" : "" ?>>
                                                    <label class="form-check-label mt-1 ms-2" for="0">NO NAME</label>
                                                </div>
                                                <div class="form-check col-12 mb-0 ps-5">
                                                    <input id="fullName" class="form-check-input" type="radio" value="1" name="formName" <?php echo $form['formName'] == 1 ? "checked" : "" ?>>
                                                    <label class="form-check-label mt-1 ms-2" for="0">FULL NAME</label>
                                                </div>
                                                <div class="form-check col-12 mb-0 ps-5">
                                                    <input id="splitName" class="form-check-input" type="radio" value="2" name="formName" <?php echo $form['formName'] == 2 ? "checked" : "" ?>>
                                                    <label class="form-check-label mt-1 ms-2" for="0">FIRST & LAST NAME</label>
                                                </div>

                                                <!-- Phone Number Selection -->
                                                <div class="col-12 mt-4">
                                                    <label>Form Phone Field</label>
                                                </div>
                                                <div class="form-check col-12 mb-0 ps-5">
                                                    <input id="phone" class="form-check-input" type="checkbox" value="1" name="formPhone" <?php echo $form['formPhone'] == TRUE ? "checked" : "" ?>>
                                                    <label class="form-check-label mt-1 ms-2" for="0">Phone Number</label>
                                                </div>

                                                <!-- Human Selection -->
                                                <div class="col-12 mt-4">
                                                    <label>Form Human Check Field</label>
                                                </div>
                                                <div class="form-check col-12 mb-0 ps-5">
                                                    <input id="human" class="form-check-input" type="checkbox" value="1" name="formHuman" <?php echo $form['formHuman'] == TRUE ? "checked" : "" ?>>
                                                    <label class="form-check-label mt-1 ms-2" for="0">Human Check</label>
                                                </div>
                                            </div>
                                            <div class="row justify-content-center">
                                                <div class="form-check col-lg-6 mt-4 d-grid">
                                                    <button class="btn btn-lg btn-primary" type="submit" value="1" name="editForm"><i class="fa fa-thumbs-up"></i> Save Form</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- Create Page Form End -->

                    <!-- Display Page Form Start -->
                    <div class='col-lg-4 box-col-4'>
                        <div class="row justify-content-center">
                            <div class="col-lg-10 col-md-11 page-content">
                                <div class="card">
                                    <div class="card-body">
                                        <form>
                                            <div class="row">
                                                <div id="fullNameDiv" class="col-12 mb-3 d-none">
                                                    <div class="form-group">
                                                        <label class="text-light">Full Name</label>
                                                        <input type="text" class="form-control form-control-lg" placeholder="Full Name">
                                                    </div>
                                                </div>
                                                <div id="splitNameDiv" class="col-12 mb-3 d-none">
                                                    <div class="form-group mb-3">
                                                        <label class="text-light">First Name</label>
                                                        <input type="text" class="form-control form-control-lg" placeholder="First Name">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="text-light">Last Name</label>
                                                        <input type="text" class="form-control form-control-lg" placeholder="Last Name">
                                                    </div>
                                                </div>
                                                <div id="email" class="col-12 mb-3">
                                                    <div class="form-group">
                                                        <label class="text-light">Email Address</label>
                                                        <input type="email" class="form-control form-control-lg" placeholder="Email Address">
                                                    </div>
                                                </div>
                                                <div id="phoneDiv" class="col-12 mb-3 d-none">
                                                    <div class="form-group">
                                                        <label class="text-light">Phone Number</label>
                                                        <input type="tel" class="form-control form-control-lg" placeholder="Phone Number">
                                                    </div>
                                                </div>
                                                <div id="humanDiv" class="col-12 mb-3 d-none">
                                                    <div class="form-group">
                                                        <label class="text-light">Are You Human? - Vs9o4DKp</label>
                                                        <input type="text" class="form-control form-control-lg" placeholder="Enter Code Above">
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Display Page Form End -->

                </div>
            </div>
            <!-- Container-fluid Ends-->
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
                        <h4>You are about to DELETE a capture form! Once deleted, this process can not be undone.</h4>
                    </div>
                    <div class="col-12 text-center mb-5">
                        <h3>Do you wish to proceed?</h3>
                    </div>
                    <div class="col-12 d-grid">
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <button type="button" class="btn btn-dark btn-lg close" data-bs-dismiss="modal" aria-label="Close"><i class='fa fa-times-circle'></i> CANCEL</button>
                            <a href="deleteForm.php?formID=<?php echo $form['formID'] ?>" class="btn btn-primary btn-lg"> <i class="fa fa-trash"></i> DELETE FORM</a>
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
<script src="js/form-builder.js"></script>

<script>new WOW().init();</script>

</body>

</html>