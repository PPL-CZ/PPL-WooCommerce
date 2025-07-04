<?php
// phpcs:ignoreFile WordPress.DB.DirectDatabaseQuery.DirectQuery

namespace PPLCZ\Data;

defined("WPINC") or die();


class LogDataStore extends PPLDataStore
{
    protected $id_name = "ppl_log_id";

    protected $table_name = "log";


    public static function stores($stores)
    {
        return array_merge($stores, ["pplcz-log" =>  self::class]);
    }

    public static function register()
    {
        add_filter('woocommerce_data_stores', [self::class, "stores"]);
    }

    public static function get_logs()
    {
        global $wpdb;
        $output = [];
        foreach (
            $wpdb->get_results(
                $wpdb->prepare("select * from {$wpdb->prefix}pplcz_log order by ppl_log_id desc limit 30"),
                ARRAY_A) as $item
        ) {
            wp_cache_add($item["ppl_log_id"], $item, "pplcz_log");
            $output[] = new LogData($item["ppl_log_id"]);
        }
        return $output;
    }

    public static function clear_logs()
    {
        global $wpdb;
        $wpdb->query(
            $wpdb->prepare("delete from {$wpdb->prefix}pplcz_log where ppl_log_id not in (select ppl_log_id from (select ppl_log_id from {$wpdb->prefix}pplcz_log order by ppl_log_id desc limit 100) as temp)"),
        );
        return;
    }

}