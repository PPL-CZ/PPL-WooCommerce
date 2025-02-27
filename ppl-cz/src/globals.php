<?php

use PPLCZ\Model\Model\ParcelDataModel;
use PPLCZ\Model\Model\CartModel;
use PPLCZ\Serializer;

defined("WPINC") or die();

function pplcz_create_name($name) {
    return "pplcz_" . $name;
}

function pplcz_map_args() {
    return PPLCZ\Front\Components\Map\Map::args();
}

function pplcz_asset_icon($name) {

    return plugins_url("src/Admin/Assets/Images/$name", realpath(__DIR__));
}

function pplcz_set_shipment_print($shipmentId, $print)
{
    set_transient(pplcz_create_name("print_shipment_{$shipmentId}"),  $print, time() + 60 * 60 * 48);
}

function pplcz_get_shipment_print($shipmentId)
{
    return get_transient(pplcz_create_name("print_shipment_{$shipmentId}"));
}

function pplcz_set_batch_print($batchId, $print)
{
    set_transient(pplcz_create_name("print_batch_{$batchId}"),  $print, time() + 60 * 60 * 48);
}

function pplcz_get_batch_print($batchId)
{
    return get_transient(pplcz_create_name("print_batch_{$batchId}"));
}

function pplcz_normalize($value)
{
    return Serializer::getInstance()->normalize($value);
}

function pplcz_denormalize($value, $type, $context = [])
{
    return Serializer::getInstance()->denormalize($value, $type, null, $context);
}

function pplcz_validate($model, $errors = null,  $path = "") {
    if (!$errors)
        $errors = new WP_Error();
    \PPLCZ\Validator\Validator::getInstance()->validate($model, $errors, $path);
    return $errors;
}

function pplcz_get_allowed_countries() {
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

function pplcz_get_cod_currencies() {
    $currencies = include __DIR__ . '/config/cod_currencies.php';
    return $currencies;
}

function pplcz_set_phase_max_sync($value)
{
    add_option(pplcz_create_name("watch_phases_max_sync"), intval($value) ?: 200) || update_option(pplcz_create_name("watch_phases_max_sync"), intval($value) ?: 200);
}

function pplcz_get_phase_max_sync() {
    $value = get_option(pplcz_create_name("watch_phases_max_sync"));
    return intval($value) ?: 200;
}

function pplcz_set_phase($key, $watch) {
    if ($watch)
        add_option(pplcz_create_name("watch_phases_{$key}"), true) || update_option(pplcz_create_name("_watch_phases_{$key}"), true);
    else
        delete_option(pplcz_create_name("watch_phases_{$key}"));
}

function pplcz_get_phases() {
    $phases = include  __DIR__ . '/config/shipment_phases.php';
    return array_map(function ($item, $key)  {
        $output = [
            'code' => $key,
            'title' => $item,
            'watch' =>  !!get_option(pplcz_create_name("watch_phases_{$key}"))
        ];

        return $output;
    }, $phases, array_keys($phases));

}

function pplcz_get_version() {
    if( !function_exists('get_plugin_data') ){
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }
    $pluginData = get_plugin_data( __DIR__ . '/../ppl-cz.php' );
    return $pluginData['Version'];
}

/**
 * @return ParcelDataModel|null
 */
function pplcz_get_cart_parceldata() {
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

    $chosen_shipping_methods = $session->get( 'chosen_shipping_methods' );
    if (!$chosen_shipping_methods)
        return null;


    $chosen_shipping_method  = $chosen_shipping_methods[0];

    foreach ($chosen_shipping_methods as $key => $shipping_method) {
        $method = str_replace(pplcz_create_name(""), "", $chosen_shipping_method);
        $methods = \PPLCZ\ShipmentMethod::methods();
        if (isset($methods[$method])) {
            $shipping = WC()->session->get("shipping_for_package_{$key}");
            /**
             * @var \WC_Shipping_Rate $rate
             */
            $rate = @$shipping["rates"][$chosen_shipping_method];
            if ($rate)
                return $rate;
            // problem se zasilkovnou, proste to bez diskuzi vycisti
            $cart->calculate_shipping();
            $shipping = WC()->session->get("shipping_for_package_{$key}");
            $rate = $shipping["rates"][$chosen_shipping_method];
            return $rate;
        }
    }
    return null;
}


function pplcz_currency($params)
{
    foreach ($params as $key => $value)
    {
        $params[$key]['pplcz_currency'] = get_woocommerce_currency();
        $params[$key]['pplcz_version'] = pplcz_get_version();
    }
    return $params;
}

function pplcz_tables ($activate = false) {
    $version = get_option(pplcz_create_name("version"));
    if ($version !== pplcz_get_version()) {

        require_once __DIR__ . '/installdb.php';
        pplcz_installdb();

        add_action("admin_init", function() use ($activate) {
            as_unschedule_action("woocommerceppl_refresh_shipments_cron");
            as_unschedule_action("woocommerceppl_refresh_setting_cron");
            as_unschedule_action(pplcz_create_name("refresh_shipments_cron"));
            as_unschedule_action(pplcz_create_name("refresh_setting_cron"));
            as_unschedule_all_actions(pplcz_create_name("refresh_setting_cron"));
            as_unschedule_all_actions(pplcz_create_name("refresh_shipments_cron"));

            as_schedule_recurring_action(time(), 60 * 60 * 6, pplcz_create_name("refresh_shipments_cron"));
            as_schedule_recurring_action(time(), 60 * 60 * 24, pplcz_create_name("refresh_setting_cron"));

            if (!$activate)
                add_option(pplcz_create_name("version"), pplcz_get_version()) || update_option(pplcz_create_name("version"), pplcz_get_version());

        });
    }


    $rules = get_option(pplcz_create_name("rules_version"));

    if ($rules !== pplcz_get_version() || $activate)
    {
        add_action("wp_loaded", function() use ($activate) {
            flush_rewrite_rules();
            if (!$activate) {
                add_option(pplcz_create_name("rules_version"), pplcz_get_version()) || update_option(pplcz_create_name("rules_version"), pplcz_get_version());
            }
        });
    }

    if ($activate)
    {
        delete_option(pplcz_create_name("rules_version"));
        delete_option(pplcz_create_name("version"));
    }
}

function pplcz_activate () {
    pplcz_tables(true);
}

function pplcz_deactivate()
{
    as_unschedule_action(pplcz_create_name("refresh_shipments_cron"));
    as_unschedule_action(pplcz_create_name("refresh_setting_cron"));
}

