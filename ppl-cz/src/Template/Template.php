<?php
namespace PPLCZ\Template;

defined("WPINC") or die();

class Template {

    public static function wc_get_template( $template, $template_name, $args, $template_path, $default_path )
    {
        switch ($template_name) {
            case "ppl/parcelshops-map.php":
            case "ppl/parcelshop-shipping-address.php":
            case "ppl/admin/parcelshop-shipping-address.php":
            case "ppl/admin/order-filter.php":
            case "ppl/admin/order-panel.php":
            case "ppl/admin/order-table-column.php":
            case "ppl/method-name.php":
            case "ppl/select-parcelshop-label.php":
            case "ppl/select-parcelshop-inner.php":
                if(str_contains($template, WC()->plugin_path())) {
                    return __DIR__ . "/" . $template_name;
                }
                return $template;
            default:
                return $template;
        }
    }

    public static function register()
    {
        add_filter("wc_get_template", [self::class, "wc_get_template"], 10, 5);
    }
}