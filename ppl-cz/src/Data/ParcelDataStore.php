<?php
// phpcs:ignoreFile WordPress.DB.DirectDatabaseQuery.DirectQuery

namespace PPLCZ\Data;
defined("WPINC") or die();


class ParcelDataStore extends PPLDataStore {


    protected $id_name = "ppl_parcel_id";

    protected $table_name = "parcel";


    public static function stores($stores)
    {
        return array_merge($stores, ["pplcz-parcel" =>  self::class]);
    }

    public static function register()
    {
        add_filter('woocommerce_data_stores', [self::class, "stores"]);
    }

    public static function getAccessPointByCode($code)
    {
        global $wpdb;
        $output = [];
        foreach ($wpdb->get_results($wpdb->prepare("select * from {$wpdb->prefix}pplcz_parcel where code = %s and hidden = 0", $code), ARRAY_A) as $item)
        {
            wp_cache_add($item["ppl_parcel_id"], $item, "pplcz_parcel");
            $output[] = new ParcelData($item["ppl_parcel_id"]);
        }

        return reset($output);

    }
}