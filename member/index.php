<?php
session_start();
include '../includes/session.php';
include '../includes/settings.php';
if(isset($_SESSION['user'])){
    header('location: dashboard.php');
}
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
    <title><?php echo SYSTEM_NAME ?></title>

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
                            <div class='row <?php echo $showMobile ?>'>
                                <div class='col-12 mb-2'>
                                    <div class='alert alert-warning'>
                                        <i class='fa fa-exclamation-triangle'></i> Your are accessing your back office on a mobile device. Some features may not be available in mobile. For access to all features, please log into your back office on a laptop or desktop device.
                                    </div>
                                </div>
                            </div>
                            <?php include_once 'includes/alerts.php' ?>
                            <form method="post" action="verify.php" class="theme-form">
                                <h3>Sign in to your account</h3>
                                <p>Enter your email & password to login</p>
                                <div class="form-group">
                                    <label class="col-form-label">Email Address</label>
                                    <input class="form-control" type="email" name="loginEmail" placeholder="member@gmail.com" required>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">Password</label>
                                    <div class="form-input position-relative">
                                        <input id="password" class="form-control" type="password" name="loginPassword" placeholder="*********" required>
                                        <i id="showPass" class="fa fa-eye" onclick="togglePassVisibility()"></i>
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <a class="link" href="forgot-password">Forgot password?</a>
                                    <div class="text-end mt-3">
                                        <button class="btn btn-primary btn-lg btn-block w-100" type="submit" name="memberLogin"><i class="fa fa-lock"></i> Sign in</button>
                                    </div>
                                </div>
                                <p class="mt-4 mb-0 text-center">Don't have account?<a class="ms-2" href="register">Create Account</a></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include_once 'includes/js.php' ?>
    <script>

        function togglePassVisibility() {
            var pincodeInput = document.getElementById('password');
            var showPinIcon = document.getElementById('showPass');
            if (pincodeInput.type === 'password') {
                pincodeInput.type = 'text';
                showPinIcon.classList.remove('fa-eye');
                showPinIcon.classList.add('fa-eye-slash');
            } else {
                pincodeInput.type = 'password';
                showPinIcon.classList.remove('fa-eye-slash');
                showPinIcon.classList.add('fa-eye');
            }
        }
    </script>
</div>
</body>

</html>