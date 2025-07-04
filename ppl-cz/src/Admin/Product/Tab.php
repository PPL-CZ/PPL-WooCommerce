<?php

namespace PPLCZ\Admin\Product;

defined("WPINC") or die();

use PPLCZ\Admin\Assets\JsTemplate;
use PPLCZ\Model\Model\ProductModel;
use PPLCZ\Entity\ProductEntity;
use PPLCZ\Serializer;
use PPLCZ\Model\Model\ShipmentMethodModel;
use PPLCZ\ShipmentMethod;

class Tab {

    /**
     * @param array $tabs
     * @return array
     */
    public static function Tabs($tabs)
    {
        $tabs["pplcz_shipping_tab"] = [
            "label" => "PPL",
            "target" => "pplcz_tab_overlay",
            "class" => [ 'hide_if_virtual', 'hide_if_downloadable'  ]
        ];

        return $tabs;
    }

    public static function get_shipping()
    {
        $output = [];
        foreach (ShipmentMethod::methodsWithCod() as $k => $v) {
            $shipmentMethodModel = Serializer::getInstance()->denormalize([
                "code" => $k,
                "title" => $v,
                "codAvailable" => ShipmentMethod::isMethodWithCod($k),
                "parcelRequired" => ShipmentMethod::isMethodWithParcel($k)
            ], ShipmentMethodModel::class);
            $output[] = Serializer::getInstance()->normalize($shipmentMethodModel);
        }
        return $output;
    }

    public static function Render() {
        global $post_id;
        $model = Serializer::getInstance()->denormalize(new \WC_Product($post_id),  ProductModel::class);
        $model = Serializer::getInstance()->normalize($model, "array");
        $shipments = self::get_shipping();
        $nonce = wp_create_nonce("product");


        ?><div
                id="pplcz_tab_overlay"
                class="panel woocommerce_options_panel"
                data-data='<?php echo esc_html(wp_json_encode(pplcz_normalize($model) )); ?>'
                data-methods='<?php echo esc_html(wp_json_encode( $shipments )); ?>'
                data-pplNonce='<?php echo esc_html(wp_json_encode($nonce)); ?>'
        >
        </div>
        <?php
        JsTemplate::add_inline_script("pplczInitProductTab", 'pplcz_tab_overlay');
    }

    public static function Save($post_id) {

        if (!isset($_POST['pplNonce']) || !wp_verify_nonce(sanitize_key($_POST['pplNonce']), 'product'))
        {
            return;
        }

        $model = new ProductModel();
        $pplDisabledParcelBox = false;
        $pplDisabledParcelShop = false;
        $pplDisabledAlzaBox = false;

        $pplConfirmAge15 = false;
        $pplConfirmAge18 = false;
        $pplDisabledTransport = [];

        if (isset($_POST['pplConfirmAge15']))
            $pplConfirmAge15 = sanitize_post(wp_unslash($_POST['pplConfirmAge15']), 'raw');
        if (isset($_POST['pplConfirmAge18']))
            $pplConfirmAge18 = sanitize_post(wp_unslash($_POST['pplConfirmAge18']), 'raw');
        if (isset($_POST['pplDisabledTransport']))
            $pplDisabledTransport = map_deep(wp_unslash($_POST['pplDisabledTransport']), 'sanitize_text_field');

        if (isset($_POST['pplDisabledParcelBox']))
            $pplDisabledParcelBox = sanitize_post(wp_unslash($_POST['pplDisabledParcelBox']), 'raw');

        if (isset($_POST['pplDisabledParcelShop']))
            $pplDisabledParcelShop = sanitize_post(wp_unslash($_POST['pplDisabledParcelShop']), 'raw');

        if (isset($_POST['pplDisabledAlzaBox']))
            $pplDisabledAlzaBox = sanitize_post(wp_unslash($_POST['pplDisabledAlzaBox']), 'raw');


        $model->setPplConfirmAge15(!!$pplConfirmAge15);
        $model->setPplConfirmAge18(!!$pplConfirmAge18);

        if (is_array($pplDisabledTransport)) {
            $model->setPplDisabledTransport($pplDisabledTransport);
        }
        $model->setPplDisabledParcelBox(!!$pplDisabledParcelBox);
        $model->setPplDisabledParcelShop(!!$pplDisabledParcelShop);
        $model->setPplDisabledAlzaBox($pplDisabledAlzaBox);

        $product = new \WC_Product($post_id);
        pplcz_denormalize($model, \WC_Product::class, [ "product" => $product]);
        $product->save_meta_data();
    }

    public static function register()
    {
        add_filter("woocommerce_product_data_tabs", array(Tab::class, "Tabs"));
        add_action("woocommerce_product_data_panels", array(Tab::class, "Render"));
        add_action("woocommerce_process_product_meta", array(Tab::class, "Save"));

    }
}

