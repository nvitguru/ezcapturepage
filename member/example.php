<?php
/**
 * example.php
 *
 * Purpose:
 * - Render a preview of a member's capture page in the dashboard ("example view")
 *
 * Security:
 * - Requires authenticated session
 * - Ensures the requested page belongs to the authenticated user (prevents IDOR)
 * - Escapes all output
 * - Validates iframe URL (basic)
 */

/** @var Database $pdo */
session_start();

include 'includes/session.php';
include '../includes/settings.php';

// -----------------------------------------------------------------------------
// Redirect helper
// -----------------------------------------------------------------------------
$redirect = static function (string $to): void {
    header('Location: ' . $to);
    exit;
};

// -----------------------------------------------------------------------------
// Auth gate
// -----------------------------------------------------------------------------
if (!isset($_SESSION['user']) || empty($user['userID'])) {
    $redirect('index');
}

// -----------------------------------------------------------------------------
// Input: pageID (required)
// -----------------------------------------------------------------------------
$pageID = isset($_GET['pageID']) ? trim((string)$_GET['pageID']) : '';
if ($pageID === '') {
    $_SESSION['error'] = 'Missing page ID.';
    $redirect('dashboard.php');
}

$userID = (int)$user['userID'];

// -----------------------------------------------------------------------------
// Helpers
// -----------------------------------------------------------------------------
$e = static function ($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};

// Ensure we only ever print a safe filename-like token (prevents path traversal)
$safeToken = static function ($value): string {
    $value = trim((string)$value);
    $value = basename($value);                 // strips any path
    $value = preg_replace('/[^a-zA-Z0-9_-]/', '', $value);
    return $value;
};

$isValidUrl = static function ($url): bool {
    if (!is_string($url) || trim($url) === '') return false;
    $url = trim($url);
    if (!filter_var($url, FILTER_VALIDATE_URL)) return false;
    $parts = parse_url($url);
    return isset($parts['scheme'], $parts['host']) && in_array(strtolower($parts['scheme']), ['http', 'https'], true);
};

// -----------------------------------------------------------------------------
// DB fetch (pages + content + form)
// -----------------------------------------------------------------------------
try {
    $conn = $pdo->open();
} catch (PDOException $ex) {
    error_log('example.php DB connect error: ' . $ex->getMessage());
    $_SESSION['error'] = 'Database connection failed.';
    $redirect('dashboard.php');
}

// page must belong to current user
$pagesStmt = $conn->prepare("SELECT * FROM `pages` WHERE `pageID` = :pageID AND `userID` = :userID LIMIT 1");
$pagesStmt->execute(['pageID' => $pageID, 'userID' => $userID]);
$pages = $pagesStmt->fetch(PDO::FETCH_ASSOC);

if (!$pages) {
    $pdo->close();
    $_SESSION['error'] = 'Page not found or you do not have permission to view it.';
    $redirect('dashboard.php');
}

$contentStmt = $conn->prepare("SELECT * FROM `content` WHERE `pageID` = :pageID LIMIT 1");
$contentStmt->execute(['pageID' => $pages['pageID']]);
$content = $contentStmt->fetch(PDO::FETCH_ASSOC);

if (!$content) {
    $pdo->close();
    $_SESSION['error'] = 'Page content not found.';
    $redirect('dashboard.php');
}

$formStmt = $conn->prepare("SELECT * FROM `form` WHERE `formID` = :formID LIMIT 1");
$formStmt->execute(['formID' => $content['formID']]);
$form = $formStmt->fetch(PDO::FETCH_ASSOC);

if (!$form) {
    $pdo->close();
    $_SESSION['error'] = 'Form configuration not found.';
    $redirect('dashboard.php');
}

// -----------------------------------------------------------------------------
// Derived display values
// -----------------------------------------------------------------------------
$tabTitle = $content['tabTitle'] ?? 'Capture Page';

$backgroundToken = $safeToken($content['background'] ?? '');
$headerToken     = $safeToken($content['header'] ?? '');
$subheaderToken  = $safeToken($content['subheader'] ?? '');
$buttonToken     = $safeToken($content['button'] ?? '');

$videoUrl = trim((string)($content['video'] ?? ''));
$hasHeader    = !empty($headerToken) && $headerToken !== '99';
$hasSubheader = !empty($subheaderToken) && $subheaderToken !== '99';
$hasVideo     = ($videoUrl !== '' && $videoUrl !== '99' && $isValidUrl($videoUrl));

$disclaimer = (string)($content['disclaimer'] ?? '');
$hasDisclaimer = trim($disclaimer) !== '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>

    <title><?php echo $e($tabTitle); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="css/example.css">
</head>

<body id="funnel" class="fullscreen"
      style="background: url('../images/background/<?php echo $e($backgroundToken); ?>.jpg') center center fixed; background-size: cover;">

<div class="container full-screen">
    <div class="row justify-content-center full-screen py-3">
        <div class="col-lg-6 col-md-8 col-sm-10 my-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-center">

                        <?php if ($hasHeader) { ?>
                            <div class="col-lg-11 text-center mt-2 mb-3">
                                <img src="../images/header/<?php echo $e($headerToken); ?>.png" class="img-fluid" alt="">
                            </div>
                        <?php } ?>

                        <?php if ($hasSubheader) { ?>
                            <div class="col-lg-11 text-center mb-3">
                                <img src="../images/subheader/<?php echo $e($subheaderToken); ?>.png" class="img-fluid" alt="">
                            </div>
                        <?php } ?>

                        <?php if ($hasVideo) { ?>
                            <div class="col-lg-11 mb-3">
                                <div id="videoDiv" class="embed-responsive embed-responsive-16by9">
                                    <iframe
                                            width="100%"
                                            height="400"
                                            id="videoSource"
                                            class="embed-responsive-item"
                                            src="<?php echo $e($videoUrl); ?>"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen
                                    ></iframe>
                                </div>
                            </div>
                        <?php } ?>

                    </div>

                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <form>
                                <div class="row">
                                    <?php
                                    // FIXED: comparisons (==) instead of assignments (=)
                                    if ((int)$form['formName'] == 1) {
                                        echo "
                                        <div class='col-12 input-group mb-2'>
                                            <div class='input-group-prepend input-group-lg'>
                                                <span class='input-group-text'><i class='fa fa-user'></i></span>
                                            </div>
                                            <input type='text' class='form-control form-control-lg' placeholder='Full Name'>
                                        </div>
                                        ";
                                    } elseif ((int)$form['formName'] == 2) {
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
                                    }
                                    ?>

                                    <div class='col-12 input-group mb-2'>
                                        <div class='input-group-prepend input-group-lg'>
                                            <span class='input-group-text'><i class='fa fa-envelope'></i></span>
                                        </div>
                                        <input type='text' class='form-control form-control-lg' placeholder='Email Address'>
                                    </div>

                                    <?php if (!empty($form['formPhone'])) { ?>
                                        <div class='col-12 input-group mb-2'>
                                            <div class='input-group-prepend input-group-lg'>
                                                <span class='input-group-text'><i class='fa fa-phone'></i></span>
                                            </div>
                                            <input type='text' class='form-control form-control-lg' placeholder='Phone Number'>
                                        </div>
                                    <?php } ?>

                                    <?php if (!empty($form['formHuman'])) { ?>
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
                            <img src="../images/button/<?php echo $e($buttonToken); ?>.png" class="img-fluid" alt="">
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <?php if ($hasDisclaimer) { ?>
                            <div class="col-12 text-center mb-2">
                                <p style="color: #ffffff;"><?php echo $e($disclaimer); ?></p>
                            </div>
                        <?php } ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js"
        integrity="sha512-u3fPA7V8qQmhBPNT5quvaXVa1mnnLSXUep5PS1qo5NRzHwG19aHmNJnj1Q8hpA/nBWZtZD4r4AX6YOt5ynLN2g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

</body>
</html>
