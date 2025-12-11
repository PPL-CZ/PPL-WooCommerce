<?php
namespace PPLCZ\Admin\Order;
defined("WPINC") or die();

use PPLCZ\Admin\Assets\JsTemplate;
use PPLCZ\Data\ShipmentData;
use PPLCZ\Admin\Errors;

use PPLCZ\Model\Model\ShipmentModel;
use PPLCZ\Serializer;
use PPLCZ\Traits\ParcelDataModelTrait;
use PPLCZ\Validator\Validator;


class OrderTable {

    use ParcelDataModelTrait;

    public static function custom_columns($columns, $post_type = null)
    {
        return array_merge($columns, ["pplcz" => "PPL"]);
    }

    public static function create_shipment()
    {
        if (!current_user_can("manage_woocommerce") ||
            !isset($_POST['nonce']) || !wp_verify_nonce(sanitize_key($_POST['nonce']), "order")) {
            wp_die("", "", [
                "code" => 403
            ]);
        }

        $orderId  = 0;
        if (isset($_POST['orderId']))
            $orderId = (int)sanitize_key($_POST['orderId']);

        $order = new  \WC_Order($orderId);

        if (!$order->get_id())
        {
            wp_die("", "", [
                "response" => 404
            ]);
        }

        $shipments = ShipmentData::read_order_shipments($orderId);
        if (!$shipments) {
            $shipment = new ShipmentData();
            $shipment->set_note($order->get_customer_note());
            $shipment->set_wc_order_id($orderId);
            $shipment->save();
        }

        self::rerender();


    }

    public static function rerender_orders()
    {
        if (!current_user_can("manage_woocommerce") ||
            !isset($_POST['nonce']) || !wp_verify_nonce(sanitize_key($_POST['nonce']), "order")) {
            wp_die("", "", [
                "code" => 403
            ]);
        }

        $orderIds = [];
        if (isset($_POST['orderIds']))
            $orderIds = sanitize_post(wp_unslash($_POST["orderIds"]), 'raw');

        if (!is_array($orderIds))
            wp_die("", "", [
                "code" => 400
            ]);

        $output = [];
        foreach ($orderIds as $orderId) {
            $order = new  \WC_Order((int)sanitize_key($orderId));
            ob_start();
            self::render_column("pplcz", $order);
            $response = ob_get_clean();
            $output[$orderId] = $response;
        }

        wp_send_json_success([
            "orders" => $output
        ], 200);
    }

    public static function rerender()
    {
        if (!current_user_can("manage_woocommerce") ||
            !isset($_POST['nonce']) || !wp_verify_nonce(sanitize_key(@$_POST['nonce']), "order")) {
            wp_die("", "", [
                "code" => 403
            ]);
        }

        $orderId = 0;
        if (isset($_POST['orderId']))
            $orderId = (int)sanitize_key($_POST['orderId']);

        $order = new  \WC_Order($orderId);

        if (!$order->get_id())
        {
            wp_die("", "", [
                "response" => 404
            ]);
        }
        ob_start();
        self::render_column("pplcz", $order);
        $response = ob_get_clean();
        wp_send_json_success([
            "html" => $response
        ], 200);
    }

    private static $jsInitTab = false;


    public static function render_column($column_name, $order)
    {

        if($column_name === "pplcz") {
            if (is_string($order) || is_int($order))
                $order = new \WC_Order($order);

            $shipments = ShipmentData::read_order_shipments($order->get_id());

            if (!$shipments && !self::hasPPLShipment($order)) {
                return;
            }
            /**
             * @var ShipmentModel $shipment
             */
            foreach ($shipments as $key => $shipment) {
                $shipments[$key] = pplcz_denormalize($shipment, ShipmentModel::class);
            }

            if (!$shipments) {
                if(self::hasPPLShipment($order)) {
                    $shipments = [pplcz_denormalize($order, ShipmentModel::class)];
                }
            }

            $jsShipments = [];
            $jsShipmentsOk = [];
            foreach ($shipments as $key=>$shipment) {
                $jsShipments[$key] = Serializer::getInstance()->normalize($shipment, "array");
            }

            $anyError = false;

            foreach ($shipments as $key => $shipment) {
                $tests = new Errors();
                pplcz_validate($shipment, $tests, "");
                if ($tests->errors || $shipment->getImportState() === "Error")
                {
                    $anyError = true;
                    $jsShipmentsOk[$key] = false;
                }
                else {

                    $jsShipmentsOk[$key] = true;
                }
            }

            if (!self::$jsInitTab)
                JsTemplate::add_inline_script('pplczInitOrderTable');

            self::$jsInitTab = true;

            global $submenu;

            $uri = sanitize_url($_SERVER['REQUEST_URI']);

            if ($submenu && !$uri || (strpos($uri, 'shop_order') === false && strpos($uri, 'wc-orders') === false))
            {
                $uri = admin_url('edit.php?post_type=shop_order');
                if (isset($submenu['woocommerce']) && is_array($submenu['woocommerce']))
                {
                    if(array_filter($submenu['woocommerce'], function($item) {
                        return isset($item[2]) && $item[2] === 'wc-orders';
                    })) {
                        $uri = admin_url('admin.php?page=wc-orders');
                    }
                }
            }

            $unfinished = count($shipments) > 1 && !$anyError && array_filter($shipments, function($shipment) {
                return in_array($shipment->getImportState(), [ "None", null, ""], true) ;
            });

            if (isset($_REQUEST['acp_export_action']) && $_REQUEST['acp_export_action'] === 'acp_export_listscreen_export')
            {
                $output = [];
                foreach ($shipments as $shipment)
                {
                    $packages = $shipment->getPackages();
                    if ($packages) {
                        foreach ($packages as $package) {
                            $shipmentNumber = $package->getShipmentNumber();
                            if ($shipmentNumber)
                                $output[] = $shipmentNumber;
                        }
                    }
                }
                echo join(', ', $output);
                return;
            }

            wc_get_template("ppl/admin/order-table-column.php", [
                "order"=> $order,
                "unfinished"=> $unfinished,
                "shipments" => $shipments,
                "jsShipments" => $jsShipments,
                "jsShipmentsOk" => $jsShipmentsOk,
                "wcOrderUrl" => $uri,
            ]);
        }

    }



    public static function register()
    {
        add_action("manage_shop_order_posts_columns", [self::class, 'custom_columns'], 20, 1);
        add_filter("woocommerce_shop_order_list_table_columns" , [self::class, "custom_columns"], 10, 1 );

        add_action('manage_shop_order_posts_custom_column', [self::class, "render_column"], 10, 2);
        add_action('woocommerce_shop_order_list_table_custom_column', [self::class, "render_column"], 10, 2);

        add_action("wp_ajax_pplcz_order_table", [self::class, "rerender"]);
        add_action("wp_ajax_pplcz_orders_table", [self::class, "rerender_orders"]);
        add_action("wp_ajax_pplcz_order_table_create_shipment", [self::class, "create_shipment"]);

    }
}