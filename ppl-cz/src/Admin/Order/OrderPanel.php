<?php
namespace PPLCZ\Admin\Order;
defined("WPINC") or die();


use PPLCZCPL\ApiException;
use PPLCZ\Admin\Assets\JsTemplate;
use PPLCZ\Admin\CPLOperation;
use PPLCZ\Data\AddressData;
use PPLCZ\Data\PackageData;
use PPLCZ\Data\ShipmentData;
use PPLCZ\Admin\Errors;
use PPLCZ\Model\Model\SenderAddressModel;
use PPLCZ\Model\Model\ShipmentModel;
use PPLCZ\Serializer;
use PPLCZ\Traits\ParcelDataModelTrait;
use PPLCZ\Validator\Validator;


class OrderPanel {

    use ParcelDataModelTrait;

    public static function renderFromWpPost(\WP_Post $post)
    {
        self::render(new \WC_Order($post->ID));
    }

    public static function render(\WC_Order $order) {

        $shipments = ShipmentData::read_order_shipments($order->get_id());
        foreach ($shipments as $key => $shipment) {
            $shipments[$key] = Serializer::getInstance()->denormalize($shipment, ShipmentModel::class);
        }

        if (!$shipments) {
            if (self::hasPPLShipment($order))
                $shipments = [Serializer::getInstance()->denormalize($order, ShipmentModel::class)];
        }

        $jsShipments = [];
        $jsShipmentsOk = [];
        $jsShipmentsErrors = [];
        $jsImage = [];

        foreach ($shipments as $key=>$shipment) {
            $jsShipments[$key] = Serializer::getInstance()->normalize($shipment, "array");
            /**
             * @var ShipmentModel $shipment
             */
            if ($shipment->getHasParcel()){
                if ($shipment->getParcel()) {
                    $parcel = $shipment->getParcel();
                    if ($parcel->getType() === "parcelshop") {
                        $jsImage[$key] = pplcz_asset_icon("parcelshop_2609x1033.png");
                    }
                    else {
                        $jsImage[$key] = pplcz_asset_icon("parcelbox_2625x929.png");
                    }
                }
            }
            if (!isset($jsImage[$key]))
            {
                $jsImage[$key] = pplcz_asset_icon("ppldhl_4084x598.png");
            }
        }

        foreach ($shipments as $key => $shipment) {
            $tests = new Errors();
            pplcz_validate($shipment, $tests, "");
            if ($tests->errors)
            {
                $jsShipmentsOk[$key] = false;
                $jsShipmentsErrors[$key] = $tests->errors;
            }
            else {
                $jsShipmentsOk[$key] = true;
                $jsShipmentsErrors[$key] =  [];
            }
        }

        JsTemplate::scripts();
        $availablePrinters =  (new CPLOperation())->getAvailableLabelPrinters();
        $format = (new CPLOperation())->getFormat(get_option(pplcz_create_name("print_setting"), ""));
        wc_get_template("ppl/admin/order-panel.php", [
            "availablePrinters" => $availablePrinters,
            "selectedPrint" => $format,
            "order"=> $order,
            "shipments" => $shipments,
            "jsShipments" => $jsShipments,
            "jsShipmentsOk" => $jsShipmentsOk,
            "jsShipmentsErrors" => $jsShipmentsErrors,
            "jsImage" => $jsImage,
            "nonce" => wp_create_nonce('orderpanel')
        ]);


        $pplcz_id_safe = (int) $order->get_id();

        JsTemplate::add_inline_script("pplczInitOrderPanel", "pplcz-order-panel-shipment-div-{$pplcz_id_safe}-overlay");
    }

    public static function prepare_package()
    {
        if (!current_user_can("manage_woocommerce")
            || !isset($_POST['nonce']) || !wp_verify_nonce(sanitize_key($_POST['nonce']), 'orderpanel'))
        {
            http_response_code(403);
            wp_die();
        }

        $shipmentId = 0;
        $orderId = 0;

        if(isset($_POST['shipmentId']))
            $shipmentId = (int)sanitize_key($_POST['shipmentId']);
        if(isset($_POST['orderId']))
            $orderId = (int)sanitize_key($_POST['orderId']);

        $order = new \WC_Order($orderId);

        if (!$shipmentId) {
            $shipmentModel = Serializer::getInstance()->denormalize($order, ShipmentModel::class);
            $shipmentData = Serializer::getInstance()->denormalize($shipmentModel, ShipmentData::class);
            $shipmentData->save();
        } else {
            $shipmentData = new ShipmentData($shipmentId);
        }

        $shipmentModel = Serializer::getInstance()->denormalize($shipmentData, ShipmentModel::class);

        ob_start();
        self::render($order);
        $html = ob_get_clean();
        $shipment = Serializer::getInstance()->normalize($shipmentModel);
        wp_send_json_success(["html" => $html, "shipment" => $shipment]);

        self::rerender();
    }

    public static function add_package()
    {
        if (!current_user_can("manage_woocommerce")
            || !isset($_POST['nonce']) || !wp_verify_nonce(sanitize_key($_POST['nonce']), 'orderpanel')) {
            http_response_code(403);
            wp_die();
        }

        $shipmentId = 0;
        $orderId = 0;
        if(isset($_POST['shipmentId']))
            $shipmentId = (int)sanitize_key($_POST['shipmentId']);
        if(isset($_POST['orderId']))
            $orderId = (int)sanitize_key($_POST['orderId']);

        $order = new \WC_Order($orderId);
        if (!$shipmentId) {

            $shipmentModel = Serializer::getInstance()->denormalize($order, ShipmentModel::class);
            $shipmentData = Serializer::getInstance()->denormalize($shipmentModel, ShipmentData::class);
            $shipmentData->save();
        } else {
            $shipmentData = new ShipmentData($shipmentId);
        }

        if (!$shipmentData->get_import_state() || $shipmentData->get_import_state() === "None") {
            $packageData = new PackageData();
            $packageData->set_phase("None");
            $packageData->set_reference_id("{$order->get_id()}");
            $packageData->save();
            $packageIds = $shipmentData->get_package_ids();
            $packageIds[] = $packageData->get_id();
            $shipmentData->set_package_ids($packageIds);
            $shipmentData->save();
        }
        self::rerender();
    }

    public static function remove_shipment()
    {
        if (!current_user_can("manage_woocommerce")
            || !isset($_POST['nonce']) || !wp_verify_nonce(sanitize_key($_POST['nonce']), 'orderpanel')) {
            http_response_code(403);
            wp_die();
        }

        $shipmentId = 0;
        if(isset($_POST['shipmentId']))
            $shipmentId = (int)sanitize_key($_POST['shipmentId']);


        if (!$shipmentId) {
            self::rerender();
        } else {
            $shipmentData = new ShipmentData($shipmentId);
        }
        if (!$shipmentData->get_import_state()
            || $shipmentData->get_import_state() === "None"
            || strpos(strtolower($shipmentData->get_import_state()), "error") !== false)
        {
            $packages = $shipmentData->get_package_ids();
            foreach ($packages as $package) {
                $package = new PackageData($package);
                $package->ignore_lock();
                $package->delete();
            }
            $sender = $shipmentData->get_recipient_address_id();
            if ($sender)
            {
                $sender = new AddressData($sender);
                if (!$sender->get_lock())
                    $sender->delete(true);
            }
            $shipmentData->delete(true);
        }
        self::rerender();
    }


    public static function remove_package()
    {
        if (!current_user_can("manage_woocommerce")
            || !isset($_POST['nonce']) || !wp_verify_nonce(sanitize_key($_POST['nonce']), 'orderpanel')) {
            http_response_code(403);
            wp_die();
        }

        $shipmentId = 0;
        if(isset($_POST['shipmentId']))
            $shipmentId = (int)sanitize_key($_POST['shipmentId']);

        if (!$shipmentId) {
            self::rerender();
        } else {
            $shipmentData = new ShipmentData($shipmentId);
        }
        if (!$shipmentData->get_import_state() || $shipmentData->get_import_state() === "None") {
            $ids = $shipmentData->get_package_ids();
            $c = array_pop($ids);
            $packageData = new PackageData($c);
            $packageData->delete();
            $shipmentData->set_package_ids($ids);
            $shipmentData->save();
            self::rerender();
        } else {
            self::rerender();
        }
    }

    public static function rerender()
    {
        if (!current_user_can("manage_woocommerce")
            || !isset($_POST['nonce']) || !wp_verify_nonce(sanitize_key($_POST['nonce']), 'orderpanel')) {
            http_response_code(403);
            wp_die();
        }

        $orderId = 0;
        if(isset($_POST['orderId']))
            $orderId = (int)sanitize_key($_POST['orderId']);

        $wc = new \WC_Order($orderId);
        ob_start();
        self::render($wc);
        $html = ob_get_clean();

        wp_send_json_success(["html" => $html]);
    }

    public static function test_labels()
    {
        if (!current_user_can("manage_woocommerce")
            || !isset($_POST['nonce']) || !wp_verify_nonce(sanitize_key($_POST['nonce']), 'orderpanel'))
        {
            http_response_code(403);
            wp_die();
        }

        $shipmentId = 0;
        if(isset($_POST['shipmentId']))
            $shipmentId = (int)sanitize_key($_POST['shipmentId']);

        $shipment = new ShipmentData($shipmentId);

        if (!$shipment->get_id()) {
            wp_send_json_error("Neexistující balík");
        }

        $cpl = new CPLOperation();

        $fullPackages = [];
        $batchsId = [];
        if ($shipment->get_batch_id()
            && !$shipment->get_import_errors()) {

            $packageIds = $shipment->get_package_ids();

            $packages = array_map(function ($item) {
                return new PackageData($item);
            }, $packageIds);

            foreach ($packages as $package) {
                if ($package->get_shipment_number()) {
                    $fullPackages[] = $package;
                }

                if (!$package->get_label_id())
                {
                    $batchsId[] = $shipment->get_batch_id();
                }
            }
        }
        else {
            $batchsId[] = $shipment->get_batch_id();
        }

        if ($batchsId)
        {
            $batchsId = array_unique(array_filter($batchsId));
            if ($batchsId) {
                $cpl->loadingShipmentNumbers($batchsId);
            }
        }

        $packageShipmentNumbers = array_map(function (PackageData $item) {
            return $item->get_id();
        }, $fullPackages);

        $cpl->testPackageStates($packageShipmentNumbers);


        self::rerender();
    }

    public static function create_labels()
    {
        if (!current_user_can("manage_woocommerce")
            || !isset($_POST['nonce']) || !wp_verify_nonce(sanitize_key($_POST['nonce']), 'orderpanel')) {
            http_response_code(403);
            wp_die();
        }

        $shipmentId = 0;
        $orderId = 0;
        if(isset($_POST['shipmentId']))
            $shipmentId = (int)sanitize_key($_POST['shipmentId']);
        if(isset($_POST['orderId']))
            $orderId = (int)sanitize_key($_POST['orderId']);

        if (!$shipmentId) {

            $wc = new \WC_Order($orderId);
            $newShipment = Serializer::getInstance()->denormalize($wc, ShipmentModel::class);
            $errors = new Errors();
            pplcz_validate($newShipment, $errors, "");
            if (!$errors->errors) {
                $newShipment = Serializer::getInstance()->denormalize($newShipment, ShipmentData::class);
                $newShipment->save();
            } else {
                wp_send_json_error("Problém s vytvořením zásilky");
                return;
            }
        } else {
            $newShipment = new ShipmentData($shipmentId);
            $testShipment = Serializer::getInstance()->denormalize($newShipment, ShipmentModel::class);
            $errors = new Errors();
            pplcz_validate($testShipment, $errors, "");
            if ($errors->errors)
            {
                wp_send_json_error("Problém s vytvořením zásilky");
                return;
            }
        }

        if ($newShipment) {
            try {
                $cpl = new CPLOperation();
                $cpl->createPackages([$newShipment->get_id()]);
            }
            catch (\Exception $ex) {
                $wc = new \WC_Order($orderId);
                ob_start();
                self::render($wc);
                $html = ob_get_clean();

                $newShipment = new ShipmentData($newShipment->get_id());
                $errors = [];
                if ($ex instanceof  ApiException) {
                    if ($ex->getCode() === 400) {
                        foreach (explode("\n", $newShipment->get_import_errors()) as $err) {
                            $err = preg_replace("/^.+?:/", "", $err);
                            $errors[] = trim($err);
                        }
                    }
                    else {
                        $errors[] = $ex->getMessage();
                    }
                }

                wp_send_json_error(["message" =>$errors ? join("\n", $errors) :  $ex->getMessage(), "html" =>$html] );
                return;
            }
        }
        self::rerender();
    }

    public static function change_print()
    {
        if (!current_user_can("manage_woocommerce")
            || !isset($_POST['nonce']) || !wp_verify_nonce(sanitize_key($_POST['nonce']), 'orderpanel')) {
            http_response_code(403);
            wp_die();
        }

        $print = null;
        $shipmentId = null;
        if (isset($_POST['print']))
            $print = sanitize_text_field(wp_unslash($_POST['print']));
        if (isset($_POST['shipmentId']))
            $shipmentId = sanitize_text_field(wp_unslash($_POST['shipmentId']));

        $labelPrinters = (new CPLOperation())->getAvailableLabelPrinters();
        foreach ($labelPrinters as $printer)
        {
            if ($printer->getCode() === $print)
            {
                if ($shipmentId)
                    pplcz_set_shipment_print($shipmentId, $print);
                else
                    add_option(pplcz_create_name("print_setting"), $print) || update_option(pplcz_create_name("print_setting"), $print);
                self::rerender();
            }
        }
        self::rerender();
    }

    public static function cancel_package() {
        if (!current_user_can("manage_woocommerce")
            || !isset($_POST['nonce']) || !wp_verify_nonce(sanitize_key($_POST['nonce']), 'orderpanel')) {
            http_response_code(403);
            wp_die();
        }

        $shipmentId = 0;
        $orderId = 0;
        $packageId = 0;


        if (isset($_POST['shipmentId']))
            $shipmentId = (int)sanitize_key($_POST['shipmentId']);
        if (isset($_POST['orderId']))
            $orderId = (int)sanitize_key($_POST['orderId']);
        if (isset($_POST['packageId']))
            $packageId = (int)sanitize_key($_POST['packageId']);

        try {
            (new CPLOperation())->cancelPackage($packageId);
            self::rerender();
        }
        catch (\Exception $ex)
        {
            wp_send_json_error("Zásilku nelze deaktivovat");
        }

    }


    public static function box($screen, $order)
    {
        if (in_array($screen, ["woocommerce_page_wc-orders",  "shop_order"], true)) {
            if ($order instanceof \WP_Post)
                add_meta_box("pplbox", "PPL", [self::class, "renderFromWpPost"], $screen, "side", "high");
            else if ($order instanceof \WC_Order)
                add_meta_box("pplbox", "PPL", [self::class, "render"], $screen, "side", "high");
        }
    }

    public static function register() {
        add_action('add_meta_boxes', [self::class, "box"], 10, 2);
        add_action("wp_ajax_pplcz_order_panel", [self::class, "rerender"]);
        add_action("wp_ajax_pplcz_change_print", [self::class, "change_print"]);
        add_action("wp_ajax_pplcz_order_panel_create_labels", [self::class, "create_labels"]);
        add_action("wp_ajax_pplcz_order_panel_test_labels", [self::class, "test_labels"]);
        add_action("wp_ajax_pplcz_order_panel_add_package", [self::class, "add_package"]);
        add_action("wp_ajax_pplcz_order_panel_remove_package", [self::class, "remove_package"]);
        add_action("wp_ajax_pplcz_order_panel_cancel_package", [self::class, "cancel_package"]);
        add_action("wp_ajax_pplcz_order_panel_prepare_package", [self::class, "prepare_package"]);
        add_action("wp_ajax_pplcz_order_panel_remove_shipment", [self::class, "remove_shipment"]);
    }
}