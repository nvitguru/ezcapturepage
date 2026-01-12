<?php
/** @var Database $pdo */
session_start();

include '../includes/conn.php';
include '../includes/settings.php';

$uriParts = explode('/', $_SERVER['REQUEST_URI']);
$pageID = (string) end($uriParts);

$conn = $pdo->open();

$countStmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM `pages` WHERE `pageID` = :pageID");
$countStmt->execute(['pageID' => $pageID]);
$counter = $countStmt->fetch();
$count = $counter['numrows'];

$pagesStmt = $conn->prepare("SELECT * FROM `pages` WHERE `pageID` = :pageID");
$pagesStmt->execute(['pageID' => $pageID]);
$pages = $pagesStmt->fetch();

$userStmt = $conn->prepare("SELECT suspend FROM `users` WHERE `userID` = :userID");
$userStmt->execute(['userID' => $pages['userID']]);
$user = $userStmt->fetch();

if ($count = 0) {
    header("Location: https://www.ezcapturepage.com");
    exit;
}

if ($pages['active'] == FALSE OR $user['suspend'] == TRUE) {
    header("Location: oops.php");
    exit;
}

$contentStmt = $conn->prepare("SELECT * FROM `content` WHERE `pageID` = :pageID");
$contentStmt->execute(['pageID' => $pages['pageID']]);
$content = $contentStmt->fetch();

$formStmt = $conn->prepare("SELECT * FROM `form` WHERE `formID` = :formID");
$formStmt->execute(['formID' => $content['formID']]);
$form = $formStmt->fetch();

if ($pages['active'] == TRUE) {
    $metricStmt = $conn->prepare('UPDATE `metrics` SET `view` = `view` + 1 WHERE `pageID` = :pageID;');
    $metricStmt->execute(['pageID' => $pageID]);
} else{
    header("Location: oops.php");
}

$authStmt = $conn->prepare("SELECT * FROM auth ORDER BY RAND() LIMIT 1;");
$authStmt->execute();
$auth = $authStmt->fetch();
?>

<!DOCTYPE html>
<html class="no-js" lang="zxx">
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>

    <link rel="icon" href="../images/ez-capture-page-icon.png" type="image/x-icon">
    <link rel="shortcut icon" href="../images/ez-capture-page-icon.png" type="image/x-icon">

    <!-- Site Title -->
    <title><?php echo $content['tabTitle'] ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .full-screen{
            height: 100vh;
        }
        .card{
            background: rgba(0, 0, 0, 0.9) !important;
        }
        .form-control-lg{
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
    </style
    <?php if($pages['google']){ ?>
            <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $pages['google'] ?>"></script>

    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '<?php echo $pages['google'] ?>');
    </script>
    <?php } ?>
    <?php if($pages['pixel']){ ?>
        <!-- Facebook Pixel Code -->
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)}(window, document,'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '<?php echo $pages['pixel'] ?>');
            fbq('track', 'PageView');
        </script>
        <noscript>
            <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?php echo $pages['pixel'] ?>&ev=PageView&noscript=1"/>
        </noscript>
        <!-- End Facebook Pixel Code -->
    <?php } ?>
</head>

<body id="funnel" class="fullscreen" style="background: url('https://<?php echo SYSTEM_URL ?>/images/background/<?php echo $content['background'] ?>.jpg') center center fixed; background-size: cover;">
<div class="container full-screen">
    <div class="row justify-content-center full-screen">
        <div class="col-lg-6 col-md-8 col-sm-10 my-auto">
            <div class="card my-4">
                <div class="card-body">
                    <div class="row justify-content-center">
                        <?php if($content['header'] != NULL && $content['header'] != 99) { ?>
                            <div class="col-lg-11 text-center mt-2 mb-3">
                                <img src="https://<?php echo SYSTEM_URL ?>/images/header/<?php echo $content['header'] ?>.png" class="img-fluid">
                            </div>
                        <?php } ?>
                        <?php if($content['subheader'] != NULL && $content['subheader'] != 99) { ?>
                            <div class="col-lg-11 text-center mb-3">
                                <img src="https://<?php echo SYSTEM_URL ?>/images/subheader/<?php echo $content['subheader'] ?>.png" class="img-fluid">
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
                    <form method="post" action="">
                        <div class="row justify-content-center">
                            <div class="col-lg-9">
                                <div class="row">
                                    <?php include_once '../includes/alerts.php' ?>
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
                                            <p class="text-light mb-0">Are You Human? - <?php echo $auth['authCode'] ?></p>
                                            <input type="hidden" name="authID" value="<?php echo $auth['id'] ?>">
                                        </div>
                                        <div class='col-12 input-group mb-2'>
                                            <div class='input-group-prepend input-group-lg'>
                                                <span class='input-group-text'><i class='fa fa-shield'></i></span>
                                            </div>
                                            <input type='text' class='form-control form-control-lg' placeholder='Enter Code Above'>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-lg-8 text-center mb-2">
                                <input name="submit" class="submit img-fluid" type="image" src="https://<?php echo SYSTEM_URL ?>/images/button/<?php echo $content['button'] ?>.png" tabindex="503" />
                            </div>
                        </div>
                    </form>
                    <div class="row justify-content-center">
                        <?php if($content['footer'] != NULL && $content['footer'] != 99) { ?>
                            <div class="col-12 text-center mb-2">
                                <img src="https://<?php echo SYSTEM_URL ?>/images/footer/<?php echo $content['footer'] ?>.png" class="img-fluid">
                            </div>
                        <?php } ?>
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
