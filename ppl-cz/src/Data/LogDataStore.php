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
               "select * from {$wpdb->prefix}pplcz_log order by ppl_log_id desc limit 100",
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
            "delete from {$wpdb->prefix}pplcz_log where ppl_log_id not in (select ppl_log_id from (select ppl_log_id from {$wpdb->prefix}pplcz_log order by ppl_log_id desc limit 30) as temp)"
        );

        add_option(pplcz_create_name("error_log"), 0, null, 'yes') || update_option(pplcz_create_name("error_log"), 0, 'yes');
        add_option(pplcz_create_name("error_log_hashes"), "", null, 'yes') || update_option(pplcz_create_name("error_log_hashes"), "", 'yes');
    }


    public function create(&$data)
    {
        /**
         * @var PPLData $data
         */
        global $wpdb;
        $insertData = $data->get_props_for_store("create");
        $prepare = $wpdb->prepare("insert ignore into {$wpdb->prefix}pplcz_{$this->table_name} (`timestamp`, `message`, `errorhash`) values (%s, %s, %s)", $insertData['timestamp'], $insertData['message'], $insertData['errorhash']);
        $wpdb->query($prepare);
        $id = $wpdb->insert_id;
        $data->set_id($id);
        $data->apply_changes();
        do_action("pplcz_{$this->table_name}_new", $id, $data);
    }

}