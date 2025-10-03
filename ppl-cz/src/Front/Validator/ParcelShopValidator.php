<?php
namespace PPLCZ\Front\Validator;

defined("WPINC") or die();


use PPLCZ\Model\Model\ProductModel;
use PPLCZ\Model\Model\CartModel;
use PPLCZ\Serializer;
use PPLCZ\Traits\ParcelDataModelTrait;
use PPLCZ\Validator\Validator;

class ParcelShopValidator
{
    use ParcelDataModelTrait;


    public static function cart_api_validate(\WP_Error $errors, \WC_Cart $cart)  {
        Validator::getInstance()->validate($cart, $errors);

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

        add_filter("woocommerce_store_api_cart_errors",[static::class, "cart_api_validate"], 10, 2);

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