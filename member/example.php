<?php
/** @var Database $pdo */
session_start();

include 'includes/session.php';
include '../includes/settings.php';

if (!isset($_SESSION['user'])) {
    header('location: index');
}

$pageID = $_GET['pageID'];

$conn = $pdo->open();

$pagesStmt = $conn->prepare("SELECT * FROM `pages` WHERE `pageID` = :pageID");
$pagesStmt->execute(['pageID' => $pageID]);
$pages = $pagesStmt->fetch();

$contentStmt = $conn->prepare("SELECT * FROM `content` WHERE `pageID` = :pageID");
$contentStmt->execute(['pageID' => $pages['pageID']]);
$content = $contentStmt->fetch();

$formStmt = $conn->prepare("SELECT * FROM `form` WHERE `formID` = :formID");
$formStmt->execute(['formID' => $content['formID']]);
$form = $formStmt->fetch();
?>

<!DOCTYPE html>
<html class="no-js" lang="zxx">
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>

    <!-- Site Title -->
    <title><?php echo $content['tabTitle'] ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/example.css">
</head>

<body id="funnel" class="fullscreen" style="background: url('../images/background/<?php echo $content['background'] ?>.jpg') center center fixed; background-size: cover;">
<div class="container full-screen">
    <div class="row justify-content-center full-screen py-3">
        <div class="col-lg-6 col-md-8 col-sm-10 my-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-center">
                        <?php if($content['header'] != NULL && $content['header'] != 99) { ?>
                            <div class="col-lg-11 text-center mt-2 mb-3">
                                <img src="../images/header/<?php echo $content['header'] ?>.png" class="img-fluid">
                            </div>
                        <?php } ?>
                        <?php if($content['subheader'] != NULL && $content['subheader'] != 99) { ?>
                            <div class="col-lg-11 text-center mb-3">
                                <img src="../images/subheader/<?php echo $content['subheader'] ?>.png" class="img-fluid">
                            </div>
                        <?php } ?>
                        <?php if($content['video'] != NULL && $content['video'] != 99) { ?>
                            <div class="col-lg-11 mb-3">
                                <div id="videoDiv" class="embed-responsive embed-responsive-16by9">
                                    <iframe width="100%" height="400" id="videoSource" class="embed-responsive-item" src="<?php echo $content['video'] ?>"></iframe>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <form>
                                <div class="row">
                                    <?php if($form['formName'] = 1) {
                                        echo "
                                        <div class='col-12 input-group mb-2'>
                                            <div class='input-group-prepend input-group-lg'>
                                                <span class='input-group-text'><i class='fa fa-user'></i></span>
                                            </div>
                                            <input type='text' class='form-control form-control-lg' placeholder='Full Name'>
                                        </div>
                                        ";
                                     } elseif($form['formName'] = 2) {
                                        echo "
                                            <div class='col-12 input-group mb-2'>
                                                <div class='input-group-prepend input-group-lg'>
                                                    <span class='input-group-text'><i class='fa fa-user'></i></span>
                                                </div>
                                                <input type='text' class='form-control form-control-lg' placeholder='First Name'>
                                            </div>
                                            <div class='col-12 input-group mb-2'>
                                                <div class='input-group-prepend input-group-lg'>
                                                    <span class='input-group-text'><i class='fa fa-user'></i></span>
                                                </div>
                                                <input type='text' class='form-control form-control-lg' placeholder='Last Name'>
                                            </div>
                                        ";
                                    } else {
                                        "";
                                    }
                                     ?>
                                    <div class='col-12 input-group mb-2'>
                                        <div class='input-group-prepend input-group-lg'>
                                            <span class='input-group-text'><i class='fa fa-envelope'></i></span>
                                        </div>
                                        <input type='text' class='form-control form-control-lg' placeholder='Email Address'>
                                    </div>
                                    <?php if($form['formPhone']){ ?>
                                        <div class='col-12 input-group mb-2'>
                                            <div class='input-group-prepend input-group-lg'>
                                                <span class='input-group-text'><i class='fa fa-phone'></i></span>
                                            </div>
                                            <input type='text' class='form-control form-control-lg' placeholder='Phone Number'>
                                        </div>
                                    <?php } ?>
                                    <?php if($form['formHuman']){ ?>
                                        <div class="col-12">
                                            <p class="text-light mb-0">Are You Human? - Vs9o4DKp</p>
                                        </div>
                                        <div class='col-12 input-group mb-2'>
                                            <div class='input-group-prepend input-group-lg'>
                                                <span class='input-group-text'><i class='fa fa-shield'></i></span>
                                            </div>
                                            <input type='text' class='form-control form-control-lg' placeholder='Enter Code Above'>
                                        </div>
                                    <?php } ?>
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-8 text-center mb-2">
                            <img src="../images/button/<?php echo $content['button'] ?>.png" class="img-fluid">
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <?php if($content['disclaimer']) { ?>
                            <div class="col-12 text-center mb-2">
                                <p style="color: #ffffff;"><?php echo $content['disclaimer'] ?></p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js" integrity="sha512-u3fPA7V8qQmhBPNT5quvaXVa1mnnLSXUep5PS1qo5NRzHwG19aHmNJnj1Q8hpA/nBWZtZD4r4AX6YOt5ynLN2g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

</body>

</html>
