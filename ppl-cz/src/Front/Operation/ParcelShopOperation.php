<?php
namespace PPLCZ\Front\Operation;

defined("WPINC") or die();


use PPLCZ\Model\Model\ParcelDataModel;
use PPLCZ\Serializer;
use PPLCZ\Traits\ParcelDataModelTrait;

class ParcelShopOperation {

    use ParcelDataModelTrait;

    public static function update_order(\WC_Order $order, \WP_REST_Request $request) {
        $parcelshopData = pplcz_get_cart_parceldata();
        if ($parcelshopData)
            self::setParcelshopOrderData($order, $parcelshopData);
    }

    public static function old_posted_data($data)
    {
        if (!isset($_POST['pplcz_parcelshop']) ||
            !isset($_POST['pplcz_nonce']) || !wp_verify_nonce(sanitize_key($_POST['ppl_nonce']), 'selectparcelshop'))
            return $data;

        if (isset($_POST["pplcz_parcelshop"])) {
            $content = wp_unslash(sanitize_post($_POST["pplcz_parcelshop"], 'raw'));
            $parcelshop = json_decode(urldecode($content), true);
            wc_clean($parcelshop);
            if ($parcelshop) {
                $content = Serializer::getInstance()->denormalize($parcelshop, ParcelDataModel::class);
                pplcz_set_cart_parceldata($content);
            }
            else {
                pplcz_set_cart_parceldata(null);
            }
            $data["pplcz_parcelshop"] = urlencode(wp_json_encode($content));
        }
        return $data;
    }

    public static function old_update_checkout($data) {

        parse_str($data, $arraydata);
        if (isset($arraydata['pplcz_parcelshop'])) {
            $decoded = json_decode(urldecode($arraydata['pplcz_parcelshop']), true);
            wc_clean($decoded);
            if ($decoded) {
                $unserialized = Serializer::getInstance()->denormalize($decoded, ParcelDataModel::class);
                pplcz_set_cart_parceldata($unserialized);
            }
            else
                pplcz_set_cart_parceldata(null);
        }
    }

    public static function old_shipping_item(\WC_Order_Item_Shipping $item, $package_key, $package, $order)
    {
        $data = pplcz_get_cart_parceldata();
        if ($data) {
            self::setParcelDataModel($item, $data);
        }
    }

    public static function register()
    {
        add_action('woocommerce_store_api_checkout_update_order_from_request', [self::class, 'update_order'], 10, 2);
        add_action('woocommerce_checkout_create_order_shipping_item', [self::class, "old_shipping_item"], 10, 4);
        add_filter("woocommerce_checkout_posted_data", [self::class, "old_posted_data"]);
        add_action('woocommerce_checkout_update_order_review', [self::class, "old_update_checkout"]);

    }
}