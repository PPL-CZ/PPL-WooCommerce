<?php
namespace PPLCZ\ModelNormalizer;

use PPLCZ\Admin\CPLOperation;
use PPLCZ\Data\LogDataStore;
use PPLCZ\Data\ShipmentData;
use PPLCZ\Data\ShipmentDataStore;
use PPLCZ\Model\Model\CategoryModel;
use PPLCZ\Model\Model\ErrorLogCategorySettingModel;
use PPLCZ\Model\Model\ErrorLogItemModel;
use PPLCZ\Model\Model\ErrorLogModel;
use PPLCZ\Model\Model\ErrorLogProductSettingModel;
use PPLCZ\Model\Model\ErrorLogShipmentSettingModel;
use PPLCZ\Model\Model\ProductModel;
use PPLCZ\Model\Model\ShipmentMethodSettingModel;
use PPLCZ\Model\Model\ShipmentModel;
use PPLCZ\Setting\ApiSetting;
use PPLCZ\ShipmentMethod;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ErrorLogDenormalizer implements DenormalizerInterface
{


    private function getOrder($orderId)
    {
        $order = wc_get_order($orderId);
        if (!$order)
            return null;
        $export['id'] = $order->get_id();
        $export['status'] = $order->get_status();
        $export['currency'] = $order->get_currency();
        $export['payment_method'] = $order->get_payment_method();
        $export['payment_method_title'] = $order->get_payment_method_title();
        $export['shipping_total'] = $order->get_shipping_total();
        $export['discount_total'] = $order->get_discount_total();
        $export['cart_tax'] = $order->get_cart_tax();
        $export['shipping_tax'] = $order->get_shipping_tax();
        $export['total'] = $order->get_total();
        $export['date_created'] = $order->get_date_created() ? $order->get_date_created()->date('Y-m-d H:i:s') : null;
        $export['date_modified'] = $order->get_date_modified() ? $order->get_date_modified()->date('Y-m-d H:i:s') : null;
        $export['customer_ip'] = $order->get_customer_ip_address();
        $export['customer_user'] = $order->get_customer_id();

        // Fakturační adresa
        $export['billing'] = [
            'first_name' => $order->get_billing_first_name(),
            'last_name' => $order->get_billing_last_name(),
            'company' => $order->get_billing_company(),
            'email' => $order->get_billing_email(),
            'phone' => $order->get_billing_phone(),
            'address_1' => $order->get_billing_address_1(),
            'address_2' => $order->get_billing_address_2(),
            'city' => $order->get_billing_city(),
            'postcode' => $order->get_billing_postcode(),
            'state' => $order->get_billing_state(),
            'country' => $order->get_billing_country(),
        ];

        // Dodací adresa
        $export['shipping'] = [
            'first_name' => $order->get_shipping_first_name(),
            'last_name' => $order->get_shipping_last_name(),
            'company' => $order->get_shipping_company(),
            'address_1' => $order->get_shipping_address_1(),
            'address_2' => $order->get_shipping_address_2(),
            'city' => $order->get_shipping_city(),
            'postcode' => $order->get_shipping_postcode(),
            'state' => $order->get_shipping_state(),
            'country' => $order->get_shipping_country(),
        ];

        // Položky objednávky
        $export['items'] = [];
        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();

            $export['items'][] = [
                'item_id' => $item_id,
                'product_id' => $item->get_product_id(),
                'variation_id' => $item->get_variation_id(),
                'name' => $item->get_name(),
                'quantity' => $item->get_quantity(),
                'subtotal' => $item->get_subtotal(),
                'total' => $item->get_total(),
                'tax' => $item->get_total_tax(),
                'sku' => $product ? $product->get_sku() : null,
                'meta' => $item->get_formatted_meta_data('_', true),
            ];
        }

        // Doprava
        $export['shipping_lines'] = [];
        foreach ($order->get_shipping_methods() as $ship) {
            $export['shipping_lines'][] = [
                'method_title' => $ship->get_name(),
                'total' => $ship->get_total(),
                'tax' => $ship->get_total_tax(),
                'meta' => $ship->get_formatted_meta_data('_', true),
            ];
        }

        // Poplatky
        $export['fees'] = [];
        foreach ($order->get_fees() as $fee) {
            $export['fees'][] = [
                'name' => $fee->get_name(),
                'total' => $fee->get_total(),
                'tax' => $fee->get_total_tax(),
                'meta' => $fee->get_formatted_meta_data('_', true),
            ];
        }

        // Metadata (raw pole z wp_postmeta)
        $export['meta_data'] = [];
        foreach ($order->get_meta_data() as $meta) {
            $export['meta_data'][$meta->key] = $meta->value;
        }

        $shipments = null;
        try {
            $shipments = ShipmentData::read_order_shipments($order->get_id());
            foreach ($shipments as $k => $shipment) {
                $shipments[$k] = pplcz_normalize(pplcz_denormalize($shipment, ShipmentModel::class));
            }
        } catch (\Throwable $ex)
        {
            $shipments = "Error with getting existing ppl shipments\n"
                . $ex->getMessage() . "\n"
                . $ex->getFile() . "\n"
                . $ex->getLine();
        }

        $orderShipments = null;

        try {
            $orderShipments = pplcz_normalize(pplcz_denormalize($order, ShipmentModel::class));
        }
        catch (\Throwable $ex)
        {
            $orderShipments = "Error with creating ppl shipment from order \n"
                . $ex->getMessage() . "\n"
                . $ex->getFile() . "\n"
                . $ex->getLine();
        }

        $export['pplshipments'] = [
            "shipments" => $shipments,
            "orderShipments" => $orderShipments
        ];

        return $export;
    }
    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        /**
         * @var ErrorLogModel $data
         */

        $client_id = null;
        $client_secret = null;
        $accessToken = null;
        try {
            $apisetting = ApiSetting::getApi();
            $client_id = $apisetting->getClientId();
            $client_secret = $apisetting->getClientSecret();

            if ($client_id && $client_secret) {
                $accessToken = (new CPLOperation())->getAccessToken();
            }
        } catch (\Error $e) {

        }


        $active_plugins = get_option('active_plugins');

        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        $all_plugins = get_plugins();
        $active_plugins = get_option('active_plugins');

        foreach ($all_plugins as $key => $value) {
            if (!in_array($key, $active_plugins, true))
                unset($all_plugins[$key]);
        }

        $wordpress = get_bloginfo('version');
        $php = phpversion();

        $plugins = array_map(function ($plugin, $path) {
            return $plugin['Name'] . ' - ' . $plugin['Version'] . ' (' . explode('/', $path)[0] . ')';
        }, $all_plugins, array_keys($all_plugins));

        global $wpdb;

        $zones = array_merge(\WC_Shipping_Zones::get_zones(), [['zone_id' => 0]]);

        $others = $wpdb->get_results("SELECT * FROM wp_woocommerce_shipping_zone_methods WHERE zone_id = 0");
        $shipmentSettings = [];
        foreach ($zones as $zone) {
            if ($zone['zone_id'] === 0) {
                $zone_methods = array_filter(array_map(function ($item) {
                    if (strpos($item->method_id, 'pplcz_') === 0) {
                        return new ShipmentMethod($item->instance_id);
                    }
                    return null;
                }, $others));
                $zone_obj = null;
            } else {
                $zone_obj = new \WC_Shipping_Zone($zone['zone_id']);
                $zone_methods = $zone_obj->get_shipping_methods();
            }

            foreach ($zone_methods as $method) {
                if ($method instanceof ShipmentMethod) {
                    /**
                     * @var ShipmentMethodSettingModel $shipmentMethodSetting
                     */
                    $shipmentMethodSetting = pplcz_denormalize($method, ShipmentMethodSettingModel::class);

                    $sql = $wpdb->prepare("select * from {$wpdb->prefix}options where option_name = %s", 'woocommerce_' . ($method->instance_id ? ($method->id . '_' . $method->instance_id) : $method->id) . '_settings');
                    $row = $wpdb->get_row($sql);

                    $optionvalue = null;
                    if ($row)
                        $optionvalue = $row->option_value;

                    $shipmentSetting = new ErrorLogShipmentSettingModel();

                    $shipmentSetting->setShipmentSetting($shipmentMethodSetting);
                    $shipmentSetting->setRawBasicData($optionvalue);

                    $sql = $wpdb->prepare("select * from {$wpdb->prefix}options where option_name = %s", 'woocommerce_' . ($method->instance_id ? ($method->id . '_' . $method->instance_id) : $method->id) . '_settings_weight');
                    $row = $wpdb->get_row($sql);
                    if ($row) {
                        $shipmentSetting->setRawWeightData($row->option_value ?: null);
                    } else {
                        $shipmentSetting->setRawWeightData(null);
                    }
                    $shipmentSetting->setName(($zone_obj ? $zone_obj->get_zone_name() : "Ostatní") . ': ' . $method->get_method_title());
                    $shipmentSetting->setZones($zone_obj ? $zone_obj->get_formatted_location(1000) : "Ostatní");
                    $shipmentSettings[] = $shipmentSetting;
                }
            }
        }

        $data->setShipmentsSetting($shipmentSettings);

        $terms = get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
        ]);

        $output = [];

        foreach ($terms as $term) {
            /**
             * @var CategoryModel $cat
             */
            $cat = pplcz_denormalize($term, CategoryModel::class);
            if ($cat) {
                $setting = new ErrorLogCategorySettingModel();
                $setting->setName($term->name);
                $setting->setId($term->term_id);
                $setting->setSetting($cat);

                if ($term->parent)
                    $setting->setParent($term->parent);
                $output[] = $setting;
            }
        }

        $data->setCategorySetting($output);

        $data->setOrders([]);

        if (isset($context['order_ids']) && $context['order_ids']) {
            $orders = [];
            $product_ids = [];
            foreach ($context['order_ids'] as $orderId)
            {
                $order = $this->getOrder($orderId);
                if ($order) {
                    $product_ids = array_merge($product_ids, array_map(function ($item) {
                        return $item['product_id'];
                    }, $order['items']));
                    $orders[] = $order;
                }
            }

            if (!isset($context['product_ids']))
                $context['product_ids'] = [];

            $context['product_ids'] = array_unique(array_merge($context['product_ids'], $product_ids));

            $data->setOrders($orders);
        }

        $products = [];

        $get_product = function ($id)
        {
            $product = wc_get_product($id);
            if ($product) {
                $productSetting = new ErrorLogProductSettingModel();
                $productSetting->setId($product->get_id());
                $productSetting->setName($product->get_name());
                $productSetting->setParent($product->get_parent_id());
                $productSetting->setSetting(pplcz_denormalize($product, ProductModel::class));
                $product_terms = get_the_terms($product->get_id(), 'product_cat');
                if (is_array($product_terms)) {
                    $categories = wp_list_pluck($product_terms, 'term_id');
                    $productSetting->setCategoryIds($categories);
                }
                return $productSetting;
            }
            return null;
        };

        foreach ($context['product_ids'] as $product_id)
        {
            $products[$product_id] = call_user_func($get_product, $product_id);
        }

        $data->setProductsSetting(array_filter($products));

        if ($accessToken)
            $accessToken = "ano";
        else
            $accessToken = "ne";

        $summary = [
            "### Přístup",
            "Client ID: $client_id",
            "Získá accessToken: {$accessToken}",
            "***",
            "### Verze",
            "Wordpress: $wordpress",
            "PHP: $php",
            "***",
            "### Plugins",
            join("\n", $plugins)
        ];
        $data->setMail(get_option('admin_email'));
        $data->setInfo(join("\n", $summary));
        $items = [];

        $logs = LogDataStore::get_logs();
        foreach ($logs as $log) {
            $item = new ErrorLogItemModel();
            $item->setTrace($log->get_message());
            $item->setId($log->get_id());
            $items[] = $item;
        }

        $data->setErrors($items);

        return $data;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof ErrorLogModel && $type === ErrorLogModel::class;
    }
}