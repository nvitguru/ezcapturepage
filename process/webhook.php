<?php
require_once '../vendor/autoload.php';
require_once '../includes/settings.php';
$subscriptions = new \EZCapture\StripeSubscriptions();
$event = null;
$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$webhook_secret = STRIPE_WEBHOOK_SECRET;
try {
    $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, $webhook_secret
    );
} catch(\UnexpectedValueException $e) {
    // Invalid payload
    http_response_code(400);
    exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
    // Invalid signature
    http_response_code(400);
    exit();
}
// https://docs.stripe.com/billing/subscriptions/webhooks
switch ($event->type) {
    case 'invoice.paid':
        $invoice = $event->data->object;
        break;
    default:
        break;
}
http_response_code(200);
