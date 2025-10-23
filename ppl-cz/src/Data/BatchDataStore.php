<?php

namespace PPLCZ\Data;

use Automattic\WooCommerce\Admin\API\Reports\DataStoreInterface;

class BatchDataStore extends PPLDataStore
{
    protected $id_name = "ppl_batch_id";

    protected $table_name = "batch";

    public static function stores($stores)
    {
        return array_merge($stores, ["pplcz-batch" => self::class]);
    }

    public static function register()
    {
        add_filter('woocommerce_data_stores', [self::class, "stores"]);
    }

    public function remove_batchs()
    {
        global $wpdb;

        $wpdb->query("delete from {$wpdb->prefix}pplcz_batch where `lock` = 0 and ppl_batch_id not in (select batch_local_id from {$wpdb->prefix}pplcz_shipment where batch_id is not null) and created_at < now() - INTERVAL 2 DAY");
    }

    public function get_batchs($free)
    {
        global $wpdb;

        if ($free)
        {
            $rows = $wpdb->get_results("select * from {$wpdb->prefix}pplcz_batch where `lock` = 0 and ppl_batch_id  in (select batch_local_id from {$wpdb->prefix}pplcz_shipment)  order by ppl_batch_id desc", ARRAY_A);
        }
        else
        {
            $rows = $wpdb->get_results("select * from {$wpdb->prefix}pplcz_batch where ppl_batch_id  in (select batch_local_id from {$wpdb->prefix}pplcz_shipment) and created_at > now() - INTERVAL 5 DAY  order by ppl_batch_id desc", ARRAY_A);
            if (count($rows) < 20) {
                $ids = array_map(function ($item){
                    return $item['ppl_batch_id'];
                }, $rows);
                foreach ($wpdb->get_results("select * from {$wpdb->prefix}pplcz_batch where ppl_batch_id  in (select batch_local_id from {$wpdb->prefix}pplcz_shipment)  order by ppl_batch_id desc limit 20", ARRAY_A) as $row) {
                    if (count($rows) < 20) {
                        if (!in_array($row['ppl_batch_id'], $ids, true))
                            $rows[] = $row;
                        continue;
                    }
                    break;
                }
            }
        }

        usort($rows, function ($a, $b) {
            return -($a["ppl_batch_id"] - $b["ppl_batch_id"]);
        });

        $output = [];

        foreach ($rows as $row) {
            wp_cache_add($row["ppl_batch_id"], $row, "batch");
            $output[] = new BatchData($row["ppl_batch_id"]);
        }
        return $output;
    }

    public function get_last_batch()
    {
        global $wpdb;
        $rows = $wpdb->get_results("select * from {$wpdb->prefix}pplcz_batch where `lock` = 0 order by ppl_batch_id desc limit 1 ", ARRAY_A);
        foreach ($rows as $row) {
            wp_cache_add($row["ppl_batch_id"], $row, "batch");
            return new BatchData($row["ppl_batch_id"]);
        }
        return null;
    }
}