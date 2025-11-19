<?php
namespace PPLCZ\Front\Validator;

defined("WPINC") or die();


use PPLCZ\Model\Model\ProductModel;
use PPLCZ\Model\Model\CartModel;
use PPLCZ\Proxy\ApiCartProxy;
use PPLCZ\Serializer;
use PPLCZ\Traits\ParcelDataModelTrait;
use PPLCZ\Validator\Validator;

class ParcelShopValidator
{
    use ParcelDataModelTrait;


    public static function cart_api_validate($data, \WP_Error $errors, \WC_Cart $cart)  {

        $apicart = new ApiCartProxy($cart);
        if (isset($data['shipping_address']))
            $apicart->shipping_address = $data['shipping_address'];
        if (isset($data['billing_address']))
            $apicart->billing_address = $data['billing_address'];


        Validator::getInstance()->validate($apicart, $errors);

        return $errors;
    }

    public static function rest_request($response, $handler, \WP_REST_Request $request)
    {
        if (preg_match("~^/wc-analytics~", $request->get_route())) {
            return $response;
        }

        $validate2 = preg_match("~^/wc/store~", $request->get_route());
        $validate3 = preg_match("~/batch$~", $request->get_route());

        if ($validate3 && $validate2) {
            return $response;
        }

        if ($validate2 && preg_match("~/cart(/.+)?$~", $request->get_route())) {
            return $response;
        }

        $stripe = $request->get_json_params();

        if ($stripe && isset($stripe['payment_method']) && $stripe['payment_method'] === 'stripe')
            do_action('pplcz_payment_stripe');

        add_filter("woocommerce_store_api_cart_errors",function (\WP_Error $errors, \WC_Cart $cart) use ($request) {
            self::cart_api_validate($request->get_json_params(), $errors, $cart);
        }, 10, 2);

        return $response;
    }


    public static function cart_validate($data, ?\WP_Error $errors = null)
    {
        pplcz_validate(WC()->cart, $errors);

        return $data;
    }

    public static function register()
    {
        add_filter("rest_request_before_callbacks", [self::class, 'rest_request'], 10, 3);
        add_filter("woocommerce_after_checkout_validation", [self::class, "cart_validate"],10, 2);
    }
}