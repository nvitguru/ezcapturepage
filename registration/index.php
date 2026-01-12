<?php
require_once '../vendor/autoload.php';
session_start();

$uriParts = explode('/', $_SERVER['REQUEST_URI']);
$referralID = (string) end($uriParts);
$referralCode = $referralID ? $referralID : "";

$pageTitle = "New Member Registration";

include '../includes/settings.php';

$elements = new EZCapture\StripeElements();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    try {
        // retrieve JSON from POST body
        $jsonStr = file_get_contents('php://input');
        $jsonObj = json_decode($jsonStr);
    } catch (Error $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    function validateCustomerData($data)
    {
        $errors = [];
        if (empty($data->fname)) {
            $errors['fname'] = 'Name is required';
        }
        if (empty($data->lname)) {
            $errors['lname'] = 'Name is required';
        }
        if (empty($data->email)) {
            $errors['email'] = 'Email is required';
        }
        if (empty($data->membership)) {
            $errors['membership'] = 'Membership is required';
        }
        return $errors;
    }

    $errors = validateCustomerData($jsonObj);
    if (count($errors) > 0) {
        http_response_code(400);
        echo json_encode(['errors' => $errors]);
        exit;
    }
    $name = $jsonObj->fname . ' ' . $jsonObj->lname;
    $customer = $elements->stripe->createCustomer($name, $jsonObj->email, ['referral_code' => $jsonObj->code, 'membership' => $jsonObj->membership]);
    $setupIntent = $elements->stripe->createSetupIntent($customer->id, ['membership' => $jsonObj->membership]);
    setcookie($customer->id, $setupIntent->id, time() + 3600, '/');
    echo json_encode(['url' => sprintf('/process/%s', $customer->id)]);
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?> | <?php echo SYSTEM_NAME ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100;200;300;400;500;600;700;800;900&amp;family=Nunito+Sans:ital,opsz,wght@0,6..12,200;0,6..12,300;0,6..12,400;0,6..12,500;0,6..12,600;0,6..12,700;0,6..12,800;0,6..12,900;0,6..12,1000;1,6..12,200;1,6..12,300;1,6..12,400;1,6..12,500;1,6..12,600;1,6..12,700;1,6..12,800;1,6..12,900;1,6..12,1000&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../member/css/vendors/themify.css">
    <link rel="stylesheet" type="text/css" href="../member/css/vendors/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../member/css/style.css">
    <link id="color" rel="stylesheet" href="../member/css/color-1.css" media="screen">
    <link rel="stylesheet" type="text/css" href="../member/css/responsive.css">
    <link rel="stylesheet" type="text/css" href="../member/css/custom.css">

    <link rel="icon" href="../images/ez-capture-page-icon.png" type="image/x-icon">
    <link rel="shortcut icon" href="../images/ez-capture-page-icon.png" type="image/x-icon">
</head>
<body id="registration" class="full-screen">
    <!-- Container starts-->
    <div class="container full-screen">
        <div class="row justify-content-center full-screen">
            <div class="col-xl-6 col-lg-8 col-md-10 py-5 my-auto">
                <div class="card shadow">
                    <div class="card-body">
                        <form id="subscription_form" class="theme-form" method="post" action="">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <p><a href="#" onclick="window.history.back(); return false;"><i class="fa fa-backward"></i> Back to Site</a></p>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-lg-8 mb-4">
                                    <img src="../images/ez-capture-page-logo.png" class="img-fluid">
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12 mb-3">
                                    <h3>Create Account</h3>
                                </div>
                                <?php include_once '../includes/alerts.php' ?>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="mb-3 col-lg-6">
                                            <label for="fname">First Name</label>
                                            <input class="form-control form-control-lg" id="fname" type="text" name="fname" required>
                                        </div>
                                        <div class="mb-3 col-lg-6">
                                            <label for="lname">Last Name</label>
                                            <input class="form-control form-control-lg" id="lname" type="text" name="lname" required>
                                        </div>
                                        <div class="mb-3 col-12">
                                            <label for="email">Email Address</label>
                                            <input class="form-control form-control-lg" id="email" type="text" name="email" required>
                                        </div>
                                        <div class="mb-3 col-12">
                                            <label for="membership">Membership Plan</label>
                                            <?php echo $elements->generatePlanSelect(); ?>
                                        </div>
                                        <div class="mb-3 col-lg-6">
                                            <label for="code">Referral Code</label>
                                            <input class="form-control form-control-lg" id="code" type="text" name="code" value="<?php echo $referralCode ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12"><hr></div>
                                <div class="col-12 mb-3">
                                    <div class="checkout-details">
                                        <div class="order-box">
                                            <div class="title-box">
                                                <div class="checkbox-title">
                                                    <h4>Product </h4><span>Total</span>
                                                </div>
                                            </div>
                                            <ul class="qty sub-total total">
                                                <li id="product">- Select Membership - <span id="cost">$0.00</span></li>
                                            </ul>
                                            <ul class="sub-total total">
                                                <li>Total <span id="total" class="count">$0.00</span></li>
                                                <li id="dueToday" class="d-none">Due Today <span class="count">$0.00</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" id="refund" type="checkbox" name="refund" required>
                                        <label class="form-check-label" for="refund">I have read & agree to the <?php echo SYSTEM_NAME ?> <a href="#refungModal" data-bs-toggle="modal">Refund Policy</a>.</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-lg-10 d-grid mb-2">
                                    <button type="submit" class="btn btn-lg btn-primary" name="" disabled="true"><span id="payBtn">CREATE ACCOUNT</span></button>
                                </div>
                                <div class="col-xl-6 col-lg-8 col-md-10">
                                    <img src="../images/stripe.png" class="img-fluid">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="../member/js/jquery.min.js"></script>
<script src="../member/js/popper.min.js"></script>
<script src="../member/js/bootstrap/bootstrap.bundle.min.js"></script>

    <script>
        let stripe, elements;
        document.addEventListener("DOMContentLoaded", function() {
            const membershipSelect = document.getElementById("membership");
            const productElement = document.getElementById("product");
            const costElement = document.getElementById("cost");
            const totalElement = document.getElementById("total");
            const dueTodayElement = document.getElementById("dueToday");
            const payBtnElement = document.getElementById("payBtn");
            const subscriptionForm = document.getElementById("subscription_form");

            membershipSelect.addEventListener("change", function() {
                let option = membershipSelect.options[membershipSelect.selectedIndex];
                productElement.childNodes[0].textContent = option.dataset.text + ' ';
                costElement.textContent = `$${option.dataset.cost}`;
                totalElement.textContent = `$${option.dataset.cost}`;
                if (option.value) {
                    dueTodayElement.classList.remove("d-none");
                    payBtnElement.textContent = "START 7 DAY FREE TRIAL";
                    payBtnElement.closest('button').disabled = false;
                } else {
                    dueTodayElement.classList.add("d-none");
                    payBtnElement.textContent = "CREATE ACCOUNT";
                    payBtnElement.closest('button').disabled = true;
                }
            });
            subscriptionForm.addEventListener('submit', function (event) {
                event.preventDefault();
                payBtnElement.closest('button').disabled = true;
                const formData = JSON.stringify(Object.fromEntries(new FormData(subscriptionForm).entries()));
                fetch('/registration/', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                }).then(response => response.json()).then(data => {
                    if (data.error) {
                        alert(data.error);
                        payBtnElement.closest('button').disabled = false;
                    } else {
                        window.location.href = data.url;
                    }
                });
            });
        });

    </script>


</body>

</html>