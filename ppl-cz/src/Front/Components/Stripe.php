<?php

namespace PPLCZ\Front\Components;

class Stripe
{


    public static function stripe_express_fields($params)
    {
        $params['checkout']['needs_payer_phone'] = true;
        return $params;
    }

    public static $isStripeShippingRequest = false;

    public static function  is_stripe_shipping_request()
    {
        self::$isStripeShippingRequest = true;
    }

    public static function register()
    {
        add_filter('wc_stripe_express_checkout_params',[self::class, 'stripe_express_fields']);
        add_filter('wc_stripe_payment_request_shipping_posted_values', [self::class, 'is_stripe_shipping_request']);
    }

}


