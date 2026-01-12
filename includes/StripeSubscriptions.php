<?php

namespace EZCapture;

use Stripe\StripeClient;

class StripeSubscriptions
{
    private $prices = [
        'startup' => [
            'currency' => 'USD',
            'nickname' => 'startup',
            'product_data' => [
                'name' => 'Start-Up Plan',
            ],
            'recurring' => [
                'interval' => 'month',
            ],
            'unit_amount' => 999,
        ],
        'standard' => [
            'currency' => 'USD',
            'nickname' => 'standard',
            'product_data' => [
                'name' => 'Standard Plan',
            ],
            'recurring' => [
                'interval' => 'month',
            ],
            'unit_amount' => 1499,
        ],
        'pro' => [
            'currency' => 'USD',
            'nickname' => 'pro',
            'product_data' => [
                'name' => 'Pro Plan',
            ],
            'recurring' => [
                'interval' => 'month',
            ],
            'unit_amount' => 1999,
        ],
    ];

    public function __construct()
    {
        $this->stripe = new StripeClient(STRIPE_PRIVATE);
    }

    public function setup()
    {
        $prices = $this->stripe->prices->all();
        if (count($prices->data) === 0) {
            foreach ($this->prices as $price) {
                echo '<pre>';
                echo $this->stripe->prices->create($price)->toJSON();
                echo '</pre>';
            }
        } else {
            foreach ($prices->data as $price) {
                echo '<pre>';
                echo $price->toJSON();
                echo '</pre>';
            }
        }
    }

    public function createCustomer($name, $email, $metadata = [])
    {
        $customers = $this->stripe->customers->search(['query' => sprintf('email:\'%s\'', $email)]);
        if (count($customers->data) > 0) {
            $customer = $this->stripe->customers->update($customers->data[0]->id, [
                'name' => $name,
                'metadata' => $metadata,
            ]);
            return $customer;
        }
        return $this->stripe->customers->create([
            'name' => $name,
            'email' => $email,
            'metadata' => $metadata,
        ]);
    }

    public function createSubscription($customer_id, $price_id)
    {
        $subscription = $this->stripe->subscriptions->create([
            'customer' => $customer_id,
            'items' => [
                ['price' => $price_id],
            ],
            'trial_period_days' => 7,
        ]);

        return $subscription;
    }

    public function cancelSubscription($subscription_id)
    {
        $subscription = $this->stripe->subscriptions->cancel($subscription_id);

        return $subscription;
    }

    public function retrieveSubscription($subscription_id)
    {
        $subscription = $this->stripe->subscriptions->retrieve($subscription_id);

        return $subscription;
    }

    public function pauseSubscription($subscription_id)
    {
        $subscription = $this->retrieveSubscription($subscription_id);
        if ($subscription->pause_collection !== null) {
            return $subscription;
        }
        $subscription = $this->stripe->subscriptions->update($subscription_id, [
            'pause_collection' => ['behavior' => 'void'],
        ]);

        return $subscription;
    }

    public function resumeSubscription($subscription_id)
    {
        $subscription = $this->retrieveSubscription($subscription_id);
        if ($subscription->pause_collection === null) {
            return $subscription;
        }
        $subscription = $this->stripe->subscriptions->update($subscription_id, [
            'pause_collection' => null,
        ]);

        return $subscription;
    }

    public function retrieveCustomerSubscriptions($customer_id)
    {
        $subscriptions = $this->stripe->subscriptions->all([
            'customer' => $customer_id,
        ]);

        return $subscriptions;
    }

    public function retrievePrice($price_id)
    {
        $price = $this->stripe->prices->retrieve($price_id);

        return $price;
    }

    public function retrievePriceByNickname($nickname)
    {
        $prices = $this->stripe->prices->all();
        foreach ($prices->data as $price) {
            if ($price->nickname === $nickname) {
                return $price;
            }
        }

        return null;
    }
    public function retrieveCustomer($customer_id)
    {
        $customer = $this->stripe->customers->retrieve($customer_id);

        return $customer;
    }

    public function retrievePaymentIntent($payment_intent_id)
    {
        $payment_intent = $this->stripe->paymentIntents->retrieve($payment_intent_id);

        return $payment_intent;
    }

    public function retrieveSetupIntent($setup_intent_id)
    {
        $setup_intent = $this->stripe->setupIntents->retrieve($setup_intent_id);

        return $setup_intent;
    }

    public function retrievePrices($filters = ['active' => true])
    {
        $prices = $this->stripe->prices->all($filters);
        $result = [];
        foreach ($this->prices as $nickname => $price) {
            foreach ($prices->data as $value) {
                if ($value->nickname === $nickname) {
                    $price['id'] = $value->id;
                }
            }
            $result[] = $price;
        }

        return $result;
    }

    public function createSetupIntent($id, $metadata = [])
    {
        $setupIntent = $this->stripe->setupIntents->create([
            'customer' => $id,
            'usage' => 'off_session',
            'metadata' => $metadata,
        ]);

        return $setupIntent;
    }

    public function cancelSetupIntent($id)
    {
        $setupIntent = $this->stripe->setupIntents->cancel($id);

        return $setupIntent;
    }
}