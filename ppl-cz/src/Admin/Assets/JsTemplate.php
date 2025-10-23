<?php
namespace  PPLCZ\Admin\Assets;

use PPLCZ\Admin\Page\FilePage;

defined("WPINC") or die();

class JsTemplate
{
    public const COLLECTIONURL  = "https://www1.ppl.cz";

    public static $isAdded = false;



    public static $pplcz_scripts = [];

    public static $counter = 1;

    public static function scripts()
    {
        if (!self::$isAdded)
            add_action("admin_footer", [JsTemplate::class, "admin_scripts"]);
        self::$isAdded = true;
    }

    public static function add_inline_script($functionName, $element = null)
    {
        if (!self::$isAdded) {
            add_action("admin_footer", [self::class, 'admin_scripts']);
        }

        self::$isAdded = true;

        add_action("admin_footer", function() use ($functionName, $element) {
            if ($element)
                wp_add_inline_script("pplcz_plugin", sprintf("window.PPLczPlugin.push([%s, %s])", wp_json_encode(esc_html($functionName)), wp_json_encode(esc_html($element))));
            else
                wp_add_inline_script("pplcz_plugin", sprintf("window.PPLczPlugin.push([%s])", wp_json_encode(esc_html($functionName))));
        });
    }

    public static function admin_scripts()
    {
        $setting = require __DIR__ . '/build/index.asset.php';
        wp_enqueue_script("pplcz_map_js");
        wp_enqueue_style("pplcz_map_css");
        wp_enqueue_script("pplcz_scripts", plugin_dir_url(__FILE__) . 'build/index.js', array_merge($setting['dependencies'], ["jquery"]), $setting['version'], true);

        wp_enqueue_script("pplcz_plugin", plugin_dir_url(realpath(__DIR__ . '/../'))  . "Admin/MuiAdmin/build/static/js/bundle.js", [] , pplcz_get_version(), true);

        $newWoo = false;

        if (class_exists('Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController'))
        {
            $controller = wc_get_container()->get( \Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController::class );
            $newWoo = $controller->custom_orders_table_usage_is_enabled();
        }


        wp_localize_script("pplcz_plugin", "pplcz_data", [
            "nonce" => wp_create_nonce("wp_rest"),
            "url" => rtrim(get_rest_url(), '/'),
            "pluginPath" => plugin_dir_url(realpath(__DIR__ . '/../'))  . "Admin/MuiAdmin/build/static",
            "newCollectionUrl" => self::COLLECTIONURL,
            "ajax_url" => admin_url("admin-ajax.php"),
            "file_download_url" => FilePage::createUrl(null),
            "old_order_url" => !$newWoo
        ]);
        wp_add_inline_script("pplcz_plugin", "window.PPLczPlugin = window.PPLczPlugin || [];");
    }

}