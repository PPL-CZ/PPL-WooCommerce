<?php
namespace PPLCZ\Admin\Order;
defined("WPINC") or die();


use PPLCZ\Admin\Assets\JsTemplate;
use PPLCZ\Model\Model\ParcelDataModel;
use PPLCZ\Model\Normalizer\ParcelDataModelNormalizer;
use PPLCZ\ShipmentMethod;
use PPLCZ\Serializer;
use PPLCZ\Traits\ParcelDataModelTrait;

class ParcelShop {

    use ParcelDataModelTrait;

    public static function itemmeta($item_id, $item, $any) {
        if ($item instanceof \WC_Order_Item_Shipping)
        {
            $meta = self::getParcelDataModel($item);
            $pplcz_meta_id_safe = (int)$item_id;
            wc_get_template("ppl/admin/parcelshop-shipping-address.php", [
                "shippingAddress" => self::getParcelDataModel($item),
                "meta_id" => $item_id,
                "hidden_data" => wp_json_encode($meta ? Serializer::getInstance()->normalize($meta) : null),
                "order_id" => $item->get_order_id(),
                "nonce"=>wp_create_nonce("parcelshop_edit_metadata"),
                "show" => str_contains($item->get_method_id(), pplcz_create_name(""))
            ]);

            JsTemplate::add_inline_script("pplczPPLParcelshop", "pplcz_parcelshop_$pplcz_meta_id_safe");
        }

        return;
    }

    public static function render_parcel_shop() {
        check_ajax_referer('parcelshop_edit_metadata', 'nonce');

        // Krok 3: Získání ID objednávky z požadavku
        $order_id = isset($_POST['order_id']) ? intval(sanitize_key($_POST['order_id'])) : 0;

        // Krok 4: Ověření uživatelských práv
        if (!current_user_can('edit_shop_orders')) {
            wp_send_json_error('Nemáte oprávnění upravovat objednávky.');
        }

        // Krok 5: Získání objednávky
        $order = wc_get_order($order_id);
        if (!$order) {
            wp_send_json_error('Objednávka nebyla nalezena.');
        }

        $meta_id = 0;
        $shipping_address = null;
        if (isset($_POST['meta_id']))
            $meta_id = (int)sanitize_key($_POST['meta_id']);
        if (isset($_POST['shipping_address']))
            $shipping_address = sanitize_post(wp_unslash($_POST['shipping_address']), 'raw');

        /**
         * @var \WC_Order_Item_Shipping $shipping
         */
        foreach ( $order->get_shipping_methods() as $shipping) {
            if ($shipping->get_id() === intval($meta_id)
                && (str_contains($shipping->get_method_id(), pplcz_create_name("")) || $shipping->get_method_id() === '')
            )
            {
                if (!$shipping_address) {
                    $data = null;
                } else {
                    $shipping_address = wc_clean($shipping_address);
                    $data = Serializer::getInstance()->denormalize($shipping_address, ParcelDataModel::class);
                }
                ob_start();
                wc_get_template("ppl/admin/parcelshop-shipping-address.php", [
                    "shippingAddress" => $data,
                    "meta_id" => $meta_id,
                    "hidden_data" => wp_json_encode($data ? Serializer::getInstance()->normalize($data) : null),
                    "order_id" => $order_id,
                    "nonce"=>wp_create_nonce("parcelshop_edit_metadata"),
                    "show"=> str_contains($shipping->get_method_id(), pplcz_create_name(""))
                ]);
                $content = ob_get_clean();
                wp_send_json_success([
                    "content" => $content
                ]);
                return;
            }
        }
        wp_send_json_error("Doprava nebyla nalezena.");
    }

    public static function update($item_id, $item, $args) {
        if ($item instanceof \WC_Order_Item_Shipping)
        {
            $order_id = $item->get_order_id();

            // https://github.com/PPL-CZ/PPL-WooCommerce/issues/7
            if (self::$ppl_parcelshopdata && isset(self::$ppl_parcelshopdata[$order_id]) && isset(self::$ppl_parcelshopdata[$order_id][$item_id]))
                $data = self::$ppl_parcelshopdata[$order_id][$item_id];
            else
                $data = null;
            if (str_contains($item->get_method_id(), pplcz_create_name(""))) {
                if ($data) {
                    try {
                        $contentData = json_decode($data, true);
                        if ($contentData) {
                            $data = Serializer::getInstance()->denormalize($contentData, ParcelDataModel::class);
                            self::setParcelDataModel($item, $data);
                        }
                    } catch (\Exception $e) {

                    }
                }
            } else {
                self::setParcelDataModel($item, null);
            }

        }
        return;
    }

    private static $ppl_parcelshopdata = [];

    public static function order_item_prepare($order_id, $items) {
        self::$ppl_parcelshopdata[$order_id] = @$items["pplcz_parcelshop"];
    }

    public static function register()
    {
        add_action("wp_ajax_pplcz_render_parcel_shop",[self::class, "render_parcel_shop" ]);
        add_action("woocommerce_before_order_itemmeta", [self::class, "itemmeta"], 10, 3);
        add_action("woocommerce_update_order_item", [self::class, "update" ], 10, 3);
        add_action('woocommerce_before_save_order_items', [self::class, "order_item_prepare"], 10, 2);
    }
}