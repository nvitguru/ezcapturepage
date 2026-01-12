<?php

namespace EZCapture;

class StripeElements
{
    public function __construct()
    {
        $this->stripe = new StripeSubscriptions();
    }

    public function generatePlanSelect()
    {
        $plans = $this->stripe->retrievePrices();
        $html = '<select class="form-select form-select-lg" id="membership" name="membership" required><option selected="" value="" data-text="- Select Membership -" data-cost="0.00">Choose Your Plan</option>';
        foreach ($plans as $plan) {
            $html .= sprintf("<option id=\"%s\" value=\"%s\" data-text=\"%s\" data-cost=\"%s\">%s - \$%s/mo</option>", $plan['nickname'], $plan['id'], $plan['product_data']['name'], number_format($plan['unit_amount'] / 100, 2), $plan['product_data']['name'], number_format($plan['unit_amount'] / 100, 2));
        }
        $html .= '</select>';
        return $html;
    }
}