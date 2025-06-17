<?php

namespace PPLCZ\Front\Components\ParcelShop;

defined("WPINC") or die();

use PPLCZ\Model\Model\ParcelDataModel;
use PPLCZ\Model\Model\CartModel;
use PPLCZ\Serializer;
use PPLCZ\Traits\ParcelDataModelTrait;

class BlockOldView
{

    use ParcelDataModelTrait;

    private static $updateOrderReview = false;

    private static $beforeUpdateOrderShipment = null;

    private static $afterUpdateOrderShipment = null;

    public static function footer()
    {
        wp_enqueue_script("pplcz_map_js");
        wp_enqueue_style("pplcz_map_css");
        $script = plugins_url("ParcelShop/assets/select-parcelshop.js", realpath(__DIR__));
        wp_enqueue_script("pplcz_parcelshop-old-view", $script, [], pplcz_get_version(), true);
    }

    public static function woocommerce_checkout_update_order_review($postdata)
    {
        self::$beforeUpdateOrderShipment = pplcz_get_cart_shipping_method();

        wp_parse_str($postdata, $content);

        if (self::$beforeUpdateOrderShipment) {
            self::$beforeUpdateOrderShipment = self::$beforeUpdateOrderShipment->get_method_id();
            if (isset($content['shipping_method']) && is_array($content['shipping_method']))
                self::$afterUpdateOrderShipment = reset($content['shipping_method']);
        }
    }


    public static function woocommerce_after_calculate_totals($postdata) {

        $shippingMethod = pplcz_get_cart_shipping_method();
        if ($shippingMethod) {
            if ($shippingMethod->get_method_id() === self::$beforeUpdateOrderShipment
                || !self::$afterUpdateOrderShipment)
                return;
            self::$updateOrderReview = true;
        }
    }

    public static function after_shipping($inner = false)
    {

        $shippingMethod = pplcz_get_cart_shipping_method();
        if (!$shippingMethod) {
            return;
        }

        if ($shippingMethod->get_method_id() !== $inner->get_method_id())
            return;

        /**
         * @var CartModel $cartModel
         */
        $cartModel = Serializer::getInstance()->denormalize($shippingMethod->get_meta_data(), CartModel::class);

        printf("<input type='hidden' value='%s' name='pplcz_nonce' >",  esc_html(wp_create_nonce("selectparcelshop")));
        if ($cartModel->getParcelRequired() && !is_cart()) {
            $parcelshop = pplcz_get_cart_parceldata();

            $ageOk = !$cartModel->getAgeRequired();
            $ageOk = $ageOk || !$parcelshop || $parcelshop && $parcelshop->getAccessPointType() === "ParcelShop";

            wc_get_template("ppl/select-parcelshop-inner.php", [
                "shipping_address" => $parcelshop,
                "cod" => $cartModel->getCost() > 0 ? true: false,
                "parcelRequired" => $cartModel->getParcelRequired(),
                "ageRequired" => $cartModel->getAgeRequired(),
                "mapEnabled" => $cartModel->getParcelRequired(),
                "parcelShopEnabled" => $cartModel->getParcelShopEnabled(),
                "parcelBoxEnabled"=> $cartModel->getParcelBoxEnabled(),
                "alzaBoxEnabled" => $cartModel->getAlzaBoxEnabled(),
                "countries" => $cartModel->getEnabledParcelCountries(),
                "ageOk" => $ageOk,
                "content" => urlencode(wp_json_encode($parcelshop ? Serializer::getInstance()->normalize($parcelshop) : null)),
                "image" =>  pplcz_asset_icon("ps_pb.png"),
                "nonce" =>  wp_create_nonce("selectparcelshop"),
                "showMap" => is_ajax() && !$parcelshop && self::$updateOrderReview
            ]);
        }

    }

    public static function full_label($label, \WC_Shipping_Rate $method)
    {

        $id = $method->get_method_id();
        if (str_starts_with($id, pplcz_create_name(""))) {
            $metadata = $method->get_meta_data();
            $metadata = Serializer::getInstance()->denormalize($metadata, CartModel::class);
            /**
             * @var CartModel $metadata
             */
            $img = pplcz_asset_icon("small_logo.png");
            ob_start();

            // label is content from woocommerce with html, where i dont want change
            wc_get_template("ppl/method-name.php", [
                "img" => $img,
                "pplcz_label_safe" => $label,
                "free_shipping" => floatval($method->get_cost()) == 0
            ]);
            return ob_get_clean();

        }
        return $label;
    }

    public static function formatted_address_init($args)
    {
        add_filter('woocommerce_formatted_address_replacements', [self::class, "formatted_address"], 10, 2);
        return $args;
    }

    public static function formatted_address($args, $text)
    {
        remove_filter('woocommerce_formatted_address_replacements', [self::class, "formatted_address"]);

        /**
         * '{first_name}'       => $args['first_name'],
         * '{last_name}'        => $args['last_name'],
         * '{name}'             => sprintf(
         * '{company}'          => $args['company'],
         * '{address_1}'        => $args['address_1'],
         * '{address_2}'        => $args['address_2'],
         * '{city}'             => $args['city'],
         * '{state}'            => $full_state,
         * '{postcode}'         => $args['postcode'],
         * '{country}'          => $full_country,
         * '{first_name_upper}' => wc_strtoupper($args['first_name']),
         * '{last_name_upper}'  => wc_strtoupper($args['last_name']),
         * '{name_upper}'       => wc_strtoupper(

         * '{company_upper}'    => wc_strtoupper($args['company']),
         * '{address_1_upper}'  => wc_strtoupper($args['address_1']),
         * '{address_2_upper}'  => wc_strtoupper($args['address_2']),
         * '{city_upper}'       => wc_strtoupper($args['city']),
         * '{state_upper}'      => wc_strtoupper($full_state),
         * '{state_code}'       => wc_strtoupper($state),
         * '{postcode_upper}'   => wc_strtoupper($args['postcode']),
         * '{country_upper}'    => wc_strtoupper($full_country),
         */
        $shippingMethod =  pplcz_get_cart_shipping_method();
        if (!$shippingMethod)
            return $args;

        /**
         * @var CartModel $cartModel
         */
        $cartModel = pplcz_denormalize($shippingMethod->get_meta_data(), CartModel::class);
        /**
         * @var ParcelDataModel $parcelshop
         */
        $parcelshop = pplcz_get_cart_parceldata();

        if ($cartModel->getParcelRequired()) {
            foreach ($args as $key => $value)
            {
                $args[$key] = '';
            }
        }
        if ($parcelshop)
        {
            $args['{name}'] = "Výdejní místo";
            $args['{company}'] = $parcelshop->getName();
            $args['{address_1}'] = $parcelshop->getStreet();
            $args['{city}'] = $parcelshop->getCity();
            $args['{country}'] = $parcelshop->getCountry();
        }


        return $args;
    }

    public static function label_position()
    {
        wp_enqueue_style("ppl-label-position", plugins_url("ParcelShop/assets/label-method.css", realpath(__DIR__)), [], pplcz_get_version());
    }

    public static function register()
    {

        add_action("woocommerce_after_shipping_rate", [self::class, "after_shipping"]);

        if (!is_admin())
        {
            add_filter('woocommerce_order_formatted_shipping_address', [self::class, "formatted_address_init"], 10, 2);
        }

        add_filter("woocommerce_cart_shipping_method_full_label", [self::class, "full_label"], 10, 2);
        add_action("wp_head", [self::class, "label_position"]);
        add_action("wp_footer", [self::class, "footer"]);

        add_action("woocommerce_checkout_update_order_review", [self::class, "woocommerce_checkout_update_order_review"]);
        add_action("woocommerce_after_calculate_totals", [self::class, "woocommerce_after_calculate_totals"]);

    }
}



