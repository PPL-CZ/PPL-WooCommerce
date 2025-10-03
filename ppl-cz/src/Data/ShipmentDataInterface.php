<?php
namespace PPLCZ\Data;

interface ShipmentDataInterface
{
    public static function read_shipments($args = []);

    public static function read_order_shipments($order_id);

    public static function read_batch_shipments($batch_id);

    public static function read_label_groups();
}