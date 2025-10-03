<?php
// phpcs:ignoreFile WordPress.DB.DirectDatabaseQuery.DirectQuery
// phpcs:ignoreFile WordPress.DB.DirectDatabaseQuery.NoCaching

namespace PPLCZ\Data;

defined("WPINC") or die();


class ShipmentDataStore extends PPLDataStore implements ShipmentDataStoreInterface
{
    protected $meta_type = 'pplcz_shipments';


    protected $table_name = "shipment";

    protected $id_name = "ppl_shipment_id";

    public static function stores($stores)
    {
        return array_merge($stores, ["pplcz-shipment" =>  self::class]);
    }

    public static function register()
    {
        add_filter('woocommerce_data_stores', [self::class, "stores"]);
    }



    public function read_shipments($args = [])
    {
        global $wpdb;
        $where = [" 1 = 1 "];

        if (isset($args['wc_order_id']) && is_array($args['wc_order_id']) && $args["wc_order_id"])
        {
            $orders = array_map(function ($item) {
                return intval($item);
            }, $args["wc_order_id"]) ;

            $where[] = " wc_order_id in (" . join(', ', $orders). ") ";
        }

        if (isset($args['batch_label_group']) && is_array($args['batch_label_group']) && $args['batch_label_group'])
        {
            $batch_groups = array_map(function ($item) {
                global $wpdb;
                return $wpdb->prepare("%s", $item);
            }, $args["batch_label_group"]) ;

            $where[] = " batch_label_group in (" . join(', ', $batch_groups). ") ";
        }

        if (isset($args['batch_id']) && is_array($args['batch_label_group']) && $args['batch_label_group'])
        {
            $batch_groups = array_map(function ($item) {
                global $wpdb;
                return $wpdb->prepare("%s", $item);
            }, $args["batch_label_group"]) ;

            $where[] = " batch_id in (" . join(', ', $batch_groups). ") ";
        }
        $condition = ' where ' . join(" AND ", $where);
        $output = [];

        foreach ($wpdb->get_results( "SELECT * from {$wpdb->prefix}pplcz_shipment {$condition}", ARRAY_A) as $result)
        {
            wp_cache_add($result["ppl_shipment_id"], $result, "pplcz_shipment");
            $output[] = new ShipmentData($result["ppl_shipment_id"]);
        }

        return $output;
    }

    public function read_order_shipments($order_id)
    {
        global $wpdb;

        $output = [];

        foreach ($wpdb->get_results($wpdb->prepare("SELECT * from {$wpdb->prefix}pplcz_shipment WHERE wc_order_id = %d  ", $order_id), ARRAY_A) as $result)
        {
            wp_cache_add($result["ppl_shipment_id"], $result, "pplcz_shipment");
            $output[] = new ShipmentData($result["ppl_shipment_id"]);
        }

        return $output;
    }

    public function read_batch_shipments($batch_id)
    {
        global $wpdb;

        $output = [];

        foreach ($wpdb->get_results($wpdb->prepare("SELECT * from {$wpdb->prefix}pplcz_shipment WHERE batch_id = %s ", $batch_id), ARRAY_A) as $result)
        {
            wp_cache_add($result["ppl_shipment_id"], $result, "pplcz_shipment");
            $output[] = new ShipmentData($result["ppl_shipment_id"]);
        }

        return $output;
    }

    public function read_progress_shipments()
    {
        global $wpdb;

        $output = [];

        foreach ($wpdb->get_results( $wpdb->prepare("SELECT * from {$wpdb->prefix}pplcz_shipment WHERE batch_id is not null AND import_state =%s", 'InProgress'), ARRAY_A) as $result)
        {
            wp_cache_add($result["ppl_shipment_id"], $result, "pplcz_shipment");
            $output[] = new ShipmentData($result["ppl_shipment_id"]);
        }

        return $output;

    }

    public function find_shipments_by_wc_order($wc_order_id )
    {
        global $wpdb;

        $output = [];

        foreach ($wpdb->get_results($wpdb->prepare("SELECT * from {$wpdb->prefix}pplcz_shipment WHERE wc_order_id = %d  ", $wc_order_id), ARRAY_A) as $result)
        {
            wp_cache_add($result["ppl_shipment_id"], $result, "pplcz_shipment");
            $output[] = new ShipmentData($result["ppl_shipment_id"]);
        }

        return $output;
    }

    public function read_label_groups()
    {
        global $wpdb;

        $output = [];
        $dateTime = new \DateTime();
        $dateTime->sub(new \DateInterval("P2D"));

        $from = $dateTime->format("Y-m-d");

        foreach ($wpdb->get_results($wpdb->prepare("SELECT batch_label_group from {$wpdb->prefix}pplcz_shipment WHERE batch_label_group > %s group by batch_label_group order by batch_label_group desc", $from), ARRAY_A) as $item) {
            $output[] = $item["batch_label_group"];
        }

        return $output;
    }
}