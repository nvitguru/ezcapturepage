<?php
/**
 * Forgot Password Page
 *
 * Responsibilities:
 * - Prevent authenticated users from accessing password reset flow
 * - Display password reset request form
 * - Delegate reset logic to requestReset.php
 *
 * NOTE:
 * This page intentionally contains NO business logic.
 * All reset handling, token generation, and email delivery
 * are handled server-side in requestReset.php.
 */

session_start();

/**
 * Core configuration and database connection
 * - conn.php: initializes $pdo
 * - settings.php: system constants (name, logo, etc.)
 */
include_once '../includes/conn.php';
include_once '../includes/settings.php';

/**
 * Security gate:
 * If a user is already authenticated, they should not
 * access the forgot-password flow.
 */
if (isset($_SESSION['user'])) {
    header('location: dashboard');
    exit;
}

/**
 * Page metadata
 * Used for <title> and header rendering
 */
$pageTitle = "Forgot Password";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Optional SEO placeholders -->
    <meta name="description" content="">
    <meta name="author" content="pixelstrap">

    <!-- Favicon -->
    <link rel="icon" href="images/favicon.png" type="image/x-icon">
    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">

    <!-- Dynamic page title -->
    <title><?php echo $pageTitle ?> | <?php echo SYSTEM_NAME ?></title>

    <!-- Global fonts + theme CSS -->
    <?php include_once 'includes/css-fonts.php' ?>
    <?php include_once 'includes/css.php' ?>
</head>

<body id="captureLogin">
<!-- Login page container -->
<div class="container fullscreen">
    <div class="row justify-content-center fullscreen">
        <div class="col-lg-5 col-md-8 my-auto">

            <div class="row justify-content-center">

                <!-- Logo -->
                <div class="col-11 text-center my-3">
                    <a class="logo" href="index.php">
                        <img class="img-fluid"
                             src="images/<?php echo SYSTEM_LOGO ?>"
                             title="<?php echo SYSTEM_NAME ?>"
                             alt="<?php echo SYSTEM_NAME ?>">
                    </a>
                </div>

                <!-- Forgot password card -->
                <div class="col-12">
                    <div class="login-main login-card card">
                        <div class="card-body">

                            <!-- Flash alerts (success / error messages) -->
                            <?php include_once 'includes/alerts.php' ?>

                            <!-- Password reset request form -->
                            <form method="post"
                                  action="requestReset.php"
                                  class="theme-form">

                                <h3>Request Password Reset</h3>
                                <p>Enter email associated with your account.</p>

                                <!-- Email input -->
                                <div class="form-group">
                                    <label class="col-form-label">Email Address</label>
                                    <input class="form-control"
                                           type="email"
                                           name="forgotEmail"
                                           placeholder="member@gmail.com"
                                           required>
                                </div>

                                <!-- Submit -->
                                <div class="form-group mb-0">
                                    <div class="text-end mt-3">
                                        <button class="btn btn-primary btn-lg btn-block w-100"
                                                type="submit"
                                                name="requestReset">
                                            <i class="fa fa-lock"></i> Request Reset
                                        </button>
                                    </div>
                                </div>

                                <!-- Navigation -->
                                <p class="mt-4 mb-0 text-center">
                                    <a class="ms-2" href="index">
                                        <i class="fa fa-backward"></i> Back to Login
                                    </a>
                                </p>

                            </form>
                            <!-- /form -->

                        </div>
                    </div>
                </div>
                <!-- /card -->

            </div>
        </div>
    </div>

    <!-- Global JS -->
    <?php include_once 'includes/js.php' ?>
</div>
</body>
</html>
