<?php
session_start();
include '../includes/conn.php';
include '../includes/settings.php';
if(isset($_SESSION['user'])){
    header('location: dashboard');
}

$pageTitle = "Capture Page Form";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="pixelstrap">
    <link rel="icon" href="images/favicon.png" type="image/x-icon">
    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
    <title><?php echo $pageTitle ?> | <?php echo SYSTEM_NAME ?></title>

    <?php include_once 'includes/css-fonts.php' ?>
    <?php include_once 'includes/css.php' ?>

</head>
<body id="captureLogin">
<!-- login page start-->
<div class="container fullscreen">
    <div class="row justify-content-center fullscreen">
        <div class="col-lg-5 col-md-8 my-auto">
            <div class="row justify-content-center">
                <div class="col-11 text-center my-3">
                    <a class="logo" href="index.php"><img class="img-fluid" src="images/<?php echo SYSTEM_LOGO ?>" title="<?php echo SYSTEM_NAME ?>" alt="<?php echo SYSTEM_NAME ?>"></a>
                </div>
                <div class="col-12">
                    <div class="login-main login-card card">
                        <div class="card-body">
                            <?php include_once 'includes/alerts.php' ?>
                            <form method="post" action="requestReset.php" class="theme-form">
                                <h3>Request Password Reset</h3>
                                <p>Enter email associated with your account.</p>
                                <div class="form-group">
                                    <label class="col-form-label">Email Address</label>
                                    <input class="form-control" type="email" name="forgotEmail" placeholder="member@gmail.com" required>
                                </div>
                                <div class="form-group mb-0">
                                    <div class="text-end mt-3">
                                        <button class="btn btn-primary btn-lg btn-block w-100" type="submit" name="requestReset"><i class="fa fa-lock"></i> Request Reset</button>
                                    </div>
                                </div>
                                <p class="mt-4 mb-0 text-center"><a class="ms-2" href="index"><i class="fa fa-backward"></i> Back to Login</a></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include_once 'includes/js.php' ?>

</div>
</body>

</html>