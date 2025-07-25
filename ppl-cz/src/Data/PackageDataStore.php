<?php
// phpcs:ignoreFile WordPress.DB.DirectDatabaseQuery.DirectQuery

namespace PPLCZ\Data;

defined("WPINC") or die();


class PackageDataStore extends PPLDataStore
{
    protected $id_name = "ppl_package_id";

    protected $table_name = "package";


    public static function stores($stores)
    {
        return array_merge($stores, ["pplcz-package" =>  self::class]);
    }

    public static function register()
    {
        add_filter('woocommerce_data_stores', [self::class, "stores"]);
    }

    public static function find_packages_by_shipment($ppl_shipment_id)
    {
        global $wpdb;
        $output = [];
        foreach (
            $wpdb->get_results(
                $wpdb->prepare("select * from {$wpdb->prefix}pplcz_package where ppl_shipment_id = %d", $ppl_shipment_id),
                ARRAY_A) as $item
        ) {
            wp_cache_add($item["ppl_package_id"], $item, "pplcz_package");
            $output[] = new PackageData($item["ppl_package_id"]);
        }
        return $output;
    }

    public static function find_package_by_shipment_number($shipmentNumbers)
    {
        global $wpdb;
        if (!$shipmentNumbers)
            return [];

        foreach ($shipmentNumbers as $key=>$value) {
            $shipmentNumbers[$key] = $wpdb->prepare("%s", $value);
        }
        $shipmentNumbers = join(',', $shipmentNumbers);

        foreach ($wpdb->get_results("select * from {$wpdb->prefix}pplcz_package where shipment_number in ($shipmentNumbers)", ARRAY_A) as $item) {
            wp_cache_add($item["ppl_package_id"], $item, "pplcz_package");
            $output[] = new PackageData($item["ppl_package_id"]);
        }
        return $output;
    }

    public static function find_packages_by_phases_and_time($phases, $from_last_test_phase, $last_change_phase, $limit)
    {
        global $wpdb;

        $phases_safe = join(',',array_map(function ($phase) use ($wpdb) {
            return "%s";
        }, $phases));


        if (!$phases)
            return [];

        $args = array_merge($phases, [$last_change_phase, $from_last_test_phase], [$limit]);
        $query = "select * from {$wpdb->prefix}pplcz_package where phase in ($phases_safe) 
and (last_update_phase >= %s or last_update_phase is null) 
and shipment_number is not null and shipment_number <> '' 
and (last_test_phase < %s or last_test_phase is null)
order by last_test_phase asc
limit %d";
              $sql = $wpdb->prepare($query, ...$args);

       $output = [];

       foreach ($wpdb->get_results($sql, ARRAY_A) as $result) {
           wp_cache_add($result["ppl_package_id"], $result, "pplcz_package");
           $output[] = new PackageData($result["ppl_package_id"]);
       }
       return $output;
    }
}