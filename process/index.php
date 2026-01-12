<?php
require_once '../vendor/autoload.php';
session_start();
include '../includes/settings.php';

$url = parse_url($_SERVER['REQUEST_URI']);
$uriParts = explode('/', $url['path']);
$customerId = (string) end($uriParts);
if (!$customerId) {
    header(sprintf('Location: https://%s/registration/', SYSTEM_URL), true, 404);
    exit;
}
$elements = new \EZCapture\StripeElements();

$pageTitle = "Subscription Completion";

$customer = $elements->stripe->retrieveCustomer($customerId);
$intentId = $_COOKIE[$customerId];
$setupIntent = $elements->stripe->retrieveSetupIntent($intentId);

parse_str($url['query'], $searchParams);
if (!empty($searchParams)) {
    if ($setupIntent->id !== $searchParams['setup_intent'] || $setupIntent->client_secret !== $searchParams['setup_intent_client_secret']) {
        header(sprintf('Location: https://%s/registration/', SYSTEM_URL), true, 404);
        exit;
    }
    switch ($setupIntent->status) {
        case 'succeeded':
            $subscription = $elements->stripe->createSubscription($customer->id, $setupIntent->metadata->membership);
            header(sprintf('Location: https://%s/registration/success.php?customerId=%s', SYSTEM_URL, urlencode($customer->id)), true, 302);
            exit;
        case 'requires_action':
        case 'requires_confirmation':
        case 'requires_payment_method':
        case 'processing':
            break;
        case 'canceled':
        default:
            $elements->stripe->cancelSetupIntent($setupIntent->id);
            header(sprintf('Location: https://%s/registration/failure.php?customerId=%s', SYSTEM_URL, urlencode($customer->id)), true, 302);
            exit;
    }
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
<body id="process_payment" class="full-screen">
<!-- Container starts-->
<div class="container full-screen">
    <div class="row justify-content-center full-screen">
        <div class="col-xl-6 col-lg-8 col-md-10 py-5 my-auto">
            <div class="card shadow">
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 mb-4">
                            <img src="../images/ez-capture-page-logo.png" class="img-fluid">
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12 mb-3 text-center">
                            <h3>Enter Payment Information</h3>
                            <hr>
                        </div>
                    </div>
                    <form id="payment-form" class="theme-form">
                        <div class="row">
                            <div class="col-12">
                                <div id="payment-element">
                                    <!--Stripe.js injects the Payment Element-->
                                </div>
                            </div>
                            <div class="col-12 d-grid mt-3">
                                <button id="submit" class="btn btn-lg btn-primary">
                                    <div class="spinner hidden" id="spinner"></div>
                                    <span id="button-text"><i class="fa fa-credit-card"></i> PAY NOW!</span>
                                </button>
                                <div id="payment-message" class="hidden"></div>
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
<script src="https://js.stripe.com/v3/"></script>
<script>
    let stripe, elements;
    document.addEventListener("DOMContentLoaded", function() {
        stripe = Stripe('<?php echo STRIPE_PUBLIC; ?>');
        elements = stripe.elements({clientSecret: '<?php echo $setupIntent->client_secret; ?>'});
        const paymentElement = elements.create('payment');
        paymentElement.mount('#payment-element');
        const paymentForm = document.getElementById('payment-form');
        paymentForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            const {error} = stripe.confirmSetup({
                elements,
                confirmParams: {
                    return_url: 'https://<?php echo SYSTEM_URL; ?>/process/<?php echo $customer->id; ?>',
                }
            });
            if (error) {
                const paymentMessage = document.getElementById('payment-message');
                paymentMessage.classList.remove('hidden');
                paymentMessage.textContent = error.message;
            } else {
                // I don't know what to do here
            }
        });
    });

</script>


</body>

</html>
