<?php

use PPLCZ\Model\Model\ParcelDataModel;
use PPLCZ\Model\Model\CartModel;
use PPLCZ\Serializer;

defined("WPINC") or die();

function pplcz_create_name($name)
{
    return "pplcz_" . $name;
}

function pplcz_map_args()
{
    return PPLCZ\Front\Components\Map\Map::args();
}

function pplcz_asset_icon($name)
{

    return plugins_url("src/Admin/Assets/Images/$name", realpath(__DIR__));
}

function pplcz_set_shipment_print($shipmentId, $print)
{
    set_transient(pplcz_create_name("print_shipment_{$shipmentId}"), $print, time() + 60 * 60 * 48);
}

function pplcz_get_shipment_print($shipmentId)
{
    return get_transient(pplcz_create_name("print_shipment_{$shipmentId}"));
}

function pplcz_set_batch_print($batchId, $print)
{
    set_transient(pplcz_create_name("print_batch_{$batchId}"), $print, time() + 60 * 60 * 48);
}

function pplcz_get_batch_print($batchId)
{
    return get_transient(pplcz_create_name("print_batch_{$batchId}"));
}

function pplcz_get_download_pdf($download, $reference = null, $print = null)
{
    return \PPLCZ\Admin\Page\FilePage::createUrl($download, $reference, $print);
}

function pplcz_normalize($value)
{
    return Serializer::getInstance()->normalize($value);
}

function pplcz_denormalize($value, $type, $context = [])
{
    return Serializer::getInstance()->denormalize($value, $type, null, $context);
}

function pplcz_validate($model, $errors = null, $path = "")
{
    if (!$errors)
        $errors = new WP_Error();
    \PPLCZ\Validator\Validator::getInstance()->validate($model, $errors, $path);
    return $errors;
}

function pplcz_get_parcel_countries()
{
    $countries_obj = new \WC_Countries();

    $get_countries = $countries_obj->get_allowed_countries();
    $output = [];

    foreach ($get_countries as $key => $v) {
        if (!in_array($key, ['PL', "CZ", "SK", "DE"]))
            unset($get_countries[$key]);
    }

    return $get_countries;
}

function pplcz_get_allowed_countries()
{
    $countries_obj = new \WC_Countries();

    $get_countries = $countries_obj->get_allowed_countries();
    $output = [];

    $countries = include __DIR__ . '/config/countries.php';
    foreach ($get_countries as $key => $v) {
        if (!isset($countries[$key]))
            unset($get_countries[$key]);
    }

    return $get_countries;
}

function pplcz_get_cod_currencies()
{
    $currencies = include __DIR__ . '/config/cod_currencies.php';
    return $currencies;
}

function pplcz_set_phase_max_sync($value)
{
    add_option(pplcz_create_name("watch_phases_max_sync"), intval($value) ?: 200) || update_option(pplcz_create_name("watch_phases_max_sync"), intval($value) ?: 200);
}

function pplcz_get_phase_max_sync()
{
    $value = get_option(pplcz_create_name("watch_phases_max_sync"));
    return intval($value) ?: 200;
}

function pplcz_set_phase($key, $watch)
{
    if ($watch)
        add_option(pplcz_create_name("watch_phases_{$key}"), true) || update_option(pplcz_create_name("watch_phases_{$key}"), true);
    else
        delete_option(pplcz_create_name("watch_phases_{$key}"));
}

function pplcz_get_phases()
{
    $phases = include __DIR__ . '/config/shipment_phases.php';
    return array_map(function ($item, $key) {
        $output = [
            'code' => $key,
            'title' => $item,
            'watch' => !!get_option(pplcz_create_name("watch_phases_{$key}"))
        ];

        return $output;
    }, $phases, array_keys($phases));
}

function pplcz_get_version()
{
    if (!function_exists('get_plugin_data')) {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    $pluginData = get_plugin_data(__DIR__ . '/../ppl-cz.php');
    return $pluginData['Version'];
}

/**
 * @return ParcelDataModel|null
 */
function pplcz_get_cart_parceldata()
{
    $rate = pplcz_get_cart_shipping_method();
    if (!$rate)
        return null;

    /**
     * @var CartModel $metadata
     */
    $metadata = Serializer::getInstance()->denormalize($rate->get_meta_data(), CartModel::class);
    if ($metadata->getParcelRequired()) {
        $session = wc()->session;
        $parcelshop_data = $session->get(pplcz_create_name("parcelshop_data"));
        if ($parcelshop_data) {
            return Serializer::getInstance()->denormalize($parcelshop_data, ParcelDataModel::class);
        }
    }
    return null;
}

function pplcz_set_cart_parceldata(?ParcelDataModel $data)
{
    $session = wc()->session;
    if ($data)
        $session->set(pplcz_create_name("parcelshop_data"), Serializer::getInstance()->normalize($data));
    else
        $session->set(pplcz_create_name("parcelshop_data"), null);
}

/**
 * @return \WC_Shipping_Rate|null
 */
function pplcz_get_cart_shipping_method()
{
    $session = WC()->session;
    if (!$session)
        WC()->initialize_session();
    $session = WC()->session;
    $cart = WC()->cart;
    if (!$cart)
        WC()->initialize_cart();
    $cart = WC()->cart;

    $chosen_shipping_methods = $session->get('chosen_shipping_methods');
    if (!$chosen_shipping_methods)
        return null;


    $chosen_shipping_method = $chosen_shipping_methods[0];

    foreach ($chosen_shipping_methods as $key => $shipping_method) {
        $method = str_replace(pplcz_create_name(""), "", $chosen_shipping_method);
        $methods = \PPLCZ\ShipmentMethod::methods();
        $method = preg_replace("~:[0-9]+$~", "", $method);
        if (isset($methods[$method])) {
            $shipping = WC()->session->get("shipping_for_package_{$key}");
            /**
             * @var \WC_Shipping_Rate $rate
             */
            $rate = null;
            if ($shipping && isset($shipping['rates']) && isset($shipping['rates'][$chosen_shipping_method]))
                $rate = $shipping["rates"][$chosen_shipping_method];

            if ($rate)
                return $rate;
            // problem se zasilkovnou, proste to bez diskuzi vycisti
            $cart->calculate_shipping();
            $shipping = WC()->session->get("shipping_for_package_{$key}");
            if ($shipping && isset($shipping["rates"]) && isset($shipping["rates"][$chosen_shipping_method]))
                $rate = $shipping["rates"][$chosen_shipping_method];
            return $rate;
        }
    }
    return null;
}

function pplcz_get_update_setting()
{
    return get_option(pplcz_create_name("update_setting")) ?: "";
}

function pplcz_set_update_setting()
{
    add_option(pplcz_create_name("update_setting"), "" . time(), null, "yes") || update_option(pplcz_create_name("update_setting"), "" . time(), "yes");
}


function pplcz_currency($params)
{
    foreach ($params as $key => $value) {
        $params[$key]['pplcz_currency'] = get_woocommerce_currency();
        $params[$key]['pplcz_version'] = pplcz_get_version();
        $params[$key]['pplcz_update_setting'] = pplcz_get_update_setting();
    }
    return $params;
}

function pplcz_tables($activate = false)
{
    $version = get_option(pplcz_create_name("version"));
    if ($version !== pplcz_get_version()) {
        if (!$version) {
            foreach (pplcz_get_phases() as $phase) {
                pplcz_set_phase($phase['code'], true);
            }
        }

        require_once __DIR__ . '/installdb.php';
        pplcz_installdb();

        add_action("admin_init", function () use ($activate) {
            as_unschedule_action("woocommerceppl_refresh_shipments_cron");
            as_unschedule_action("woocommerceppl_refresh_setting_cron");

            as_unschedule_action(pplcz_create_name("refresh_shipments_cron"));
            as_unschedule_action(pplcz_create_name("refresh_setting_cron"));
            as_unschedule_action(pplcz_create_name("delete_logs"));

            as_unschedule_all_actions(pplcz_create_name("refresh_setting_cron"));
            as_unschedule_all_actions(pplcz_create_name("refresh_shipments_cron"));

            as_schedule_recurring_action(time(), 60 * 60 * 6, pplcz_create_name("refresh_shipments_cron"));
            as_schedule_recurring_action(time(), 60 * 60 * 24, pplcz_create_name("refresh_setting_cron"));
            as_schedule_recurring_action(time(), 60 * 60 * 24, pplcz_create_name("delete_logs"));

            if (!$activate)
                add_option(pplcz_create_name("version"), pplcz_get_version(), null, 'yes') || update_option(pplcz_create_name("version"), pplcz_get_version(), 'yes');

        });
    }


    $rules = get_option(pplcz_create_name("rules_version"));

    if ($rules !== pplcz_get_version() || $activate) {
        add_action("wp_loaded", function () use ($activate) {
            flush_rewrite_rules();
            if (!$activate) {
                add_option(pplcz_create_name("rules_version"), pplcz_get_version(), null, 'yes') || update_option(pplcz_create_name("rules_version"), pplcz_get_version(), 'yes');
            }
        });
    }

    if ($activate) {
        delete_option(pplcz_create_name("rules_version"));
        delete_option(pplcz_create_name("version"));
    }
}

function pplcz_woocommerce_loaded()
{
    if (!WC()->session)
        WC()->initialize_session();
}

function pplcz_simple_guid()
{
    $site_url = get_site_url();
    $timestamp = current_time('timestamp');

    $raw_string = $site_url . '-' . $timestamp;

    $hash = hash('sha256', $raw_string);
    $time = (new \DateTime())->format("YmdHis");
    $guid = sprintf('%s-%08s-%04s-%04s-%04s-%12s',
        substr($time, 2),
        substr($hash, 0, 8),
        substr($hash, 8, 4),
        substr($hash, 12, 4),
        substr($hash, 16, 4),
        substr($hash, 20, 12)
    );

    return $guid;
}


function pplcz_activate()
{
    pplcz_tables(true);
}

function pplcz_deactivate()
{
    as_unschedule_action(pplcz_create_name("refresh_shipments_cron"));
    as_unschedule_action(pplcz_create_name("refresh_setting_cron"));
    as_unschedule_action(pplcz_create_name("delete_logs"));
}


