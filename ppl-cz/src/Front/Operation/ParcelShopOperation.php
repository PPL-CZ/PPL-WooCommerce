<?php

namespace PPLCZ\Front\Operation;

defined("WPINC") or die();


use PPLCZ\Model\Model\AdditionalDataModel;
use PPLCZ\Model\Model\CartDataModel;
use PPLCZ\Model\Model\ParcelDataModel;
use PPLCZ\Serializer;
use PPLCZ\Traits\ParcelDataModelTrait;

class ParcelShopOperation
{

    use ParcelDataModelTrait;

    public static function update_order(\WC_Order $order, \WP_REST_Request $request)
    {
        $cartdata = pplcz_get_cart_cartdata();
        if ($cartdata)
            self::setOrderCartData($order, $cartdata);
    }

    public static function old_posted_data($data)
    {
        if (!isset($_POST['pplcz_nonce']) || !wp_verify_nonce(sanitize_key($_POST['pplcz_nonce']), 'selectparcelshop'))
            return $data;

        $cartdata = pplcz_get_cart_cartdata();

        if (!$cartdata)
            $cartdata = new CartDataModel();

        if (isset($_POST["pplcz_parcelshop"])) {
            $content = wp_unslash(sanitize_post($_POST["pplcz_parcelshop"], 'raw'));
            $parcelshop = json_decode(urldecode($content), true);
            wc_clean($parcelshop);
            if ($parcelshop) {
                $cartdata->setParcelData(pplcz_denormalize($parcelshop, ParcelDataModel::class));
            } else {
                $cartdata->setParcelData(null);
            }
            $data["pplcz_parcelshop"] = urlencode(wp_json_encode($content));
        }

        if (isset($data['shipping_pplcz_posn']))
        {
            $additionalData = $cartdata->getAdditionalData();
            if (!$additionalData)
                $additionalData = new AdditionalDataModel();
            $additionalData->setPosn($data['shipping_pplcz_posn']);
            $cartdata->setAdditionalData($additionalData);
        }

        pplcz_set_cart_cartdata($cartdata);

        return $data;
    }

    public static function old_update_checkout($data)
    {
        parse_str($data, $arraydata);

        $cartData = pplcz_get_cart_cartdata();
        if (!$cartData)
            $cartData = new CartDataModel();

        if (isset($arraydata['pplcz_parcelshop'])) {
            $decoded = json_decode(urldecode($arraydata['pplcz_parcelshop']), true);

            wc_clean($decoded);
            if ($decoded) {
                $unserialized = pplcz_denormalize($decoded, ParcelDataModel::class);
                $cartData->setParcelData($unserialized);
            }
            else
            {
                $cartData->setParcelData(null);
            }
        }

        if (isset($arraydata['shipping_pplcz_posn']))
        {
            $additionalData = $cartData->getAdditionalData();
            if (!$additionalData)
                $additionalData = new AdditionalDataModel();
            $additionalData->setPosn($arraydata['shipping_pplcz_posn']);
            $cartData->setAdditionalData($additionalData);
        }

        pplcz_set_cart_cartdata($cartData);
    }

    public static function old_shipping_item(\WC_Order_Item_Shipping $item, $package_key, $package, $order)
    {
        $cartData = pplcz_get_cart_cartdata();

        if ($cartData && ($cartData->getParcelData() || $cartData->getAdditionalData())) {
            self::setOrderShippingCartDataModel($item, $cartData);
        }
    }

    public static function woocommerce_billing_fields($fields)
    {
        $fields['shipping_pplcz_posn'] = [
            'label' => 'Postident ID',
            'placeholder' => 'Zadejte Postident ID',
            'required' => false,
            'class' => ['form-row-first'],
            'priority' => 2000,
        ];

        return $fields;
    }

    public static function woocommerce_form_field($field, $key, $args)
    {
        if ($key === 'shipping_pplcz_posn')
            return preg_replace( '/<span class="optional">\(.*?\)<\/span>/', '', $field );
        return $field;
    }

    public static function woocommerce_checkout_get_value($value, $input)
    {
        if ($input === 'shipping_pplcz_posn')
        {
            $cartdata = pplcz_get_cart_cartdata();
            if ($cartdata && $cartdata->getAdditionalData())
              return $cartdata->getAdditionalData()->getPosn();
        }
        return $value;
    }

    public static function register()
    {
        add_action('woocommerce_store_api_checkout_update_order_from_request', [self::class, 'update_order'], 10, 2);
        add_action('woocommerce_checkout_create_order_shipping_item', [self::class, "old_shipping_item"], 10, 4);
        add_filter("woocommerce_checkout_posted_data", [self::class, "old_posted_data"]);
        add_action('woocommerce_checkout_update_order_review', [self::class, "old_update_checkout"]);
        add_action("woocommerce_billing_fields",[self::class, "woocommerce_billing_fields"]);
        add_action("woocommerce_form_field",[self::class, "woocommerce_form_field"], 10, 3);
        add_filter( 'woocommerce_checkout_get_value', [self::class, 'woocommerce_checkout_get_value'], 10, 2 );

    }
}